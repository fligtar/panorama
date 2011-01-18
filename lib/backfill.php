<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/contributions/summary.php';

ob_start();

$report = new ContributionSummary;

//$report->backfill('2011-01-14');
$report->backfill('2011-01-02', '2011-01-15');

ob_end_flush();

?>