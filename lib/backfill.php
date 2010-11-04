<?php
define('OVERLORD', true);
require_once '../reports/addons/impressions.php';

ob_start();

$report = new AddonImpressions;
//$report->backfill('2010-10-19');
$report->backfill('2010-10-08', '2010-10-28');

ob_end_flush();

?>