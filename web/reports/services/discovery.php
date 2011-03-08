<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class ServicesDiscovery extends Report {
    public $table = 'services_discovery';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $pane = file_get_contents($data_dir.'/discovery.txt');
        $details = file_get_contents($data_dir.'/discovery-details.txt');
        
        $qry = "INSERT INTO {$this->table} (date, pane, details) VALUES ('{$date}', {$pane}, {$details})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$pane} total)");
        else
            $this->log("{$date} - Problem inserting row ({$pane} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        $api = array(
            'pane' => 'Pane Views',
            'details' => 'Details Page Views'
        );
        $sources = array(
             'discovery-details' => 'Downloads from Details',
             'discovery-learnmore' => 'Downloads from Learn More',
             'discovery-promo' => 'Downloads from Promos',
             'discovery-featured' => 'Downloads from Featured',
             'discovery-upandcoming' => 'Downloads from Up & Coming',
             'discovery-personalrec' => 'Downloads from Personalized Recs'
         );

         if ($graph == 'current') {
             echo "Label,Count\n";

             $_values = $this->db->query_stats("SELECT pane, details FROM {$this->table} ORDER BY date DESC LIMIT 1");
             $values = mysql_fetch_array($_values, MYSQL_ASSOC);

             foreach ($values as $column => $value) {
                 echo "{$columns[$column]},{$value}\n";
             }
         }
         elseif ($graph == 'history') {
             echo "Date,".implode(',', $api).','.implode(',', $sources).",Pane Downloads (All)\n";

             $dates = $this->db->query_stats("SELECT d.date, d.pane, d.details, ads.sources FROM {$this->table} AS d LEFT JOIN addons_downloads_sources AS ads ON ads.date = d.date ORDER BY d.date");
             while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo "{$date['date']},{$date['pane']},{$date['details']}";
                 
                $_source = json_decode($date['sources'], true);
                // Merge old sources
                $_source['discovery-learnmore'] += $_source['discovery-pane'];
                $_source['discovery-details'] += $_source['discovery-pane-details'];
                $total = 0;
                
                foreach ($sources as $source => $desc) {
                    if (!empty($_source[$source])) {
                        $total += $_source[$source];
                        echo ",{$_source[$source]}";
                    }
                    else
                        echo ",0";
                }
                echo ",{$total}\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
 $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
 $report = new ServicesDiscovery;
 $report->generateCSV($graph);
}

?>