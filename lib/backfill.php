<?php
define('OVERLORD', true);
require_once '../reports/addons/downloads.php';

ob_start();

$report = new AddonDownloads;

//$report->backfill('2010-11-01');
$report->backfill('2007-07-01', '2008-01-01');

ob_end_flush();

?>