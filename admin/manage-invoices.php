<?php 
ob_start();
error_reporting (E_ALL ^ E_NOTICE);
session_start();
include('include/database.php');
include('include/check_login.php');
include('get_url.php');


?>
<?php
?>
<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->


<head>
        <meta charset="utf-8" />
        <title>Manage Orders | STOCK MANAGEMENT SYSTEM</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <?php include('common/head.php'); ?>
         <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
       </head>
    <!-- END HEAD -->

    <body class="page-sidebar-closed-hide-logo page-content-white">
      <?php include('common/manubar.php'); ?>
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
             <div class="page-sidebar-wrapper">
           <?php include('common/sidebar.php'); ?>
            
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
          
                    <!-- BEGIN PAGE BAR -->
                    <div class="page-bar">
                        <ul class="page-breadcrumb">
                            <li>
                                <a>Home</a>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <a>Order Process</a>
                                <i class="fa fa-circle"></i>
                               
                            </li>
                            <li>
                                <a>Manage Invoices</a>
                               
                            </li>
                           
                        </ul>
                      
                    </div>
                    <!-- END PAGE BAR -->
                    <div class="alert <?php echo $MessageClass; ?> alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $CompanyMessage; ?>
                                    </div>
                    <!-- BEGIN PAGE TITLE-->
                    <h3 class="page-title">Manage Invoices
                      
                    </h3>
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                  
                    <div class="row">
                        <div class="col-md-12">
<div class="portlet light bordered form-fit ">
                                         
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                   
                                    <div class="tools"> </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="all">Invoice No.</th>
                                                <th class="all">Customer Name</th>
                                                <th class="all">Invoice Date</th>
                                                <th class="all">Grand Total</th>
                                                <th class="all">Payment Status</th>
                                                <th class="all">Delivery Status</th>
                                                <th class="all">Action</th>
                                                
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                        </tbody>                                   
                                    </table>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>
                        
                    </div>
                  
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
         
        </div>
        <!-- END CONTAINER -->
     <?php include('common/footer.php');?>
        <!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->


</body>
<script>
    jQuery(document).ready(function () {
        var table = jQuery('#sample_2');

        if (jQuery.fn.dataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

        table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'process/manage-invoices-datatable.php',
                type: 'POST'
            },
            language: {
                aria: {
                    sortAscending: ': activate to sort column ascending',
                    sortDescending: ': activate to sort column descending'
                },
                emptyTable: 'No data available in table',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries found',
                infoFiltered: '(filtered from _MAX_ total entries)',
                lengthMenu: '_MENU_ entries',
                search: 'Search:',
                zeroRecords: 'No matching records found'
            },
            buttons: [
                { extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'pdf', className: 'btn green btn-outline' },
                { extend: 'csv', className: 'btn purple btn-outline' }
            ],
            responsive: { details: { type: 'column', target: 'tr' } },
            columnDefs: [
                { className: 'control', orderable: false, targets: 0 },
                { orderable: false, targets: [0, 5, 6, 7] }
            ],
            order: [[1, 'desc']],
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            pageLength: 10,
            pagingType: 'bootstrap_extended',
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
        });
    });

    function myFunction(objId) {
        var invoice_h_id = (objId.value);
        $("body").append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Order Status</h4></div><form method="POST" action="process/order-status-process.php"><div class="modal-body"><div class="row"><div class="col-md-12"><div><small> Note:- you can not change after the submit your order status. if you have problem regarding this order please contact Administor.</small></div><div class="row" style="margin-top:10px;"><div class="col-xs-6"><div class="form-group"><label for="reference_no">Select Order Status</label>  <select name="status" class="form-control" ><option value="1">Acepct Order </option><option value="-1">Cancel Order </option></select></div></div></div></div></div><input type="hidden"  class="form-control" name="invoiceId" value="'+ invoice_h_id +'"><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="submit" name="sub" class="btn btn-primary">Submit Status</button></div></form></div></div></div>');


    }


</script>



