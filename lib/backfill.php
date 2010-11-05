<?php
define('OVERLORD', true);
require_once '../reports/addons/packager.php';

ob_start();

$report = new AddonPackager;

//$report->backfill('2010-11-01');
$report->backfill('2009-09-30', '2010-01-01');

ob_end_flush();

?>