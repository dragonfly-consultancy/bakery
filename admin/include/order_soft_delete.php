<?php

if (!function_exists('ensureInvoiceOrderSoftDeleteColumns')) {
    function ensureInvoiceOrderSoftDeleteColumns($db)
    {
        $columnsToAdd = [
            'is_deleted' => "ALTER TABLE invoice_hedder ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `invoice_h_status`",
            'deleted_at' => "ALTER TABLE invoice_hedder ADD COLUMN `deleted_at` DATETIME NULL AFTER `is_deleted`",
            'deleted_by' => "ALTER TABLE invoice_hedder ADD COLUMN `deleted_by` VARCHAR(100) NULL AFTER `deleted_at`",
            'delete_reason' => "ALTER TABLE invoice_hedder ADD COLUMN `delete_reason` VARCHAR(500) NULL AFTER `deleted_by`",
        ];

        foreach ($columnsToAdd as $columnName => $alterSql) {
            try {
                $column = $db->getRow("SHOW COLUMNS FROM invoice_hedder LIKE ?", [$columnName]);
                if (!$column) {
                    $db->insertRow($alterSql, []);
                }
            } catch (Exception $exception) {
            }
        }
    }
}

if (!function_exists('getOrderDeleteBlockReason')) {
    function getOrderDeleteBlockReason($order, $todayDate)
    {
        if (!is_array($order) || empty($order)) {
            return 'Order not found';
        }

        $alreadyDeleted = isset($order['is_deleted']) && (int)$order['is_deleted'] === 1;
        if ($alreadyDeleted) {
            return 'Order is already deleted';
        }

        $statusCode = isset($order['invoice_h_status']) ? (int)$order['invoice_h_status'] : null;
        if ($statusCode === -1) {
            return 'Cancelled orders cannot be deleted';
        }

        $deliveryDate = isset($order['invoice_h_delivery_date']) ? trim((string)$order['invoice_h_delivery_date']) : '';
        if ($deliveryDate === '') {
            return 'Order delivery date is missing';
        }

        if ($deliveryDate <= $todayDate) {
            return 'Only future delivery date orders can be deleted';
        }

        $orderNote = isset($order['invoice_h_order_note']) ? (string)$order['invoice_h_order_note'] : '';
        $isStandingOrder = stripos($orderNote, 'standing') !== false;

        if ($statusCode !== 0 && !$isStandingOrder) {
            return 'Only pending or standing orders can be deleted';
        }

        return '';
    }
}
