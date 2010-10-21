<?php
define('OVERLORD', true);
require_once '../reports/services/api.php';

ob_start();

$report = new ServicesAPI;
//$report->backfill('2010-10-19');
$report->backfill('2010-10-17', '2010-10-18');

ob_end_flush();

?>