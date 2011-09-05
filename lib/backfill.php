<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../web/reports/addons/updatepings.php';

ob_start();

$report = new AddonUpdatePings;

//$report->backfill('2011-01-14');
$report->backfill('2010-01-01', '2010-12-31');

ob_end_flush();

?>