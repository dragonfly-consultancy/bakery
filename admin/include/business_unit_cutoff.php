<?php

function getBusinessUnitCutoffSettingsColumns(Database $db)
{
    $columns = [];

    try {
        $rows = $db->getRows('SHOW COLUMNS FROM `business_unit_cutoff_settings`') ?: [];
    } catch (Exception $e) {
        return $columns;
    }

    foreach ($rows as $row) {
        if (!empty($row['Field'])) {
            $columns[$row['Field']] = true;
        }
    }

    return $columns;
}

function getBusinessUnitCutoffSettingsIndexes(Database $db)
{
    $indexes = [];

    try {
        $rows = $db->getRows('SHOW INDEX FROM `business_unit_cutoff_settings`') ?: [];
    } catch (Exception $e) {
        return $indexes;
    }

    foreach ($rows as $row) {
        if (!empty($row['Key_name'])) {
            $indexes[$row['Key_name']] = true;
        }
    }

    return $indexes;
}

function seedDefaultBusinessUnitCutoffSettings(Database $db)
{
    $defaults = [
        'GF' => [
            'standing_order_cutoff_time' => '12:30:00',
            'late_order_cutoff_time' => '15:30:00',
            'cutoff_period' => 1,
        ],
        'STRADA' => [
            'standing_order_cutoff_time' => '16:00:00',
            'late_order_cutoff_time' => '18:00:00',
            'cutoff_period' => 2,
        ],
    ];

    foreach ($defaults as $businessUnitName => $defaultRow) {
        try {
            $businessUnit = $db->getRow(
                'SELECT business_unit_id FROM business_unit_master WHERE UPPER(TRIM(business_unit_name)) = ? LIMIT 1',
                [$businessUnitName]
            );
        } catch (Exception $e) {
            return;
        }

        if (!$businessUnit || empty($businessUnit['business_unit_id'])) {
            continue;
        }

        try {
            $existing = $db->getRow(
                'SELECT cutoff_setting_id FROM business_unit_cutoff_settings WHERE business_unit_id = ? LIMIT 1',
                [(int) $businessUnit['business_unit_id']]
            );
        } catch (Exception $e) {
            continue;
        }

        if ($existing) {
            continue;
        }

        try {
            $db->insertRow(
                'INSERT INTO business_unit_cutoff_settings (business_unit_id, standing_order_cutoff_time, late_order_cutoff_time, cutoff_period) VALUES (?, ?, ?, ?)',
                [
                    (int) $businessUnit['business_unit_id'],
                    $defaultRow['standing_order_cutoff_time'],
                    $defaultRow['late_order_cutoff_time'],
                    $defaultRow['cutoff_period'],
                ]
            );
        } catch (Exception $e) {
            // Ignore duplicate or partial seed errors so the settings pages can still load.
        }
    }
}

function ensureBusinessUnitCutoffSettingsTable(Database $db)
{
    try {
        $db->insertRow('CREATE TABLE IF NOT EXISTS `business_unit_cutoff_settings` (
            `cutoff_setting_id` int(10) NOT NULL AUTO_INCREMENT,
            `business_unit_id` int(10) NOT NULL,
            `standing_order_cutoff_time` time DEFAULT NULL,
            `late_order_cutoff_time` time DEFAULT NULL,
            `cutoff_period` int(10) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`cutoff_setting_id`),
            UNIQUE KEY `uq_business_unit_cutoff_settings_business_unit` (`business_unit_id`),
            KEY `idx_business_unit_cutoff_settings_period` (`cutoff_period`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
    } catch (Exception $e) {
        return false;
    }

    $columns = getBusinessUnitCutoffSettingsColumns($db);

    if (!isset($columns['standing_order_cutoff_time'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD COLUMN `standing_order_cutoff_time` time DEFAULT NULL AFTER `business_unit_id`');
        } catch (Exception $e) {
            // Ignore and continue with the columns that exist.
        }
    }

    if (!isset($columns['late_order_cutoff_time'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD COLUMN `late_order_cutoff_time` time DEFAULT NULL AFTER `standing_order_cutoff_time`');
        } catch (Exception $e) {
            // Ignore and continue with the columns that exist.
        }
    }

    if (!isset($columns['cutoff_period'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD COLUMN `cutoff_period` int(10) NOT NULL DEFAULT 1 AFTER `late_order_cutoff_time`');
        } catch (Exception $e) {
            // Ignore and continue with the columns that exist.
        }
    }

    if (!isset($columns['created_at'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `cutoff_period`');
        } catch (Exception $e) {
            // Ignore and continue with the columns that exist.
        }
    }

    if (!isset($columns['updated_at'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');
        } catch (Exception $e) {
            // Ignore and continue with the columns that exist.
        }
    }

    $indexes = getBusinessUnitCutoffSettingsIndexes($db);

    if (!isset($indexes['uq_business_unit_cutoff_settings_business_unit'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD UNIQUE KEY `uq_business_unit_cutoff_settings_business_unit` (`business_unit_id`)');
        } catch (Exception $e) {
            // Ignore and continue with the indexes that exist.
        }
    }

    if (!isset($indexes['idx_business_unit_cutoff_settings_period'])) {
        try {
            $db->insertRow('ALTER TABLE `business_unit_cutoff_settings` ADD KEY `idx_business_unit_cutoff_settings_period` (`cutoff_period`)');
        } catch (Exception $e) {
            // Ignore and continue with the indexes that exist.
        }
    }

    seedDefaultBusinessUnitCutoffSettings($db);

    return true;
}

function normalizeBusinessUnitCutoffTime($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    if (preg_match('/^\d{2}:\d{2}$/', $value) === 1) {
        return $value . ':00';
    }

    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) === 1) {
        return $value;
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return false;
    }

    return date('H:i:s', $timestamp);
}

function formatBusinessUnitCutoffDisplayTime($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('g:i A', $timestamp);
}

function formatBusinessUnitCutoffInputTime($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return '';
    }

    return date('H:i', $timestamp);
}

function getBusinessUnitCutoffSettings(Database $db)
{
    ensureBusinessUnitCutoffSettingsTable($db);

    try {
        return $db->getRows(
            'SELECT
                bu.business_unit_id,
                bu.business_unit_name,
                s.cutoff_setting_id,
                s.standing_order_cutoff_time,
                s.late_order_cutoff_time,
                s.cutoff_period
             FROM business_unit_master bu
             LEFT JOIN business_unit_cutoff_settings s ON s.business_unit_id = bu.business_unit_id
             ORDER BY CASE
                        WHEN s.cutoff_period IS NULL OR s.cutoff_period < 1 THEN bu.business_unit_id
                        ELSE s.cutoff_period
                      END ASC,
                      bu.business_unit_name ASC'
        ) ?: [];
    } catch (Exception $e) {
        return [];
    }
}

function getStandingOrderDeadlineChips(Database $db)
{
    $rows = getBusinessUnitCutoffSettings($db);
    $chips = [];

    foreach ($rows as $row) {
        $businessUnitName = trim((string) ($row['business_unit_name'] ?? ''));
        $standingOrderTime = formatBusinessUnitCutoffDisplayTime($row['standing_order_cutoff_time'] ?? '');

        if ($businessUnitName === '' || $standingOrderTime === '') {
            continue;
        }

        $chips[] = [
            'business_unit_name' => $businessUnitName,
            'label' => $businessUnitName . ' deadline ' . $standingOrderTime,
        ];
    }

    return $chips;
}

function getLateOrderDeadlineChips(Database $db)
{
    $rows = getBusinessUnitCutoffSettings($db);
    $chips = [];

    foreach ($rows as $row) {
        $businessUnitName = trim((string) ($row['business_unit_name'] ?? ''));
        $lateOrderTime = formatBusinessUnitCutoffDisplayTime($row['late_order_cutoff_time'] ?? '');

        if ($businessUnitName === '' || $lateOrderTime === '') {
            continue;
        }

        $chips[] = [
            'business_unit_name' => $businessUnitName,
            'label' => $businessUnitName . ' late order ' . $lateOrderTime,
        ];
    }

    return $chips;
}

function getDistinctBusinessUnitIdsForItems(Database $db, array $itemIds)
{
    $itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds), function ($id) {
        return $id > 0;
    })));

    if (empty($itemIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($itemIds), '?'));

    try {
        $rows = $db->getRows(
            'SELECT DISTINCT item_business_unit FROM item_master
             WHERE item_id IN (' . $placeholders . ')
               AND item_business_unit IS NOT NULL
               AND item_business_unit > 0',
            $itemIds
        ) ?: [];
    } catch (Exception $e) {
        return [];
    }

    $ids = [];
    foreach ($rows as $row) {
        $businessUnitId = (int) ($row['item_business_unit'] ?? 0);
        if ($businessUnitId > 0) {
            $ids[$businessUnitId] = true;
        }
    }

    return array_keys($ids);
}

function selectActiveBusinessUnitCutoffRow(Database $db, array $businessUnitIds)
{
    if (empty($businessUnitIds)) {
        return null;
    }

    ensureBusinessUnitCutoffSettingsTable($db);

    $allRows = getBusinessUnitCutoffSettings($db);
    $rowsById = [];
    foreach ($allRows as $row) {
        $rowsById[(int) ($row['business_unit_id'] ?? 0)] = $row;
    }

    $candidates = [];
    foreach ($businessUnitIds as $businessUnitId) {
        if (isset($rowsById[(int) $businessUnitId])) {
            $candidates[] = $rowsById[(int) $businessUnitId];
        }
    }

    if (empty($candidates)) {
        return null;
    }

    foreach ($candidates as $row) {
        if (strcasecmp(trim((string) ($row['business_unit_name'] ?? '')), 'GF') === 0) {
            return $row;
        }
    }

    usort($candidates, function ($a, $b) {
        $aPeriod = (int) ($a['cutoff_period'] ?? PHP_INT_MAX);
        $bPeriod = (int) ($b['cutoff_period'] ?? PHP_INT_MAX);
        if ($aPeriod === $bPeriod) {
            return strcasecmp((string) ($a['business_unit_name'] ?? ''), (string) ($b['business_unit_name'] ?? ''));
        }
        return $aPeriod <=> $bPeriod;
    });

    return $candidates[0];
}

function evaluateOrderCutoffStatus(Database $db, $deliveryDate, array $itemIds, $now = null)
{
    $result = [
        'status' => 'editable',
        'reason' => '',
        'business_unit_id' => null,
        'business_unit_name' => null,
        'cutoff_period' => null,
        'standing_order_cutoff_time' => null,
        'late_order_cutoff_time' => null,
        'standing_order_cutoff_label' => null,
        'late_order_cutoff_label' => null,
        'cdd' => null,
        'delivery_date' => null,
    ];

    $deliveryTs = is_string($deliveryDate) ? strtotime($deliveryDate) : null;
    if (!$deliveryTs) {
        return $result;
    }

    if ($now === null) {
        $nowTs = time();
    } elseif (is_int($now)) {
        $nowTs = $now;
    } elseif (ctype_digit((string) $now)) {
        $nowTs = (int) $now;
    } else {
        $parsed = strtotime((string) $now);
        $nowTs = $parsed !== false ? $parsed : time();
    }

    $businessUnitIds = getDistinctBusinessUnitIdsForItems($db, $itemIds);
    $row = selectActiveBusinessUnitCutoffRow($db, $businessUnitIds);
    if ($row === null) {
        $result['delivery_date'] = date('Y-m-d', $deliveryTs);
        return $result;
    }

    $cutoffPeriod = (int) ($row['cutoff_period'] ?? 0);
    if ($cutoffPeriod < 1) {
        $cutoffPeriod = 1;
    }

    $todayStart = strtotime(date('Y-m-d', $nowTs));
    $deliveryStart = strtotime(date('Y-m-d', $deliveryTs));
    $cdd = (int) round(($deliveryStart - $todayStart) / 86400);

    $businessUnitName = trim((string) ($row['business_unit_name'] ?? 'Business unit'));
    $standingTimeLabel = formatBusinessUnitCutoffDisplayTime($row['standing_order_cutoff_time'] ?? '');
    $lateTimeLabel = formatBusinessUnitCutoffDisplayTime($row['late_order_cutoff_time'] ?? '');

    $result['business_unit_id'] = (int) ($row['business_unit_id'] ?? 0);
    $result['business_unit_name'] = $businessUnitName;
    $result['cutoff_period'] = $cutoffPeriod;
    $result['standing_order_cutoff_time'] = $row['standing_order_cutoff_time'] ?? null;
    $result['late_order_cutoff_time'] = $row['late_order_cutoff_time'] ?? null;
    $result['standing_order_cutoff_label'] = $standingTimeLabel;
    $result['late_order_cutoff_label'] = $lateTimeLabel;
    $result['cdd'] = $cdd;
    $result['delivery_date'] = date('Y-m-d', $deliveryStart);

    if ($cdd < $cutoffPeriod) {
        $result['status'] = 'locked';
        $result['reason'] = $businessUnitName . ' orders for this delivery date are closed. Orders must be placed at least ' . $cutoffPeriod . ' day(s) ahead.';
        return $result;
    }

    if ($cdd > $cutoffPeriod) {
        $result['status'] = 'editable';
        return $result;
    }

    $currentTimeKey = (int) date('His', $nowTs);
    $lateTimeKey = !empty($row['late_order_cutoff_time']) ? (int) date('His', strtotime($row['late_order_cutoff_time'])) : null;
    $standingTimeKey = !empty($row['standing_order_cutoff_time']) ? (int) date('His', strtotime($row['standing_order_cutoff_time'])) : null;

    if ($lateTimeKey !== null && $currentTimeKey > $lateTimeKey) {
        $result['status'] = 'locked';
        $result['reason'] = $businessUnitName . ' late order cutoff (' . $lateTimeLabel . ') has passed for this delivery date.';
        return $result;
    }

    if ($standingTimeKey !== null && $currentTimeKey > $standingTimeKey) {
        $result['status'] = 'late_only';
        $result['reason'] = $businessUnitName . ' standing order cutoff (' . $standingTimeLabel . ') has passed. Late (cart) orders are still allowed until ' . $lateTimeLabel . '.';
        return $result;
    }

    return $result;
}

function findEarliestStandingOrderDeliveryDate(array $items, $dateFrom, $dateTo, $now = null)
{
    if (empty($items)) {
        return null;
    }

    $nowTs = $now ?: time();
    $todayStart = strtotime(date('Y-m-d', $nowTs));

    $startTs = $dateFrom ? strtotime($dateFrom) : false;
    if (!$startTs || $startTs < $todayStart) {
        $startTs = $todayStart;
    }

    $endTs = $dateTo ? strtotime($dateTo) : false;
    if (!$endTs) {
        $endTs = $startTs + 60 * 86400;
    }
    if ($endTs < $startTs) {
        return null;
    }

    $dayHasQty = array_fill(0, 7, false);
    foreach ($items as $item) {
        $qty = isset($item['qty']) && is_array($item['qty']) ? $item['qty'] : [];
        for ($i = 0; $i < 7; $i++) {
            if (isset($qty[$i]) && (float) $qty[$i] > 0) {
                $dayHasQty[$i] = true;
            }
        }
    }

    if (!in_array(true, $dayHasQty, true)) {
        return null;
    }

    $safetyCap = 90;
    for ($ts = $startTs, $iteration = 0; $ts <= $endTs && $iteration < $safetyCap; $ts += 86400, $iteration++) {
        $dayIndex = ((int) date('N', $ts)) - 1;
        if (!empty($dayHasQty[$dayIndex])) {
            return date('Y-m-d', $ts);
        }
    }

    return null;
}