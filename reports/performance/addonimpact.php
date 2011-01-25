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
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $file = file_get_contents($data_dir.'/metadata-perf.txt');
        $lines = explode("\n", $file);
        
        $apps = array();
        $master = array();
        $file = null;
        $i = 0;
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            $columns = explode("\t", $line);
            
            $master[$i] = $columns;
            
            /* Column order: timestamp, guids, app, os, appversion, tMain, tFirstPaint, tSessionRestored, date, domain */
            // Set up app array if new app
            if (!array_key_exists($columns[2], $apps))
                $apps[$columns[2]] = array();
            
            // Set up OS array if new OS
            if (!array_key_exists($columns[3], $apps[$columns[2]]))
                $apps[$columns[2]][$columns[3]] = array();
            
            // Set up appversion array if new appversion
            if (!array_key_exists($columns[4], $apps[$columns[2]][$columns[3]]))
                $apps[$columns[2]][$columns[3]][$columns[4]] = array(
                    'timpact' => array(),
                    'tmain' => array(),
                    'tfirstpaint' => array(),
                    'tsessionrestored' => array()
                );
            
            // Store reference to the master for each time
            $timpact = $columns[7] - $columns[5];
            if (is_numeric($timpact) && $timpact < 3600000 && $timpact >= 0)
                $apps[$columns[2]][$columns[3]][$columns[4]]['timpact'][$i] = $timpact;
                
            if (is_numeric($columns[5]) && $columns[5] < 3600000 && $columns[5] >= 0)
                $apps[$columns[2]][$columns[3]][$columns[4]]['tmain'][$i] = $columns[5];
            
            if (is_numeric($columns[6]) && $columns[6] < 3600000 && $columns[6] >= 0)
                $apps[$columns[2]][$columns[3]][$columns[4]]['tfirstpaint'][$i] = $columns[6];
            
            if (is_numeric($columns[7]) && $columns[7] < 3600000 && $columns[7] >= 0)
                $apps[$columns[2]][$columns[3]][$columns[4]]['tsessionrestored'][$i] = $columns[7];

            $i++;
        }
        $lines = null;
        
        foreach ($apps as $app => $oses) {
            foreach ($oses as $os => $versions) {
                foreach ($versions as $version => $data) {
                    $suspicious = array('timpact' => array(), 'tmain' => array(), 'tfirstpaint' => array(), 'tsessionrestored' => array());
                    
                    foreach (array_keys($suspicious) as $measure) {
                        // Sort by times 
                        asort($apps[$app][$os][$version][$measure]);
                    
                        // Get the top and bottom 10%
                        $count = ceil(count($apps[$app][$os][$version][$measure]) * .10);
                        $top = array_slice($apps[$app][$os][$version][$measure], 0, $count, true);
                        $bottom = array_slice($apps[$app][$os][$version][$measure], 0 - $count, $count, true);
                        $apps[$app][$os][$version][$measure] = null;
                        
                        $guids = array('top' => array(), 'bottom' => array());
                        // Record top guid occurrence
                        foreach ($top as $id => $measure_value) {
                            $_guids = explode(',', $master[$id][1]);
                            foreach ($_guids as $guid) {
                                if (!array_key_exists($guid, $guids['top']))
                                    $guids['top'][$guid] = 1;
                                else
                                    $guids['top'][$guid]++;
                            }
                        }
                        
                        // Record bottom guid occurrence
                        foreach ($bottom as $id => $measure_value) {
                            $_guids = explode(',', $master[$id][1]);

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
                                $suspicious[$measure][urldecode($guid)] = array(
                                    'count' => $count,
                                    'topcount' => 0,
                                    'x' => $count
                                );
                            else {
                                $m = round($count / $guids['top'][$guid], 2);
                                if ($m >= 2)
                                    $suspicious[$measure][urldecode($guid)] = array(
                                        'count' => $count,
                                        'topcount' => $guids['top'][$guid],
                                        'x' => $m
                                    );
                            }
                        }
                        uasort($suspicious[$measure], array('PerformanceAddonimpact', 'compareSuspicious'));

                        // We only save the top 100 for space reasons
                        $suspicious[$measure] = array_slice($suspicious[$measure], 0, 100, true);
                    }

                    $qry = "INSERT INTO {$this->table} (date, app, os, version, timpact_suspicious, tmain_suspicious, tfirstpaint_suspicious, tsessionrestored_suspicious) VALUES ('{$date}', '".addslashes($app)."', '".addslashes($os)."', '".addslashes($version)."', '".json_encode($suspicious['timpact'])."', '".json_encode($suspicious['tmain'])."', '".json_encode($suspicious['tfirstpaint'])."', '".json_encode($suspicious['tsessionrestored'])."')";

                    if ($this->db->query_stats($qry))
                        $this->log("{$date} - Inserted row ({$app}/{$os}/{$version})");
                    else
                        $this->log("{$date} - Problem inserting row ({$app}/{$os}/{$version})".mysql_error());
                }
            }
        }
        
        $guids = null;
        $apps = null;
        $master = null;
    }
    
    public function compareSuspicious($a, $b) {
        if ($a['x'] == $b['x'])
            return 0;
        
        return ($a['x'] < $b['x']) ? 1 : -1;
    }
    
    /**
     * Generate the HTML
     */
    public function generateHTML() {
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
        
        $measure_pretty = array(
            'tmain_suspicious' => 'tMain Suspicious Add-ons',
            'tfirstpaint_suspicious' => 'tFirstPaint Suspicious Add-ons',
            'tsessionrestored_suspicious' => 'tSessionRestored Suspicious Add-ons'
        );
        
        $reports = array(
            'Firefox / WINNT / 4.0b10pre' => array('app' => 'firefox', 'os' => 'WINNT', 'version' => '4.0b10pre'),
            'Firefox / Darwin / 4.0b10pre' => array('app' => 'firefox', 'os' => 'Darwin', 'version' => '4.0b10pre'),
            'Firefox / Linux / 4.0b10pre' => array('app' => 'firefox', 'os' => 'Linux', 'version' => '4.0b10pre'),
            'Mobile / Android / 4.0b4pre' => array('app' => 'mobile', 'os' => 'Android', 'version' => '4.0b4pre')
        );
        
        foreach ($reports as $report_name => $where) {
            echo '<div class="report-section">';
            echo '<h3>'.$report_name.'</h3>';
            echo '<h4>(tSessionRestored - tMain) Suspicious Add-ons</h4>';
            $_qry = $this->db->query_stats("SELECT timpact_suspicious FROM {$this->table} WHERE app = '{$where['app']}' AND os = '{$where['os']}' AND version = '{$where['version']}' ORDER BY date DESC LIMIT 1");
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
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new PerformanceAddonimpact;
    $report->generateHTML();
}

?>