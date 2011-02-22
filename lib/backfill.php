<?php
error_reporting(E_ERROR);
define('OVERLORD', true);
require_once '../reports/goals/2011.php';

ob_start();

$report = new Goals2011;

$report->backfill('2011-01-14');
//$report->backfill('2010-05-07', '2010-05-07');

ob_end_flush();

?>