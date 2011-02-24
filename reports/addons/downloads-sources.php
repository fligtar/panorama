<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class AddonDownloadsSources extends Report {
    public $table = 'addons_downloads_sources';
    public $backfillable = true;
    public $cron_type = 'yesterday';

    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $qry = "SELECT count, src FROM download_counts WHERE date = '%DATE%'";
        
        $_rows = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $total = 0;
        $sources = array();
        while ($row = mysql_fetch_array($_rows, MYSQL_ASSOC)) {
            $total += $row['count'];
            
            $_sources = unserialize($row['src']);
            if (empty($_sources)) continue;
            foreach ($_sources as $source => $count) {
                if (strpos($source, 'external') !== false) {
                    $source = 'external';
                }
                
                if (array_key_exists($source, $sources)) {
                    $sources[$source] += $count;
                }
                else {
                    $sources[$source] = $count;
                }
            }
        }
        
        $qry = "INSERT INTO {$this->table} (date, total, sources) VALUES ('{$date}', {$total}, '".addslashes(json_encode($sources))."')";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)".mysql_error());
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        $pretty = array(
            '_total' => 'All Sources',
            'null' => 'Unknown',
            'category' => 'Category Browse',
            'search' => 'Search Results',
            'collection' => 'Collections',
            'recommended' => 'Featured Page',
            'homepagebrowse' => 'Homepage (Browse)',
            'homepagepromo' => 'Homepage (Promo)',
            'api' => 'API / Add-ons Manager',
            'sharingapi' => 'Add-on Collector',
            'addondetail' => 'Add-on Details',
            'external' => 'External Sources',
            'developers' => 'Meet the Developers',
            'installservice' => 'Install Service',
            'fxcustomization' => 'Firefox Customization Page',
            'oftenusedwith' => 'Often Used With',
            'similarcollections' => 'Similar Collections',
            'userprofile' => 'User Profile',
            'email' => 'Email Sharing',
            'rockyourfirefox' => 'Rock Your Firefox',
            'mostshared' => 'Most Shared Box',
            'fxfirstrun' => 'Firefox Firstrun',
            'fxwhatsnew' => 'Firefox Updated',
            'creatured' => 'Category Features',
            'version-history' => 'Version History',
            'addon-detail-version' => 'Add-on Details (Version Area)',
            'discovery-pane' => '(Old) Discovery Pane',
            'discovery-pane-details' => '(Old) Discovery Pane Details',
            'discovery-details' => 'Discovery Pane Details',
            'discovery-learnmore' => 'Discovery Pane Learn More'
        );
        
        if ($graph == 'current') {
            echo "Label,Count\n";
            
            $_values = $this->db->query_stats("SELECT sources FROM {$this->table} ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            $values = json_decode($values['sources'], true);
            
            foreach ($values as $column => $value) {
                if (in_array($column, array('total'))) continue;
                
                if (!empty($pretty[$column]))
                    echo "{$pretty[$column]},{$value}\n";
                else
                    echo "{$column},{$value}\n"; 
            }
        }
        elseif ($graph == 'history') {
            $headers = array();
            $sources = array();
            
            $dates = $this->db->query_stats("SELECT date, total, sources FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                $sources[$date['date']] = json_decode($date['sources'], true);
                $sources[$date['date']]['_total'] = $date['total'];
                $headers = array_merge($headers, array_keys($sources[$date['date']]));
            }
            
            $headers = array_unique($headers);
            sort($headers);
            
            echo "Date";
            foreach ($headers as $header) {
                if (!empty($pretty[$header]))
                    echo ",{$pretty[$header]}";
                else
                    echo ",{$header}";
            }
            echo "\n";
            
            foreach ($sources as $date => $source) {
                echo $date;
                foreach ($headers as $header) {
                    if (empty($source[$header]))
                        echo ",0";
                    else
                        echo ",{$source[$header]}";
                }
                echo "\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonDownloadsSources;
    $report->generateCSV($graph);
}

?>