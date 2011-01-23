<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class AddonPrivacy extends Report {
    public $table = 'addons_privacy';
    public $backfillable = false;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay() {
        $qry = "SELECT status, COUNT(*) FROM (SELECT IF(privacypolicy, 'Has Privacy Policy', 'No Privacy Policy') as status FROM addons WHERE addontype_id < 4) AS temp GROUP BY status ORDER BY status";
        
        $rows = $this->db->query_amo($qry);
        $fields = '';
        $values = '';
        
        if (mysql_num_rows($rows) > 0) {
            while ($row = mysql_fetch_array($rows, MYSQL_NUM)) {
                $fields .= ', '.strtolower(str_replace(' ', '', $row[0]));
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

            $values = $this->db->query_amo("SELECT status, COUNT(*) FROM (SELECT IF(eula, 'Has Privacy Policy', 'No Privacy Policy') as status FROM addons WHERE addontype_id < 4) AS temp GROUP BY status ORDER BY status");
            while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
                echo "{$value[0]},{$value[1]}\n";
            }
        }
        elseif ($graph == 'history') {
            echo "Date,No Privacy Policy,Has Privacy Policy\n";

            $dates = $this->db->query_stats("SELECT date, noprivacypolicy, hasprivacypolicy FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonPrivacy;
    $report->generateCSV($graph);
}
?>