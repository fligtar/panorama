<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/addons/downloads-sources.php';

ob_start();

$report = new AddonDownloadsSources;

//$report->backfill('2011-01-14');
$report->backfill('2009-10-21', '2009-12-31');

ob_end_flush();

?>