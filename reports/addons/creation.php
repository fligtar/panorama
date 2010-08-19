<?php
require_once '../../lib/db.php';

$addontypes = array();
$_addontypes = $db->query_stats("SELECT * FROM addontypes ORDER BY date");
while ($addontype = mysql_fetch_array($_addontypes, MYSQL_ASSOC)) {
    foreach ($addontype as $field => $value) {
        if ($field == 'date' || $field == 'total') continue;
        $addontypes[$field][] = $value;
    }
}
?>

var series = [
        {
        	type: 'line',
        	name: 'Extensions',
        	pointInterval: 24 * 3600 * 1000,
        	pointStart: Date.UTC(2006, 01, 01),
        	data: [<?=implode(',', $addontypes['id1']);?>]
        },
        {
        	type: 'area',
        	name: 'Themes',
        	pointInterval: 24 * 3600 * 1000,
        	pointStart: Date.UTC(2006, 01, 01),
        	data: [<?=implode(',', $addontypes['id2']);?>]
        },
        {
        	type: 'area',
        	name: 'Dictionaries',
        	pointInterval: 24 * 3600 * 1000,
        	pointStart: Date.UTC(2006, 01, 01),
        	data: [<?=implode(',', $addontypes['id3']);?>]
        },
        {
        	type: 'area',
        	name: 'Search Providers',
        	pointInterval: 24 * 3600 * 1000,
        	pointStart: Date.UTC(2006, 01, 01),
        	data: [<?=implode(',', $addontypes['id4']);?>]
        },
        {
        	type: 'area',
        	name: 'Language Packs',
        	pointInterval: 24 * 3600 * 1000,
        	pointStart: Date.UTC(2006, 01, 01),
        	data: [<?=implode(',', $addontypes['id5']);?>]
        }
];