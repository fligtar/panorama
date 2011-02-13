<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class AddonImpressions extends Report {
    public $table = 'addons_impressions';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        // Retired by CDN
        return false;
        
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $data = array();
        $nomatches = array();
        $data['total'] = file_get_contents($data_dir.'/abp-icon-total.txt');
        $referrers = file_get_contents($data_dir.'/abp-icon-referrers.txt');
        $referrers = explode("\n", $referrers);
        
        $patterns = array(
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/?(\?.+)?$/' => 'homepage',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/featured\/?$/' => 'featured',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/addon\/1865\/?(\?src=api)$/' => 'details_api',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/addon\/1865\/?(\?.+)?$/' => 'details',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/extensions\/?(\?.+)?$/' => 'extensions',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/extensions\/privacy-security\/?(\?.+)?$/' => 'category',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/search\/?/' => 'search',
            '/https?:\/\/addons\.mozilla\.org\/[^\/]+\/(firefox|thunderbird|seamonkey|sunbird|mobile)\/collections\/?/' => 'collections',
            '/https?:\/\/addons\.mozilla\.jp/' => 'japan',
            '/https?:\/\/17huohu.cn/' => 'china',
            '/https?:\/\/services\.addons\.mozilla\.org\/.+\/discovery\/.+/' => 'discovery',
            '/^-$/' => 'none'
        );
        
        foreach ($referrers as $r) {
            $match = false;
            $r = explode("\t", $r);
            
            foreach ($patterns as $pattern => $source) {
                if (preg_match($pattern, $r[0]) > 0) {
                    if (!empty($data[$source]))
                        $data[$source] += $r[1];
                    else
                        $data[$source] = $r[1];
                    
                    $match = true;
                    break;
                }
            }
            
            // External sites
            if ($match != true && preg_match('/^https?:\/\/addons\.mozilla\.org/', $r[0]) == 0) {
                if (!empty($data['external']))
                    $data['external'] += $r[1];
                else
                    $data['external'] = $r[1];
                
                $match = true;
            }
            
            if ($match != true) {
                // No matches yet
                $nomatches[] = $r;
            }
        }
        
        //print_r($data);
        //print_r($nomatches);
        
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
            'total' => 'All Impressions',
            'none' => 'Unknown Source',
            'homepage' => 'Homepage',
            'featured' => 'Featured Page',
            'details' => 'Details Page',
            'extensions' => 'Extensions Browse',
            'category' => 'Category Browse',
            'search' => 'Search',
            'external' => 'External Sites',
            'collections' => 'Collections',
            'details_api' => 'Details Page via API',
            'discovery' => 'Discovery Pane',
            'china' => 'China (17huohu.cn)',
            'japan' => 'Japan (AMJ)'
        );
        
        if ($graph == 'current') {
            echo "Label,Count\n";

            $_values = $this->db->query_stats("SELECT homepage, featured, details, extensions, category, search, external, collections, details_api, discovery, china, japan FROM {$this->table} ORDER BY date DESC LIMIT 1");
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
    $report = new AddonImpressions;
    $report->generateCSV($graph);
}

?>