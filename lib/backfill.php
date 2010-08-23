<?php
define('OVERLORD', true);
require_once '../reports/collections/votes-vote.php';

ob_start();

$report = new CollectionVotesVote;
$report->backfill('2009-08-27');
//$report->backfill('2009-08-27', '2009-09-01');

ob_end_flush();

?>