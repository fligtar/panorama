<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class ContributionSources extends Report {
    public $table = 'contributions_sources';
    public $backfillable = true;

    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $insert = array();
        
        $fieldvals = array(
            'addon-detail' => 'addon_detail',
            'standalone' => 'standalone',
            'api' => 'api',
            'meet-developers' => 'meet_developers',
            'post-download' => 'post_download',
            'browse' => 'browse',
            'direct' => 'browse',
            'roadblock' => 'roadblock'
            /*'developers' => 'meet_developers',
            'meet-the-developer' => 'meet_developers',
            'meet-the-developer-post-install' => 'post_download',
            'meet-the-developer-roadblock' => 'roadblock',
            'addondetail' => 'addon_detail',
            'addon-detail-version' => 'addon_detail',
            'search' => 'browse',
            'recommended' => 'browse',
            'homepagepromo' => 'browse',
            'homepagebrowse' => 'browse'*/
        );
        $fieldvals["'".implode("', '", array_keys($fieldvals))."'"] = 'other';
        
        $queries = array(
            'amt_earned' => "SELECT SUM(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND %SOURCEFILTER%",
            'amt_avg' => "SELECT AVG(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND %SOURCEFILTER%",
            'amt_min' => "SELECT MIN(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND %SOURCEFILTER%",
            'amt_max' => "SELECT MAX(amount) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND %SOURCEFILTER%",
            'amt_eq_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount = 0+suggested_amount AND %SOURCEFILTER%",
            'amt_gt_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount > 0+suggested_amount AND %SOURCEFILTER%",
            'amt_lt_suggested' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND 0+amount < 0+suggested_amount AND %SOURCEFILTER%",
            'tx_success' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NOT NULL AND %SOURCEFILTER%",
            'tx_abort' => "SELECT COUNT(*) FROM stats_contributions WHERE DATE(created) = '%DATE%' AND transaction_id IS NULL AND %SOURCEFILTER%"
        );
        
        foreach ($fieldvals as $fieldval => $fielddesc) {
            if ($fielddesc == 'other')
                $source_filter = "source NOT IN (%FIELDVAL%)";
            else
                $source_filter = "source = '%FIELDVAL%'";
            
            foreach ($queries as $queryname => $query) {
                $qry = str_replace('%SOURCEFILTER%', $source_filter, $query);
                $qry = str_replace('%DATE%', $date, $qry);
                $qry = str_replace('%FIELDVAL%', $fieldval, $qry);
                
                $_rows = $this->db->query_amo($qry);
                $row = mysql_fetch_array($_rows, MYSQL_NUM);
                
                if (empty($row[0]))
                    $row[0] = 0;
                
                if (!empty($insert["{$fielddesc}_{$queryname}"]))
                    $insert["{$fielddesc}_{$queryname}"] += $row[0];
                else
                    $insert["{$fielddesc}_{$queryname}"] = $row[0];
            }
        }
        
        $qry = "INSERT INTO {$this->table} (date, ".implode(', ', array_keys($insert)).") VALUES ('{$date}', ".implode(', ', $insert).")";
        
        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row");
        else
            $this->log("{$date} - Problem inserting row - ".mysql_error());
    }
    
    /**
     * Generate the CSV for graphs
     */
     public function generateCSV($graph, $field) {
         $plots = array(
             "addon_detail_{$field}" => 'Add-on Details',
             "browse_{$field}" => 'Browse',
             "meet_developers_{$field}" => 'Meet the Developer',
             "post_download_{$field}" => 'Post-download',
             "roadblock_{$field}" => 'Roadblock',
             "standalone_{$field}" => 'Standalone',
             "api_{$field}" => 'API',
             "other_{$field}" => 'Other'
         );
     
         if ($graph == 'current') {
             echo "Label,Count\n";
         
             $_values = $this->db->query_stats("SELECT ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date DESC LIMIT 1");
             $values = mysql_fetch_array($_values, MYSQL_ASSOC);
         
             foreach ($values as $plot => $value) {
                 echo "{$plots[$plot]},{$value}\n";
             }
         }
         elseif ($graph == 'history') {
             echo "Date,".implode(',', $plots)."\n";
             
             $dates = $this->db->query_stats("SELECT date, ".implode(', ', array_keys($plots))." FROM {$this->table} ORDER BY date");
             while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                 echo implode(',', $date)."\n";
             }
         }
     }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $field = !empty($_GET['field']) ? addslashes($_GET['field']) : '';
    $report = new ContributionSources;
    $report->generateCSV($graph, $field);
}

?>