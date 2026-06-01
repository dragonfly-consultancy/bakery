<?php
ob_start();
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../include/database.php');
include('../include/check_login.php');

header('Content-Type: application/json; charset=utf-8');

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function getCurrencySymbol(Database $db)
{
    $currency = $db->getRow('SELECT currency FROM currency WHERE activated = ? LIMIT 1', ['Y']);
    return isset($currency['currency']) ? $currency['currency'] : '';
}

$db = new Database();
$conn = $db->getConnection();

$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

if ($start < 0) {
    $start = 0;
}

if ($length <= 0 || $length > 200) {
    $length = 10;
}

$orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 1;
$orderDirInput = isset($_POST['order'][0]['dir']) ? strtolower($_POST['order'][0]['dir']) : 'desc';
$orderDir = $orderDirInput === 'asc' ? 'ASC' : 'DESC';

$columnMap = [
    1 => 'ih.invoice_h_code',
    2 => 'c.customer_name',
    3 => 'ih.invoice_h_datetime',
    4 => 'ih.invoice_h_gross_value'
];
$orderBy = isset($columnMap[$orderColumnIndex]) ? $columnMap[$orderColumnIndex] : 'ih.invoice_h_id';

$isSuper = function_exists('isSuperAdmin') ? isSuperAdmin() : false;
$where = [];
$params = [];

if (!$isSuper) {
    $where[] = 'ih.invoice_h_location = ?';
    $params[] = isset($_SESSION['location']) ? $_SESSION['location'] : 0;
}

$baseWhereSql = '';
if (!empty($where)) {
    $baseWhereSql = ' WHERE ' . implode(' AND ', $where);
}

$searchWhereSql = $baseWhereSql;
$searchParams = $params;
if ($searchValue !== '') {
    $searchClause = '(ih.invoice_h_code LIKE ? OR c.customer_name LIKE ? OR ih.invoice_h_datetime LIKE ?)';
    $like = '%' . $searchValue . '%';

    if ($searchWhereSql === '') {
        $searchWhereSql = ' WHERE ' . $searchClause;
    } else {
        $searchWhereSql .= ' AND ' . $searchClause;
    }

    $searchParams[] = $like;
    $searchParams[] = $like;
    $searchParams[] = $like;
}

$totalCountSql = 'SELECT COUNT(*) AS total
                  FROM invoice_hedder ih
                  LEFT JOIN customer c ON c.customer_id = ih.invoice_h_customer_id' . $baseWhereSql;
$totalStmt = $conn->prepare($totalCountSql);
$totalStmt->execute($params);
$recordsTotal = (int)$totalStmt->fetchColumn();

$filteredCountSql = 'SELECT COUNT(*) AS total
                     FROM invoice_hedder ih
                     LEFT JOIN customer c ON c.customer_id = ih.invoice_h_customer_id' . $searchWhereSql;
$filteredStmt = $conn->prepare($filteredCountSql);
$filteredStmt->execute($searchParams);
$recordsFiltered = (int)$filteredStmt->fetchColumn();

$dataSql = 'SELECT ih.invoice_h_id, ih.invoice_h_code, ih.invoice_h_datetime,
                   ih.invoice_h_gross_value, ih.invoice_h_net_value, ih.invoice_h_status,
                   ih.delivery_status, c.customer_name, c.account_hold, c.locked,
                   COALESCE(cb.customer_amount, 0) AS customer_amount
            FROM invoice_hedder ih
            LEFT JOIN customer c ON c.customer_id = ih.invoice_h_customer_id
            LEFT JOIN (
                SELECT invoice_h_id, SUM(amount) AS customer_amount
                FROM customer_balance
                GROUP BY invoice_h_id
            ) cb ON cb.invoice_h_id = ih.invoice_h_id'
            . $searchWhereSql .
            " ORDER BY {$orderBy} {$orderDir}, ih.invoice_h_id DESC LIMIT {$start}, {$length}";

$dataStmt = $conn->prepare($dataSql);
$dataStmt->execute($searchParams);
$rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

$currencySymbol = getCurrencySymbol($db);
$resultData = [];

foreach ($rows as $invoice) {
    $invoiceId = (int)$invoice['invoice_h_id'];
    $invoiceStatus = (int)$invoice['invoice_h_status'];
    $netValue = (float)$invoice['invoice_h_net_value'];
    $amount = (float)$invoice['customer_amount'];

    $style = 'lbl_Payment_status_pending';
    $status = 'Pending';
    if ($netValue == $amount || $amount > $netValue) {
        $style = 'lbl_Payment_status_paid';
        $status = 'Paid';
    } elseif ($netValue > $amount && $amount != 0) {
        $style = 'lbl_Payment_status_partial';
        $status = 'Partial';
    }

    $deliveryStatus = isset($invoice['delivery_status']) ? $invoice['delivery_status'] : 'PENDING';
    if ($deliveryStatus === 'DELIVERED') {
        $deliveryBadge = '<span class="label label-success">Delivered</span>';
        $deliveryAction = '<a href="invoice-delivery.php?id=' . $invoiceId . '"><div class="btn-group btn-group-xs btn-group-solid"><button type="button" class="btn green-jungle btn-outline"><i class="fa fa-eye"></i> View Batches</button></div></a>';
    } else {
        $deliveryBadge = '<span class="label label-warning">Pending</span>';
        $deliveryAction = '<a href="invoice-delivery.php?id=' . $invoiceId . '"><div class="btn-group btn-group-xs btn-group-solid"><button type="button" class="btn green btn-outline"><i class="fa fa-truck"></i> Mark Delivered</button></div></a>';
    }

    $actions = '<div style="text-align:center">'
        . '<a href="invoice.php?id=' . $invoiceId . '"><div class="btn-group btn-group-xs btn-group-solid"><button type="button" class="btn dark btn-outline sbold ">invoice</button></div></a>';

    if ($invoiceStatus === 1) {
        $actions .= '<a href="receipt.php?id=' . $invoiceId . '"><div class="btn-group btn-group-xs btn-group-solid"><button type="button" class="btn blue btn-outline">Receipt</button></div></a>';
    }

    $actions .= $deliveryAction . '</div>';

    $resultData[] = [
        '',
        h($invoice['invoice_h_code']),
        h($invoice['customer_name']),
        h($invoice['invoice_h_datetime']),
        h($currencySymbol) . ' ' . number_format((float)$invoice['invoice_h_gross_value'], 2),
        '<span class="' . h($style) . '">' . h($status) . '</span>',
        $deliveryBadge,
        $actions
    ];
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $resultData
]);
exit;
