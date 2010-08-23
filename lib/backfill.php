<?php
define('OVERLORD', true);
require_once '../reports/users/creation.php';

$report = new UserCreation;
$report->backfill('2010-01-01');

?>