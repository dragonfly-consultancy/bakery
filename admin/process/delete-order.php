<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
session_start();
include('../include/database.php');
include('../include/check_login.php');
include_once('../include/order_soft_delete.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$csrfToken = isset($input['csrf_token']) ? (string)$input['csrf_token'] : '';
$invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id']    : 0;
$deleteReason = trim((string)($input['delete_reason'] ?? ''));

// Validate CSRF
if (empty($_SESSION['delete_order_csrf']) || !hash_equals($_SESSION['delete_order_csrf'], $csrfToken)) {
    echo json_encode(['status' => false, 'message' => 'Security token mismatch']);
    exit;
}

if ($invoiceId <= 0) {
    echo json_encode(['status' => false, 'message' => 'Invalid order ID']);
    exit;
}

if ($deleteReason === '') {
    echo json_encode(['status' => false, 'message' => 'Delete reason is required']);
    exit;
}

if (strlen($deleteReason) > 500) {
    echo json_encode(['status' => false, 'message' => 'Delete reason must be 500 characters or fewer']);
    exit;
}

try {
    $db = new Database();
    ensureInvoiceOrderSoftDeleteColumns($db);

    if (!isSuperAdmin()) {
        echo json_encode(['status' => false, 'message' => 'Only super admin can delete orders']);
        exit;
    }

    $order = $db->getRow(
        'SELECT invoice_h_id, invoice_h_status, invoice_h_order_note, invoice_h_delivery_date, is_deleted FROM invoice_hedder WHERE invoice_h_id = ?',
        [$invoiceId]
    );

    if (!$order) {
        echo json_encode(['status' => false, 'message' => 'Order not found']);
        exit;
    }

    $blockReason = getOrderDeleteBlockReason($order, date('Y-m-d'));
    if ($blockReason !== '') {
        echo json_encode(['status' => false, 'message' => $blockReason]);
        exit;
    }

    $deletedBy = trim((string)($_SESSION['username'] ?? ''));
    if ($deletedBy === '') {
        $deletedBy = isset($_SESSION['userid']) ? (string)$_SESSION['userid'] : 'System';
    }

    $db->updateRow(
        'UPDATE invoice_hedder SET is_deleted = 1, deleted_at = NOW(), deleted_by = ?, delete_reason = ?, invoice_h_status = -1 WHERE invoice_h_id = ?',
        [$deletedBy, $deleteReason, $invoiceId]
    );

    echo json_encode(['status' => true, 'message' => 'Order deleted successfully']);

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;
