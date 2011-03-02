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
        return;
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        /**
         * This script is pretty ridiculous. Doing it the normal way eats up
         * more than 1GB of memory for just 1 million beta users, so I had
         * to improvise. This script:
         *  1. Reads through the raw perf data and stores all of the tImpact
         *     (tSessionRestored - tMain) times in an array
         *  2. Sorts the array and identifies the top 10% and bottom 10% of
         *     start-up times
         *  3. Notes the slowest tImpact counted in the top 10% and the fastest
         *     tImpact counted in the bottom 10% and throws everything else away
         *  4. Re-reads the raw perf data and stores the GUIDs only for tImpacts
         *     that are in the top and bottom ranges we're looking for
         *  5. For each GUID in the bottom 10%, determine if it is also in the
         *     top 10%. If it isn't, it's a suspicious GUID.
         *  6. Save all the suspicious GUIDs to the db for that app / os / version
         */
        
        // Begin #1
        $apps = array();
        
        $data_dir = HADOOP_DATA.'/'.$date;
        $file = fopen($data_dir.'/metadata-perf.txt', 'r');
        while (($line = fgets($file)) !== false) {
            if (empty($line)) continue;
            $columns = explode("\t", $line);
            
            /* Column order: guid, src, appos, appversion, tmain, tfirstpaint, tsessionrestored */
            // Set up app array if new app
            if (!array_key_exists($columns[1], $apps))
                $apps[$columns[1]] = array();
            
            // Set up OS array if new OS
            if (!array_key_exists($columns[2], $apps[$columns[1]]))
                $apps[$columns[1]][$columns[2]] = array();
            
            // Set up appversion array if new appversion
            if (!array_key_exists($columns[3], $apps[$columns[1]][$columns[2]]))
                $apps[$columns[1]][$columns[2]][$columns[3]] = array();
            
            $timpact = $columns[6] - $columns[4];
            if (is_numeric($timpact) && $timpact < 3600000 && $timpact >= 0)
                $apps[$columns[1]][$columns[2]][$columns[3]][] = $timpact;
        }
        fclose($file);
        
        // Begin #2
        foreach ($apps as $app => $oses) {
            foreach ($oses as $os => $versions) {
                foreach ($versions as $version => $data) {
                    // We aren't interested in combinations with fewer than 1000 users
                    if (count($data) < 1000) {
                        $this->log("{$date} - Combination omitted ({$app}/{$os}/{$version})");
                        continue;
                    }
                    
                    // Sort by times 
                    sort($data);
                
                    // Get the top and bottom 10%
                    $count = ceil(count($data) * .10);
                    $top = array_slice($data, 0, $count);
                    $bottom = array_slice($data, 0 - $count, $count);
                    
                    // Begin #3
                    $apps[$app][$os][$version] = array();
                    $apps[$app][$os][$version]['top_below'] = $top[count($top) - 1];
                    $apps[$app][$os][$version]['bottom_above'] = $bottom[0];
                }
            }
        }
        
        // Begin #4
        $file = fopen($data_dir.'/metadata-perf.txt', 'r');
        while (($line = fgets($file)) !== false) {
            if (empty($line)) continue;
            $columns = explode("\t", $line);
            
            if (empty($apps[$columns[1]][$columns[2]][$columns[3]]['top_below'])) continue;
            
            /* Column order: guid, src, appos, appversion, tmain, tfirstpaint, tsessionrestored */
            $timpact = $columns[6] - $columns[4];
            
            if ($timpact <= $apps[$columns[1]][$columns[2]][$columns[3]]['top_below'])
                $apps[$columns[1]][$columns[2]][$columns[3]]['top'][] = $columns[0];
                
            if ($timpact >= $apps[$columns[1]][$columns[2]][$columns[3]]['bottom_above'])
                $apps[$columns[1]][$columns[2]][$columns[3]]['bottom'][] = $columns[0];
        }
        fclose($file);
        
        // Begin #5
        foreach ($apps as $app => $oses) {
            foreach ($oses as $os => $versions) {
                foreach ($versions as $version => $data) {
                    if (empty($data['bottom'])) continue;
                    
                    $suspicious = array();
                    
                    $guids = array('top' => array(), 'bottom' => array());
                    // Record top guid occurrence
                    foreach ($data['top'] as $_guids) {
                        $_guids = explode(',', $_guids);
                        foreach ($_guids as $guid) {
                            if (!array_key_exists($guid, $guids['top']))
                                $guids['top'][$guid] = 1;
                            else
                                $guids['top'][$guid]++;
                        }
                    }
                    
                    // Record bottom guid occurrence
                    foreach ($data['bottom'] as $_guids) {
                        $_guids = explode(',', $_guids);

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
                    
                    // Begin #6
                    $qry = "INSERT INTO {$this->table} (date, app, os, version, timpact_suspicious) VALUES ('{$date}', '".addslashes($app)."', '".addslashes($os)."', '".addslashes($version)."', '".addslashes(json_encode($suspicious))."')";

                    if ($this->db->query_stats($qry))
                        $this->log("{$date} - Inserted row ({$app}/{$os}/{$version})");
                    else
                        $this->log("{$date} - Problem inserting row ({$app}/{$os}/{$version})".mysql_error());
                }
            }
        }

        $guids = null;
        $apps = null;
        $suspicious = null;
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
    //$report->analyzeDay('2011-01-28');exit;
    
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