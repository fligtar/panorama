<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class PerformanceAddonimpact extends Report {
    public $table = 'performance_addonimpact';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        memory();
        $apps = array();
        $master = array();
        $i = 0;
        
        /* We store all of the individual rows in a master array with unique id for lookup later
             master[0] => 'guid1,guid2,guid3'
             
           We then store the tImpact (tSessionRestored - tMain) with the master id
             apps[firefox][WINNT][4.0b10][0] => 1234
                 (1234ms tImpact for the master id 0)
        */
        $data_dir = HADOOP_DATA.'/'.$date;
        $file = fopen($data_dir.'/metadata-perf.txt', 'r');
        while (($line = fgets($file)) !== false) {
            if (empty($line)) continue;
            $columns = explode("\t", $line);
            
            $master[$i] = $columns[1];
            
            /* Column order: timestamp, guids, app, os, appversion, tMain, tFirstPaint, tSessionRestored, date, domain */
            // Set up app array if new app
            if (!array_key_exists($columns[2], $apps))
                $apps[$columns[2]] = array();
            
            // Set up OS array if new OS
            if (!array_key_exists($columns[3], $apps[$columns[2]]))
                $apps[$columns[2]][$columns[3]] = array();
            
            // Set up appversion array if new appversion
            if (!array_key_exists($columns[4], $apps[$columns[2]][$columns[3]]))
                $apps[$columns[2]][$columns[3]][$columns[4]] = array();
            
            // Store reference to the master for each time
            $timpact = $columns[7] - $columns[5];
            if (is_numeric($timpact) && $timpact < 3600000 && $timpact >= 0)
                $apps[$columns[2]][$columns[3]][$columns[4]][$i] = $timpact;

            $i++;
        }
        memory('first part done');
        fclose($file);
        
        /* Now that the pings are all sorted into app, OS, and version, 
           we can make the total calculations and store them */
        foreach ($apps as $app => $oses) {
            foreach ($oses as $os => $versions) {
                foreach ($versions as $version => $data) {
                    memory('starting '.$app.' '.$os.' '.$version);
                    // Free up some memory
                    $apps[$app][$os][$version] = null;
                    
                    // We aren't interested in combinations with fewer than 1000 users
                    if (count($data) < 1000) {
                        $this->log("{$date} - Combination omitted ({$app}/{$os}/{$version})");
                        continue;
                    }
                    
                    $suspicious = array();
                    
                    // Sort by times 
                    asort($data);
                
                    // Get the top and bottom 10%
                    $count = ceil(count($data) * .10);
                    $top = array_slice($data, 0, $count, true);
                    $bottom = array_slice($data, 0 - $count, $count, true);
                    $data = null;
                    
                    $guids = array('top' => array(), 'bottom' => array());
                    // Record top guid occurrence
                    foreach ($top as $id => $measure_value) {
                        $_guids = explode(',', $master[$id]);
                        foreach ($_guids as $guid) {
                            if (!array_key_exists($guid, $guids['top']))
                                $guids['top'][$guid] = 1;
                            else
                                $guids['top'][$guid]++;
                        }
                    }
                    
                    // Record bottom guid occurrence
                    foreach ($bottom as $id => $measure_value) {
                        $_guids = explode(',', $master[$id]);

                        foreach ($_guids as $guid) {
                            if (!array_key_exists($guid, $guids['bottom']))
                                $guids['bottom'][$guid] = 1;
                            else
                                $guids['bottom'][$guid]++;
                        }
                    }
                    arsort($guids['bottom']);
                    
                    // For each bottom guid, see if it occurs just as often in top guids
                    foreach ($guids['bottom'] as $guid => $count) {
                        if ($count <= 1) continue;
                        if (is_numeric($guid)) continue; // Not sure what the numeric guids like '23' are
                        
                        if (!array_key_exists($guid, $guids['top']))
                            $suspicious[urldecode($guid)] = array(
                                'count' => $count,
                                'topcount' => 0,
                                'x' => $count
                            );
                        else {
                            $m = round($count / $guids['top'][$guid], 2);
                            if ($m >= 2)
                                $suspicious[urldecode($guid)] = array(
                                    'count' => $count,
                                    'topcount' => $guids['top'][$guid],
                                    'x' => $m
                                );
                        }
                    }
                    uasort($suspicious, array('PerformanceAddonimpact', 'compareSuspicious'));

                    // We only save the top 100 for space reasons
                    $suspicious = array_slice($suspicious, 0, 100, true);

                    $qry = "INSERT INTO {$this->table} (date, app, os, version, timpact_suspicious) VALUES ('{$date}', '".addslashes($app)."', '".addslashes($os)."', '".addslashes($version)."', '".json_encode($suspicious)."')";

                    if ($this->db->query_stats($qry))
                        $this->log("{$date} - Inserted row ({$app}/{$os}/{$version})");
                    else
                        $this->log("{$date} - Problem inserting row ({$app}/{$os}/{$version})".mysql_error());
                }
            }
        }
        memory();
        $guids = null;
        $apps = null;
        $master = null;
        $suspicious = null;
        memory();
    }
    
    public function compareSuspicious($a, $b) {
        if ($a['x'] == $b['x'])
            return 0;
        
        return ($a['x'] < $b['x']) ? 1 : -1;
    }
    
    /**
     * Output the available filters for app, os, and version
     */
     public function outputFilterJSON() {
         $filters = array(
             'app' => array(),
             'os' => array(),
             'version' => array(),
             'date' => array()
         );
     
         $_apps = $this->db->query_stats("SELECT DISTINCT app FROM {$this->table} ORDER BY app");
         while ($app = mysql_fetch_array($_apps, MYSQL_ASSOC)) $filters['app'][] = $app['app'];
     
         $_oses = $this->db->query_stats("SELECT DISTINCT os FROM {$this->table} ORDER BY os");
         while ($os = mysql_fetch_array($_oses, MYSQL_ASSOC)) $filters['os'][] = $os['os'];
     
         $_versions = $this->db->query_stats("SELECT DISTINCT version FROM {$this->table} ORDER BY version");
         while ($version = mysql_fetch_array($_versions, MYSQL_ASSOC)) $filters['version'][] = $version['version'];
     
         $_dates = $this->db->query_stats("SELECT DISTINCT date FROM {$this->table} ORDER BY date DESC");
         while ($date = mysql_fetch_array($_dates, MYSQL_ASSOC)) $filters['date'][] = $date['date'];
     
         echo json_encode($filters);
     }
    
    /**
     * Generate the HTML
     */
    public function generateHTML($date, $app, $os, $version) {
        $amo_statuses = array(
            'Incomplete',
            'Unreviewed',
            'Pending (Files only)',
            'Awaiting Review',
            'Fully Reviewed',
            'Admin Disabled',
            'Self-hosted',
            'Beta (Files only)',
            'Preliminarily Reviewed',
            'Preliminarily Reviewed and Awaiting Full Review',
            'Purgatory'
        );
        
        echo '<div class="report-section">';
        echo "<h3>{$date} / {$app} / {$os} / {$version}</h3>";
        echo '<h4>(tSessionRestored - tMain) Suspicious Add-ons</h4>';
        
        $_date = !empty($date) ? " AND date='".addslashes($date)."'" : '';
        $_qry = $this->db->query_stats("SELECT timpact_suspicious FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."'{$_date} ORDER BY date DESC LIMIT 1");
        $values = mysql_fetch_array($_qry, MYSQL_ASSOC);
        $suspicious = json_decode($values['timpact_suspicious'], true);
        
        $i = 1;
        echo '<dl>';
        foreach ($suspicious as $guid => $data) {
            if ($i > 30) break;
                
            $_qry = $this->db->query_amo("SELECT addons.id, addons.status, translations.localized_string as name FROM addons INNER JOIN translations ON translations.id=addons.name AND translations.locale=addons.defaultlocale WHERE addons.guid='".addslashes($guid)."'");
            if (mysql_num_rows($_qry) > 0) {
                $name = mysql_fetch_array($_qry, MYSQL_ASSOC);
                
                echo '<dt>'.$i.'. <span><a href="https://addons.mozilla.org/addon/'.$name['id'].'" title="'.$guid.'" target="_blank">'.$name['name'].'</a></span> - AMO: '.$amo_statuses[$name['status']].'</dt>';
            }
            else
                echo '<dt>'.$i.'. <span><a href="http://www.google.com/search?q='.$guid.'" target="_blank">'.$guid.'</a></span></dt>';
            echo '<dd>'.$data['x'].'x more in bottom 10% than top ('.$data['count'].' vs. '.$data['topcount'].')</dd>';
            
            if ($i % 10 == 0)
                echo '</dl><dl>';
            $i++;
        }
        echo '</dl>';
        echo '</div>';

    }
}

// If this is not being controlled by something else, output the HTML by default
if (!defined('OVERLORD')) {
    $report = new PerformanceAddonimpact;
    $report->analyzeDay('2011-01-27');exit;
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    if ($action == 'html') {
        $date = !empty($_GET['date']) ? $_GET['date'] : '';
        $app = !empty($_GET['app']) ? $_GET['app'] : '';
        $os = !empty($_GET['os']) ? $_GET['os'] : '';
        $version = !empty($_GET['version']) ? $_GET['version'] : '';
        $report->generateHTML($date, $app, $os, $version);
    }
    elseif ($action == 'filters') {
        $report->outputFilterJSON();
    }
}

?>