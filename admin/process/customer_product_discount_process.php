<?php
// admin/process/customer_product_discount_process.php
include '../include/database.php';

$db = new Database();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function redirectWith($params) {
    $url = '../customer_product_discount.php?'.http_build_query($params);
    header('Location: ' . $url);
    exit;
}

switch ($action) {
    case 'add':
        $customer_id = intval($_POST['customer_id']);
        $product_id = intval($_POST['product_id']);
        $discount_percentage = floatval($_POST['discount_percentage']);
        $is_active = intval($_POST['is_active']);
        if (!$customer_id || !$product_id) {
            redirectWith(['error'=>'missing_fields']);
        }
        $exists = $db->getRow('SELECT id FROM customer_product_discount WHERE customer_id=? AND product_id=?', [$customer_id, $product_id]);
        if ($exists) {
            redirectWith(['error'=>'duplicate']);
        }
        $db->insertRow('INSERT INTO customer_product_discount (customer_id, product_id, discount_percentage, is_active) VALUES (?, ?, ?, ?)', [$customer_id, $product_id, $discount_percentage, $is_active]);
        redirectWith(['success'=>'created']);
        break;
    case 'edit':
        $id = intval($_POST['id']);
        $customer_id = intval($_POST['customer_id']);
        $product_id = intval($_POST['product_id']);
        $discount_percentage = floatval($_POST['discount_percentage']);
        $is_active = intval($_POST['is_active']);
        if (!$id || !$customer_id || !$product_id) {
            redirectWith(['error'=>'missing_fields']);
        }
        $db->updateRow('UPDATE customer_product_discount SET customer_id=?, product_id=?, discount_percentage=?, is_active=? WHERE id=?', [$customer_id, $product_id, $discount_percentage, $is_active, $id]);
        redirectWith(['success'=>'updated']);
        break;
    case 'delete':
        $id = intval($_POST['id']);
        if (!$id) {
            redirectWith(['error'=>'invalid_id']);
        }
        $db->updateRow('DELETE FROM customer_product_discount WHERE id=?', [$id]);
        redirectWith(['success'=>'deleted']);
        break;
    default:
        redirectWith(['error'=>'invalid_action']);
}
