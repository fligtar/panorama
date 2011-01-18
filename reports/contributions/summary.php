<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class ContributionSummary extends Report {
    public $table = 'contributions_summary';
    public $backfillable = true;

    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $insert = array();
        
        $queries = array(
            'amt_earned' => "SELECT SUM(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL",
            'amt_avg' => "SELECT AVG(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL",
            'amt_min' => "SELECT MIN(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL",
            'amt_max' => "SELECT MAX(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL",
            'amt_eq_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount = 0+suggested_amount",
            'amt_gt_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount > 0+suggested_amount",
            'amt_lt_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount < 0+suggested_amount",
            'tx_success' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL",
            'tx_abort' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NULL"
        );
        
        foreach ($queries as $queryname => $query) {
            $qry = str_replace('%DATE%', $date, $query);
            
            $_rows = $this->db->query_amo($qry);
            $row = mysql_fetch_array($_rows, MYSQL_NUM);
            
            if (empty($row[0]))
                $row[0] = 0;
            
            $insert[$queryname] = $row[0];
        }
        
        $qry = "INSERT INTO {$this->table} (date, ".implode(', ', array_keys($insert)).") VALUES ('{$date}', ".implode(', ', $insert).")";
        
        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$insert[amt_earned]} total)");
        else
            $this->log("{$date} - Problem inserting row ({$insert[amt_earned]} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
     public function generateCSV($graph) {
        if ($graph == 'total') {
             $plots = array(
                 'amt_earned' => 'Total Contributions'
             );

             echo "Date,".implode(',', $plots)."\n";

             $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date");
             while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
              echo implode(',', $date)."\n";
             }
        }   
        elseif ($graph == 'amt') {
            $plots = array(
                'amt_avg' => 'Average Contribution',
                'amt_min' => 'Minimum Contribution',
                'amt_max' => 'Maximum Contribution'
            );
            
            echo "Date,".implode(',', $plots)."\n";

            $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
             echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'suggested') {
            $plots = array(
                'amt_eq_suggested' => 'Equal to suggested',
                'amt_gt_suggested' => 'Greater than suggested',
                'amt_lt_suggested' => 'Less than suggested'
            );
            
            echo "Date,".implode(',', $plots)."\n";

            $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
             echo implode(',', $date)."\n";
            }
         }
         elseif ($graph == 'tx') {
            $plots = array(
                'tx_success' => 'Successful Transactions',
                'tx_abort' => 'Aborted Transactions'
            );
            
            echo "Date,".implode(',', $plots)."\n";

            $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
             echo implode(',', $date)."\n";
            }
         }
     }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new ContributionSummary;
    $report->generateCSV($graph);
}

?>