<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonStatus extends Report {
    public $table = 'addons_status';
    public $backfillable = false;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay() {
        $qry = "SELECT status, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY status ORDER BY status";
        
        $_types = $this->db->query_amo($qry);
        
        $fields = '';
        $values = '';
        $total = 0;
        if (mysql_num_rows($_types) > 0) {
            while ($type = mysql_fetch_array($_types, MYSQL_NUM)) {
                $fields .= ", status{$type[0]}";
                $values .= ','.$type[1];
                $total += $type[1];
            }
        }
        
        $date = date('Y-m-d');
        $qry = "INSERT INTO {$this->table} (date{$fields}) VALUES ('{$date}'{$values})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        $labels = array(
            'Incomplete',
            'Awaiting Preliminary Review (Previously Unreviewed)',
            'Pending (Files only)',
            'Awaiting Full Review',
            'Fully Reviewed',
            'Admin Disabled',
            'Self-hosted',
            'Beta (Files only)',
            'Preliminarily Reviewed',
            'Preliminarily Reviewed and Awaiting Full Review',
            'Purgatory'
        );
        
        if ($graph == 'current') {
            echo "Label,Count\n";

            $values = $this->db->query_amo("SELECT status, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY status ORDER BY status");
            while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
                echo "{$labels[$value[0]]},{$value[1]}\n";
            }
        }
        elseif ($graph == 'history') {
            echo "Date,".implode(',', $labels)."\n";

            $dates = $this->db->query_stats("SELECT date, status0, status1, status2, status3, status4, status5, status6, status7, status8, status9, status10 FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonStatus;
    $report->generateCSV($graph);
}

?>