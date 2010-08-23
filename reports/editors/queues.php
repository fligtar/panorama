<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class EditorQueues extends Report {
    public $table = 'editors_queues';
    public $backfillable = true;
    
    /**
     * Called daily
     */
    public function daily() {
        //$this->analyzeDay();
    }
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $qry = "SELECT COUNT(*) FROM users WHERE DATE(created) = '%DATE%'";
        $_total = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        $total = mysql_fetch_array($_total);
        $total = $total[0];
        
        $qry = "SELECT COUNT(*) FROM users WHERE DATE(created) = '%DATE%' AND confirmationcode = ''";
        $_confirmed = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        $confirmed = mysql_fetch_array($_confirmed);
        $confirmed = $confirmed[0];
        
        $qry = "INSERT INTO {$this->table} (date, total, confirmed) VALUES ('{$date}', {$total}, {$confirmed})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV() {
        echo "Date,Nominations,Updates,Admin Reviews,Flagged Reviews\n";

        $dates = $this->db->query_stats("SELECT date, nomination, pending, flagged, reviews FROM {$this->table} ORDER BY date");
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