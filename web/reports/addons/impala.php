<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonImpala extends Report {
    public $table = 'addons_impala';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $total_views = file_get_contents($data_dir.'/impala-all.txt');
        $details_views = file_get_contents($data_dir.'/impala-details.txt');
        $home_views = $total_views - $details_views;
        
        $qry = "INSERT INTO {$this->table} (date, home, details) VALUES ('{$date}', {$home_views}, {$details_views})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total_views} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total_views} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph = 'home') {
        if ($graph == 'home') {
            $pretty = 'Homepage Views';
            
            $sources = array(
                 'hp-btn-promo' => 'Promo - Button',
                 'hp-dl-promo' => 'Promo - Details Link',
                 'hp-hc-featured' => 'Featured - Hovercard',
                 'hp-dl-featured' => 'Featured - Details Link',
                 'hp-hc-upandcoming' => 'Up & Coming - Hovercard',
                 'hp-dl-upandcoming' => 'Up & Coming - Details Link',
                 'hp-hc-mostpopular' => 'Most Popular - Hovercard',
                 'hp-dl-mostpopular' => 'Most Popular - Details Link'
             );
        }
        elseif ($graph == 'details') {
            $pretty = 'Details Page Views';
            
            $sources = array(
                 'dp-hc-oftenusedwith' => 'Often Used With - Hovercard',
                 'dp-dl-oftenusedwith' => 'Often Used With - Details Link',
                 'dp-hc-othersby' => 'Others By Author - Hovercard',
                 'dp-dl-othersby' => 'Others By Author - Details Link'
             );
        }
        else {
            exit;
        }
        
        echo "Date,{$pretty},".implode(',', $sources).",Downloads Sum\n";
        
        $dates = $this->db->query_stats("SELECT d.date, d.{$graph}, ads.sources FROM {$this->table} AS d LEFT JOIN addons_downloads_sources AS ads ON ads.date = d.date ORDER BY d.date");
        while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
            echo "{$date['date']},{$date[$graph]}";
 
            $_source = json_decode($date['sources'], true);
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

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
 $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'home';
 $report = new AddonImpala;
 $report->generateCSV($graph);
}

?>