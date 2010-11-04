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
        
        $fields = '';
        $values = '';
        foreach ($sources as $source => $count) {
            $source = str_replace('-', '_', $source);
            if ($source == 'null') $source = 'unknown';
            
            $fields .= ", ".$source;
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
            'total' => 'All Sources',
            'unknown' => 'Unknown',
            'category' => 'Category Browse',
            'search' => 'Search Results',
            'collection' => 'Collections',
            'recommended' => 'Featured Page',
            'homepagebrowse' => 'Homepage (Browse)',
            'homepagepromo' => 'Homepage (Promo)',
            'api' => 'API / Addons Manager',
            'sharingapi' => 'Addon Collector',
            'addondetail' => 'Addon Details',
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
            'version_history' => 'Version History',
            'addon_detail_version' => 'Addon Details (Version Area)',
            'discovery_pane' => 'Discovery Pane'
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
    $report = new AddonDownloadsSources;
    $report->generateCSV($graph);
}

?>