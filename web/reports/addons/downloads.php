<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonDownloads extends Report {
    public $table = 'addons_downloads';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $qry = "SELECT a.addontype_id, SUM(dc.count) FROM download_counts AS dc INNER JOIN addons AS a ON dc.addon_id = a.id WHERE dc.date = '%DATE%' GROUP BY a.addontype_id ORDER BY a.addontype_id";
        
        $_types = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $fields = '';
        $values = '';
        $total = 0;
        if (mysql_num_rows($_types) > 0) {
            while ($type = mysql_fetch_array($_types, MYSQL_NUM)) {
                $fields .= ", type{$type[0]}";
                $values .= ','.$type[1];
                $total += $type[1];
            }
        }
        
        $qry = "INSERT INTO {$this->table} (date, total{$fields}) VALUES ('{$date}', {$total}{$values})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        $columns = array(
            'total' => 'All Types',
            'type1' => 'Extensions',
            'type2' => 'Themes',
            'type3' => 'Dictionaries',
            'type4' => 'Search Providers',
            'type5' => 'Language Packs',
            'type9' => 'Personas'
        );
        
        if ($graph == 'current') {
            echo "Label,Count\n";
            
            $_values = $this->db->query_stats("SELECT ".implode(', ', array_keys($columns))." FROM {$this->table} ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            
            foreach ($values as $column => $value) {
                if (in_array($column, array('total'))) continue;
                
                echo "{$columns[$column]},{$value}\n";
            }
        }
        elseif ($graph == 'history') {
            echo "Date,".implode(',', $columns)."\n";

            $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($columns))." FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonDownloads;
    $report->generateCSV($graph);
}

?>