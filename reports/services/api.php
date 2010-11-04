<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class ServicesAPI extends Report {
    public $table = 'services_api';
    public $backfillable = true;
    
    /**
     * Called daily
     */
    public function daily() {
        $this->analyzeDay();
    }
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $data = array();
        $data['total'] = file_get_contents($data_dir.'/api-total.txt');
        $data['featured'] = file_get_contents($data_dir.'/api-featured.txt');
        $data['addon'] = file_get_contents($data_dir.'/api-addon.txt');
        $data['search'] = file_get_contents($data_dir.'/api-search.txt');
        $data['guidsearch'] = file_get_contents($data_dir.'/api-guid-search.txt');
        
        $qry = "INSERT INTO {$this->table} (date, ".implode(array_keys($data), ', ').") VALUES ('{$date}', ".implode($data, ', ').")";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$data['total']} total)");
        else
            $this->log("{$date} - Problem inserting row ({$data['total']} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        $columns = array(
            'total' => 'All Methods',
            'featured' => 'Featured',
            'addon' => 'Addon Details',
            'search' => 'Search',
            'guidsearch' => 'GUID Search'
        );
        
        if ($graph == 'current') {
            echo "Label,Count\n";

            $_values = $this->db->query_stats("SELECT featured, addon, search, guidsearch FROM {$this->table} ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            
            foreach ($values as $column => $value) {
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
    $report = new ServicesAPI;
    $report->generateCSV($graph);
}

?>