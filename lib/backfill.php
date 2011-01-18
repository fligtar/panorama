<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/contributions/sources.php';

ob_start();

$report = new ContributionSources;

//$report->backfill('2011-01-14');
$report->backfill('2009-09-07', '2010-09-23');

ob_end_flush();

?>