<?php
/*
 * Call to process daily reports. Possible syntax:
 * run today's live db reports - /usr/bin/php -f daily.php
 * run yesterday's hadoop reports - /usr/bin/php -f daily.php yesterday
 * backfill hadoop reports - /usr/bin/php -f daily.php yesterday 2011-01-01
 */

define('OVERLORD', true);
ob_start();

// Iterate through reports directories to get every report
$containers = array();
$reports_dir = '../web/reports';
$cron_type = (!empty($argv[1]) && $argv[1] == 'yesterday') ? 'yesterday' : 'today';
$date = !empty($argv[2]) ? $argv[2] : '';
 
if ($dh = opendir($reports_dir)) {
    while (($container = readdir($dh)) !== false) {
        // Ignore pesky ., .., .svn, .git, .DS_Store, etc.
        if ($container[0] != '.') {
            $containers[$container] = array();
            // Now that we have the container, get the reports inside of it
            if ($dh2 = opendir("{$reports_dir}/{$container}")) {
                while (($report = readdir($dh2)) !== false) {
                    if ($report[0] != '.') {
                        $containers[$container][] = $report;
                    }
                }
            }
        }
    }
}

// We now have a replica of the reports directory structure
foreach ($containers as $container => $reports) { 
    foreach ($reports as $report_file) {
        include "{$reports_dir}/{$container}/{$report_file}";
        // The class we want is always the one last declared
        $classes = get_declared_classes();
        $report = new $classes[count($classes) - 1];
        
        // Call the report's daily method. If the report itself has no
        // daily method, the superclass will handle it gracefully.
        $report->daily($cron_type, $date);
        
        // GC
        $report = null;
    }
}

echo "<br/><br/>DONE.";

ob_end_flush();
?>