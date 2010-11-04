<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class AddonPublicStats extends Report {
    public $table = 'addons_publicstats';
    public $backfillable = false;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay() {
        $qry = "SELECT publicstats, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY publicstats ORDER BY publicstats";
        
        $rows = $this->db->query_amo($qry);
        $fields = '';
        $values = '';
        
        $labels = array(
            'private',
            'public'
        );
        
        if (mysql_num_rows($rows) > 0) {
            while ($row = mysql_fetch_array($rows, MYSQL_NUM)) {
                $fields .= ", {$labels[$row[0]]}";
                $values .= ", {$row[1]}";
            }
        }
        
        $date = date('Y-m-d');
        $qry = "INSERT INTO {$this->table} (date{$fields}) VALUES ('{$date}'{$values})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row");
        else
            $this->log("{$date} - Problem inserting row");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        if ($graph == 'current') {
            echo "Label,Count\n";

            $labels = array(
                'Private Stats',
                'Public Stats'
            );

            $values = $this->db->query_amo("SELECT publicstats, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY publicstats ORDER BY publicstats");
            while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
                echo "{$labels[$value[0]]},{$value[1]}\n";
            }
        }
        elseif ($graph == 'history') {
            echo "Date,Public Stats,Private Stats\n";

            $dates = $this->db->query_stats("SELECT date, public, private FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonPublicStats;
    $report->generateCSV($graph);
}
?>