<?php
require_once 'db.php';

// Add-ons - Creation (by add-on type)
$qry = "SELECT addontype_id, COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' GROUP BY addontype_id ORDER BY addontype_id";
$table = 'addons_creation';


$dates = array();

// Loop through each day
$time = strtotime('2006-01-01');

while ($time <= time()) {
    $date = date('Y-m-d', $time);
    $rows = $db->query_amo(str_replace('%DATE%', $date, $qry));
    
    $dates[$date] = array();
    
    if (mysql_num_rows($rows) > 0) {
        while ($row = mysql_fetch_array($rows, MYSQL_NUM)) {
            $dates[$date][$row[0]] = $row[1];
        }
    }
    
    // Increment date
    $time = strtotime('+1 day', $time);
}

foreach ($dates as $date => $items) {
    $fields = '';
    $values = '';
    
    foreach ($items as $item => $count) {
        $fields .= ", type{$item}";
        $values .= ','.$count;
    }
    
    $total = array_sum($items);
    
    $qry = "INSERT INTO {$table} (date, total{$fields}) VALUES ('{$date}', {$total}{$values})";
    
    if ($db->query_stats($qry))
        echo "[{$table}] {$date} - Inserted row ({$total} total)<br/>";
    else
        echo "[{$table}] {$date} - Problem inserting row ({$total} total)<br/>";
}

echo '<pre>'.print_r($dates, true).'</pre>';

?>