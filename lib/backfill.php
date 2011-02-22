<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/addons/creation.php';

ob_start();

$report = new AddonCreation;

//$report->backfill('2011-01-14');
$report->backfill('2010-05-07', '2010-05-07');

ob_end_flush();

?>