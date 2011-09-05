<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonUpdatePings extends Report {
    public $table = 'addons_updatepings';
    public $backfillable = true;
    public $cron_type = 'yesterday';

    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $qry = "SELECT a.addontype_id, u.count, u.status, u.application, u.os, u.locale FROM update_counts AS u INNER JOIN addons AS a ON u.addon_id = a.id WHERE u.date = '%DATE%'";
        
        $_rows = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $applications = array(
            '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}' => 'Firefox',
            '{3550f703-e582-4d05-9a08-453d09bdfdc6}' => 'Thunderbird',
            '{a23983c0-fd0e-11dc-95ff-0800200c9a66}' => 'Mobile',
            '{92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}' => 'SeaMonkey',
            '{718e30fb-e89b-41dd-9da7-e25a45638b28}' => 'Sunbird'
        );
        
        $total = array('total' => 0);
        $pings = array();
        $columns = array('status', 'application', 'os', 'locale');
        while ($row = mysql_fetch_array($_rows, MYSQL_ASSOC)) {
            if (empty($total[$row['addontype_id']])) {
                $total[$row['addontype_id']] = 0;
            }
            $total['total'] += $row['count'];
            $total[$row['addontype_id']] += $row['count'];
            
            foreach ($columns as $column) {
                $data = unserialize($row[$column]);
                
                if (empty($pings[$row['addontype_id']][$column])) {
                    $pings[$row['addontype_id']][$column] = array();
                }
                if (empty($pings['total'][$column])) {
                    $pings['total'][$column] = array();
                }
                
                // If Applications, massage the data a bit
                if ($column == 'application') {
                    $newapps = array();
                    foreach ($data as $guid => $versions) {
                        if (empty($applications[$guid])) continue;
                        
                        foreach ($versions as $version => $count) {
                            $newversion = $applications[$guid].' '.$version;
                            if (empty($newapps[$newversion]))
                                $newapps[$newversion] = 0;
                            
                            $newapps[$newversion] += $count;
                        }
                    }
                    $data = $newapps;
                }
                
                foreach ($data as $k => $v) {
                    if (empty($pings[$row['addontype_id']][$column][$k])) {
                        $pings[$row['addontype_id']][$column][$k] = 0;
                    }
                    if (empty($pings['total'][$column][$k])) {
                        $pings['total'][$column][$k] = 0;
                    }
                    $pings['total'][$column][$k] += $v;
                    $pings[$row['addontype_id']][$column][$k] += $v;
                }
            }
        }
        
        // Clear out single pings
        foreach ($pings as $group => $c) {
            foreach ($c as $col => $keys) {
                foreach ($keys as $k => $v) {
                    arsort($pings[$group][$col]);
                    if ($v == 1)
                        unset($pings[$group][$col][$k]);
                }
            }
        }
        
        $qry = "INSERT INTO addons_updatepings (date, total) VALUES ('{$date}', {$total['total']})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted total row ({$total['total']} total)");
        else
            $this->log("{$date} - Problem inserting total row ({$total['total']} total)".mysql_error());
        
        foreach (array(1, 2, 3, 5) as $addontype) {
            $qry = "INSERT INTO addons_updatepings_addontypes (date, type, total, status, application, os, locale) VALUES ('{$date}', {$addontype}, {$total[$addontype]}, '".addslashes(json_encode($pings[$addontype]['status']))."', '".addslashes(json_encode($pings[$addontype]['application']))."', '".addslashes(json_encode($pings[$addontype]['os']))."', '".addslashes(json_encode($pings[$addontype]['locale']))."')";

            if ($this->db->query_stats($qry))
                $this->log("{$date} - Inserted row for add-on type {$addontype} ({$total[$addontype]} total)");
            else
                $this->log("{$date} - Problem inserting row for add-on type {$addontype} ({$total[$addontype]} total)".mysql_error());
        }
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
    $report = new AddonUpdatePings;
    //$report->generateCSV($graph);
    $report->analyzeDay('2011-08-02');
}

?>