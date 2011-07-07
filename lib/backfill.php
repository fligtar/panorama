<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../web/reports/contributions/sources.php';

ob_start();

$report = new ContributionSources;

//$report->backfill('2011-01-14');
$report->backfill('2011-05-26', '2011-06-15');

ob_end_flush();

?>