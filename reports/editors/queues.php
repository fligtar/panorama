<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class EditorQueues extends Report {
    public $table = 'editors_queues';
    public $backfillable = false;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        $date = date('Y-m-d');
        
        $queues = array(
            'fullreview' => "SELECT COUNT(*) FROM addons WHERE status in (3,9)",
            'prelimreview' => "SELECT COUNT(*) FROM addons WHERE status=1",
            'fullupdates' => "SELECT COUNT(*) FROM files INNER JOIN versions ON versions.id=files.version_id INNER JOIN addons ON addons.id=versions.addon_id WHERE files.status=1 and addons.status=4",
            'prelimupdates' => "SELECT COUNT(*) FROM (SELECT COUNT(*) FROM files INNER JOIN versions ON versions.id=files.version_id INNER JOIN addons ON addons.id=versions.addon_id WHERE files.status=1 and addons.status=8 GROUP BY addons.id) as temp",
            'adminreview' => "SELECT COUNT(*) FROM addons WHERE adminreview=1",
            'flaggedreviews' => "SELECT COUNT(*) FROM reviews WHERE editorreview=1"
        );
        
        foreach ($queues as $name => $qry) {
            $_total = $this->db->query_amo($qry);
            $total = mysql_fetch_array($_total);
            $counts[$name] = $total[0];
        }

        $qry = "INSERT INTO {$this->table} (date, ".implode(', ', array_keys($counts)).") VALUES ('{$date}', ".implode(', ', $counts).")";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row)");
        else
            $this->log("{$date} - Problem inserting row");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV() {
        echo "Date,Full Reviews,Preliminary Reviews,Full Updates,Preliminary Updates,Admin Reviews,Flagged Reviews\n";

        $dates = $this->db->query_stats("SELECT date, fullreview, prelimreview, fullupdates, prelimupdates, adminreview, flaggedreviews FROM {$this->table} ORDER BY date");
        while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
            echo implode(',', $date)."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new EditorQueues;
    $report->generateCSV();
}

?>
