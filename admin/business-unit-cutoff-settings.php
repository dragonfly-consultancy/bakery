<?php
ob_start();
error_reporting(E_ALL ^ E_NOTICE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('include/database.php');
include('include/check_login.php');
include('include/business_unit_cutoff.php');

requirePermission('settings.permissions');

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$message = '';
$messageClass = '';
$cutoffRows = [];

try {
    $db = new Database();
    ensureBusinessUnitCutoffSettingsTable($db);
    $cutoffRows = getBusinessUnitCutoffSettings($db);
} catch (Exception $e) {
    $db = null;
    $message = 'Database connection error: ' . $e->getMessage();
    $messageClass = 'alert-danger';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cutoffs']) && $db) {
    $standingOrderCutoffs = isset($_POST['standing_order_cutoff']) && is_array($_POST['standing_order_cutoff']) ? $_POST['standing_order_cutoff'] : [];
    $lateOrderCutoffs = isset($_POST['late_order_cutoff']) && is_array($_POST['late_order_cutoff']) ? $_POST['late_order_cutoff'] : [];
    $cutoffPeriods = isset($_POST['cutoff_period']) && is_array($_POST['cutoff_period']) ? $_POST['cutoff_period'] : [];

    $errors = [];
    $payload = [];

    foreach ($cutoffRows as $index => $row) {
        $businessUnitId = (int) ($row['business_unit_id'] ?? 0);
        $businessUnitName = trim((string) ($row['business_unit_name'] ?? 'Business Unit'));

        $standingOrderValue = normalizeBusinessUnitCutoffTime($standingOrderCutoffs[$businessUnitId] ?? '');
        if ($standingOrderValue === false) {
            $errors[] = $businessUnitName . ': invalid standing order cutoff time.';
        }

        $lateOrderValue = normalizeBusinessUnitCutoffTime($lateOrderCutoffs[$businessUnitId] ?? '');
        if ($lateOrderValue === false) {
            $errors[] = $businessUnitName . ': invalid late order cutoff time.';
        }

        $cutoffPeriodRaw = trim((string) ($cutoffPeriods[$businessUnitId] ?? ''));
        if ($cutoffPeriodRaw === '') {
            $cutoffPeriodValue = $index + 1;
        } elseif (!ctype_digit($cutoffPeriodRaw) || (int) $cutoffPeriodRaw < 1) {
            $errors[] = $businessUnitName . ': cutoff period must be a whole number greater than zero.';
            $cutoffPeriodValue = $index + 1;
        } else {
            $cutoffPeriodValue = (int) $cutoffPeriodRaw;
        }

        $payload[] = [
            'business_unit_id' => $businessUnitId,
            'standing_order_cutoff_time' => $standingOrderValue,
            'late_order_cutoff_time' => $lateOrderValue,
            'cutoff_period' => $cutoffPeriodValue,
        ];
    }

    if (empty($errors)) {
        $pdo = $db->getConnection();

        try {
            $pdo->beginTransaction();

            foreach ($payload as $row) {
                $db->insertRow(
                    'INSERT INTO business_unit_cutoff_settings (business_unit_id, standing_order_cutoff_time, late_order_cutoff_time, cutoff_period)
                     VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                        standing_order_cutoff_time = VALUES(standing_order_cutoff_time),
                        late_order_cutoff_time = VALUES(late_order_cutoff_time),
                        cutoff_period = VALUES(cutoff_period),
                        updated_at = CURRENT_TIMESTAMP',
                    [
                        $row['business_unit_id'],
                        $row['standing_order_cutoff_time'],
                        $row['late_order_cutoff_time'],
                        $row['cutoff_period'],
                    ]
                );
            }

            $pdo->commit();
            $message = 'Business unit cutoff settings saved successfully.';
            $messageClass = 'alert-success';
            $cutoffRows = getBusinessUnitCutoffSettings($db);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = 'Unable to save business unit cutoff settings: ' . $e->getMessage();
            $messageClass = 'alert-danger';
        }
    } else {
        $message = implode("\n", $errors);
        $messageClass = 'alert-warning';

        foreach ($cutoffRows as $index => &$row) {
            $businessUnitId = (int) ($row['business_unit_id'] ?? 0);
            $row['standing_order_cutoff_time'] = normalizeBusinessUnitCutoffTime($standingOrderCutoffs[$businessUnitId] ?? '') ?: '';
            $row['late_order_cutoff_time'] = normalizeBusinessUnitCutoffTime($lateOrderCutoffs[$businessUnitId] ?? '') ?: '';

            $cutoffPeriodRaw = trim((string) ($cutoffPeriods[$businessUnitId] ?? ''));
            $row['cutoff_period'] = ctype_digit($cutoffPeriodRaw) && (int) $cutoffPeriodRaw > 0 ? (int) $cutoffPeriodRaw : $index + 1;
        }
        unset($row);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Business Unit Cutoff Settings</title>
    <?php include('common/head.php'); ?>
    <style>
        .section-card {
            padding: 18px;
            margin-bottom: 18px;
            background: #fff;
            border: 1px solid #e4eaf1;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(32, 51, 74, 0.05);
        }

        .page-note {
            margin-top: 8px;
            color: #6b7785;
        }

        .cutoff-table th {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.04em;
        }

        .cutoff-table td {
            vertical-align: middle;
        }

        .cutoff-table .form-control {
            min-width: 130px;
        }

        .cutoff-table .form-control[type="number"] {
            min-width: 90px;
        }

        .settings-help {
            color: #6b7785;
            margin-top: 12px;
        }
    </style>
</head>
<body class="page-sidebar-closed-hide-logo page-content-white">
    <?php include('common/manubar.php'); ?>
    <div class="clearfix"></div>
    <div class="page-container">
        <?php include('common/sidebar.php'); ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li><a href="index.php">Home</a><i class="fa fa-circle"></i></li>
                        <li><a href="manage-settings.php">Settings</a><i class="fa fa-circle"></i></li>
                        <li><span>Business Unit Cutoff Settings</span></li>
                    </ul>
                </div>

                <h3 class="page-title"><i class="fa fa-clock-o"></i> Business Unit Cutoff Settings</h3>
                <p class="page-note">Store the standing-order cutoff, late-order cutoff, and cutoff period for each business unit.</p>

                <?php if ($message !== ''): ?>
                    <div class="alert <?php echo h($messageClass ?: 'alert-info'); ?> alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <?php echo nl2br(h($message)); ?>
                    </div>
                <?php endif; ?>

                <div class="section-card">
                    <?php if (empty($cutoffRows)): ?>
                        <p class="text-muted">No business units were found. Add business units first, then return to this page.</p>
                    <?php else: ?>
                        <form method="post">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover cutoff-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 24%;">Business Unit</th>
                                            <th style="width: 26%;">Standing Order Cutoff Time</th>
                                            <th style="width: 26%;">Late Order Cutoff Time</th>
                                            <th style="width: 14%;">Cutoff Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cutoffRows as $index => $row): ?>
                                            <?php
                                            $businessUnitId = (int) ($row['business_unit_id'] ?? 0);
                                            $cutoffPeriodValue = isset($row['cutoff_period']) && (int) $row['cutoff_period'] > 0 ? (int) $row['cutoff_period'] : $index + 1;
                                            ?>
                                            <tr>
                                                <td><strong><?php echo h($row['business_unit_name'] ?? ''); ?></strong></td>
                                                <td>
                                                    <input
                                                        type="time"
                                                        class="form-control"
                                                        name="standing_order_cutoff[<?php echo $businessUnitId; ?>]"
                                                        value="<?php echo h(formatBusinessUnitCutoffInputTime($row['standing_order_cutoff_time'] ?? '')); ?>"
                                                    >
                                                </td>
                                                <td>
                                                    <input
                                                        type="time"
                                                        class="form-control"
                                                        name="late_order_cutoff[<?php echo $businessUnitId; ?>]"
                                                        value="<?php echo h(formatBusinessUnitCutoffInputTime($row['late_order_cutoff_time'] ?? '')); ?>"
                                                    >
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        class="form-control"
                                                        name="cutoff_period[<?php echo $businessUnitId; ?>]"
                                                        value="<?php echo h($cutoffPeriodValue); ?>"
                                                        min="1"
                                                        step="1"
                                                    >
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <p class="settings-help">Times are stored per business unit. The standing-order page now reads the standard cutoff time from these settings.</p>

                            <div>
                                <button class="btn btn-primary" type="submit" name="save_cutoffs" value="1">
                                    <i class="fa fa-save"></i> Save Cutoff Settings
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
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
    <script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
</body>
</html>