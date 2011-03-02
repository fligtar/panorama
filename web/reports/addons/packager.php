<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonPackager extends Report {
    public $table = 'addons_packager';
    public $backfillable = true;

    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $qry = "SELECT serialized FROM fizzypop WHERE DATE(created) = '%DATE%'";
        
        $_rows = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $total = mysql_num_rows($_rows);
        $ui_components = array();
        while ($row = mysql_fetch_array($_rows, MYSQL_ASSOC)) {
            $selections = mb_unserialize($row['serialized']);

            if (empty($selections['ui'])) continue;
            foreach ($selections['ui'] as $ui => $value) {
                if (array_key_exists($ui, $ui_components)) {
                    $ui_components[$ui]++;
                }
                else {
                    $ui_components[$ui] = 1;
                }
            }
        }
        
        $fields = '';
        $values = '';
        foreach ($ui_components as $ui => $count) {
            $fields .= ", ".$ui;
            $values .= ','.$count;
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
            'total' => 'Total Add-ons',
            'about' => 'About Dialog',
            'options' => 'Preferences Dialog',
            'toolbar' => 'Toolbar',
            'toolbarbutton' => 'Toolbar Button',
            'mainmenu' => 'Main Menu Command',
            'contextmenu' => 'Context Menu Command',
            'sidebar' => 'Sidebar'
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
    $report = new AddonPackager;
    $report->generateCSV($graph);
}

?>