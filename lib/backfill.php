<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/addons/installdistro.php';

ob_start();

$report = new AddonInstalled;

//$report->backfill('2011-01-14');
$report->backfill('2011-02-24', '2011-02-24');

ob_end_flush();

?>