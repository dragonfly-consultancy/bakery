<?php
ob_start();
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('include/database.php');
include('include/check_login.php');
requirePermission('settings.permissions');
$db = new Database();

// Ensure the customer_product_discount table exists
$db->insertRow("CREATE TABLE IF NOT EXISTS `customer_product_discount` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `discount_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;", []);

// ── Resolve alert message ─────────────────────────────────────────────────────
$alertType    = '';
$alertMessage = '';
if (isset($_GET['success'])) {
    $alertType = 'success';
    switch ($_GET['success']) {
        case 'created': $alertMessage = 'Customer Product Discount created successfully.'; break;
        case 'updated': $alertMessage = 'Customer Product Discount updated successfully.'; break;
        case 'deleted': $alertMessage = 'Customer Product Discount deleted successfully.'; break;
        default:        $alertMessage = 'Operation completed successfully.'; break;
    }
}
if (isset($_GET['error'])) {
    $alertType = 'danger';
    switch ($_GET['error']) {
        case 'missing_fields':  $alertMessage = 'All fields are required.'; break;
        case 'duplicate':       $alertMessage = 'This discount already exists for this customer and product.'; break;
        case 'invalid_id':      $alertMessage = 'Invalid record ID.'; break;
        default:                $alertMessage = 'An error occurred. Please try again.'; break;
    }
}

// ── Fetch all discounts ───────────────────────────────────────────────────────
$discounts = $db->getRows('SELECT cpd.*, c.customer_name, p.item_name FROM customer_product_discount cpd JOIN customer c ON cpd.customer_id = c.customer_id JOIN item_master p ON cpd.product_id = p.item_id ORDER BY c.customer_name, p.item_name');

// ── Fetch single record for edit modal ────────────────────────────────────────
$editRecord = null;
if (isset($_GET['editID']) && intval($_GET['editID']) > 0) {
    $editRecord = $db->getRow('SELECT * FROM customer_product_discount WHERE id = ?', [intval($_GET['editID'])]);
}
// ── Fetch customers and products for dropdowns ────────────────────────────────
$customers = $db->getRows('SELECT customer_id, customer_name FROM customer WHERE customer_name IS NOT NULL AND customer_name <> "" ORDER BY customer_name ASC');
if (empty($customers)) {
    $customers = $db->getRows('SELECT customer_id, customer_name FROM customer ORDER BY customer_name ASC');
}
$products = $db->getRows('SELECT item_id, item_name FROM item_master WHERE item_name IS NOT NULL AND item_name <> "" ORDER BY item_name ASC');
if (empty($products)) {
    $products = $db->getRows('SELECT item_id, item_name FROM item_master ORDER BY item_name ASC');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Customer Product Discount</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <?php include('common/head.php'); ?>
    <link href="assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
</head>
<body class="page-sidebar-closed-hide-logo page-content-white">
<?php include('common/manubar.php'); ?>
<div class="clearfix"></div>
<div class="page-container">
    <div class="page-sidebar-wrapper">
        <?php include('common/sidebar.php'); ?>
    </div>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li><a href="index.php">Home</a><i class="fa fa-circle"></i></li>
                    <li><a href="#">Settings</a><i class="fa fa-circle"></i></li>
                    <li><span>Customer Product Discount</span></li>
                </ul>
                <div class="page-toolbar">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn green btn-sm" data-toggle="modal" data-target="#addCPDModal">
                            <i class="fa fa-plus"></i> Add New Discount
                        </button>
                    </div>
                </div>
            </div>
            <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                <?php echo htmlspecialchars($alertMessage); ?>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-green">
                                <i class="fa fa-percent font-green"></i>
                                <span class="caption-subject bold uppercase">Customer Product Discount List</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="cpd_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Discount (%)</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($discounts): ?>
                                        <?php foreach ($discounts as $row): ?>
                                        <tr>
                                            <td><?php echo (int)$row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                            <td><?php echo number_format((float)$row['discount_percentage'], 2); ?></td>
                                            <td><?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="customer_product_discount.php?editID=<?php echo $row['id']; ?>" class="btn btn-xs btn-default" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
                                                    <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="<?php echo $row['id']; ?>" data-label="<?php echo htmlspecialchars($row['customer_name'] . ' - ' . $row['item_name'], ENT_QUOTES); ?>" title="Delete"><i class="fa fa-trash"></i> Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center text-muted">No discounts found. Click "Add New Discount" to get started.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end page-content -->
    </div><!-- end page-content-wrapper -->
</div><!-- end page-container -->
<!-- ADD MODAL -->
<div class="modal fade" id="addCPDModal" tabindex="-1" role="dialog" aria-labelledby="addCPDModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="process/customer_product_discount_process.php" method="POST" id="addCPDForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addCPDModalLabel"><i class="fa fa-plus"></i> Add New Discount</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer <span class="required">*</span></label>
                        <input type="text" id="add_customer_search" class="form-control" placeholder="Type to search customer">
                    </div>
                    <div class="form-group">
                        <select name="customer_id" id="add_customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?php echo $c['customer_id']; ?>"><?php echo htmlspecialchars($c['customer_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product <span class="required">*</span></label>
                        <input type="text" id="add_product_search" class="form-control" placeholder="Type to search product">
                    </div>
                    <div class="form-group">
                        <select name="product_id" id="add_product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['item_id']; ?>"><?php echo htmlspecialchars($p['item_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Discount Percentage <span class="required">*</span></label>
                        <input type="number" name="discount_percentage" class="form-control" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Active <span class="required">*</span></label>
                        <select name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT MODAL -->
<div class="modal fade" id="editCPDModal" tabindex="-1" role="dialog" aria-labelledby="editCPDModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="process/customer_product_discount_process.php" method="POST" id="editCPDForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="editCPDModalLabel"><i class="fa fa-pencil"></i> Edit Discount</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer <span class="required">*</span></label>
                        <input type="text" id="edit_customer_search" class="form-control" placeholder="Type to search customer">
                    </div>
                    <div class="form-group">
                        <select name="customer_id" id="edit_customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?php echo $c['customer_id']; ?>"><?php echo htmlspecialchars($c['customer_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product <span class="required">*</span></label>
                        <input type="text" id="edit_product_search" class="form-control" placeholder="Type to search product">
                    </div>
                    <div class="form-group">
                        <select name="product_id" id="edit_product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['item_id']; ?>"><?php echo htmlspecialchars($p['item_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Discount Percentage <span class="required">*</span></label>
                        <input type="number" name="discount_percentage" id="edit_discount_percentage" class="form-control" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Active <span class="required">*</span></label>
                        <select name="is_active" id="edit_is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- DELETE MODAL -->
<div class="modal fade" id="deleteCPDModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="process/customer_product_discount_process.php" method="POST" id="deleteCPDForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-trash text-danger"></i> Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this discount for <strong id="delete_label"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include('common/footer.php'); ?>
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="assets/pages/scripts/table-datatables-responsive.min.js" type="text/javascript"></script>
<script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
<script src="assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
<script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script>
$(document).ready(function () {
    function attachSelectFilter(searchInputSelector, selectSelector) {
        var $search = $(searchInputSelector);
        var $select = $(selectSelector);

        if (!$select.data('allOptions')) {
            $select.data('allOptions', $select.find('option').clone());
        }

        function filterOptions() {
            var term = ($search.val() || '').toLowerCase();
            var selectedValue = $select.val();
            var $allOptions = $select.data('allOptions');
            var $filtered = $allOptions.filter(function () {
                var text = ($(this).text() || '').toLowerCase();
                var value = ($(this).val() || '').toLowerCase();
                return term === '' || text.indexOf(term) !== -1 || value.indexOf(term) !== -1;
            }).clone();

            $select.empty().append($filtered);
            if (selectedValue && $select.find('option[value="' + selectedValue + '"]').length) {
                $select.val(selectedValue);
            } else {
                $select.val('');
            }
        }

        $search.off('input.cpdFilter').on('input.cpdFilter', filterOptions);
    }

    attachSelectFilter('#add_customer_search', '#add_customer_id');
    attachSelectFilter('#add_product_search', '#add_product_id');
    attachSelectFilter('#edit_customer_search', '#edit_customer_id');
    attachSelectFilter('#edit_product_search', '#edit_product_id');

    $('#cpd_table').DataTable({
        responsive: true,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: [5] }]
    });
    <?php if ($editRecord): ?>
    $('#edit_id').val('<?php echo (int)$editRecord['id']; ?>');
    $('#edit_customer_id').val('<?php echo (int)$editRecord['customer_id']; ?>');
    $('#edit_product_id').val('<?php echo (int)$editRecord['product_id']; ?>');
    $('#edit_discount_percentage').val('<?php echo (float)$editRecord['discount_percentage']; ?>');
    $('#edit_is_active').val('<?php echo (int)$editRecord['is_active']; ?>');
    $('#editCPDModal').modal('show');
    <?php endif; ?>
    $(document).on('click', '.btn-delete', function () {
        var id   = $(this).data('id');
        var label = $(this).data('label');
        $('#delete_id').val(id);
        $('#delete_label').text(label);
        $('#deleteCPDModal').modal('show');
    });
});
</script>
</body>
</html>
