<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../web/reports/contributions/sources.php';

ob_start();

$report = new ContributionSources;

//$report->backfill('2011-01-14');
$report->backfill('2011-05-05', '2011-05-08');

ob_end_flush();

?>