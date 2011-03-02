<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class PerformanceStartupdistro extends Report {
    public $table = 'performance_startupdistro2';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        return;
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $apps = array();
        
        /* We read the raw perf file and split the data into an array like:
             apps[firefox][WINNT][4.0b10][tmain][3] => 1234 
                 (1234 users started tMain in 3 seconds)
                 
             apps[firefox][WINNT][4.0b10][tmain_all] => (4234, 2342, 4234, 2344, ...)
                 (the individual tMain times for an average)
        */
        $data_dir = HADOOP_DATA.'/'.$date;
        $file = fopen($data_dir.'/metadata-perf.txt', 'r');
        while (($line = fgets($file)) !== false) {
            if (empty($line)) continue;
            $line = trim($line);
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
                $apps[$columns[1]][$columns[2]][$columns[3]] = array(
                    'tmain_all' => array(),
                    'tmain' => array(),
                    'tfirstpaint_all' => array(),
                    'tfirstpaint' => array(),
                    'tsessionrestored_all' => array(),
                    'tsessionrestored' => array(),
                    'count' => 0,
                    'addons' => 0
                );
            
            $apps[$columns[1]][$columns[2]][$columns[3]]['count']++;
            $apps[$columns[1]][$columns[2]][$columns[3]]['addons'] += substr_count($columns[0], ',') + 1;
            
            if (is_numeric($columns[4]) && $columns[4] < 3600000 && $columns[4] >= 0) {
                $apps[$columns[1]][$columns[2]][$columns[3]]['tmain_all'][] = $columns[4];
                $round = round($columns[4] / 1000, 0);
                if (!empty($apps[$columns[1]][$columns[2]][$columns[3]]['tmain'][$round]))
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tmain'][$round]++;
                else
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tmain'][$round] = 1;
            }
            
            if (is_numeric($columns[5]) && $columns[5] < 3600000 && $columns[5] >= 0) {
                $apps[$columns[1]][$columns[2]][$columns[3]]['tfirstpaint_all'][] = $columns[5];
                $round = round($columns[5] / 1000, 0);
                if (!empty($apps[$columns[1]][$columns[2]][$columns[3]]['tfirstpaint'][$round]))
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tfirstpaint'][$round]++;
                else
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tfirstpaint'][$round] = 1;
            }
            
            if (is_numeric($columns[6]) && $columns[6] < 3600000 && $columns[6] >= 0) {
                $apps[$columns[1]][$columns[2]][$columns[3]]['tsessionrestored_all'][] = $columns[6];
                $round = round($columns[6] / 1000, 0);
                if (!empty($apps[$columns[1]][$columns[2]][$columns[3]]['tsessionrestored'][$round]))
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tsessionrestored'][$round]++;
                else
                    $apps[$columns[1]][$columns[2]][$columns[3]]['tsessionrestored'][$round] = 1;
            }
        }
        fclose($file);
        
        /* Now that the pings are all sorted into app, OS, and version, 
           we can make the total calculations and store them */
        foreach ($apps as $app => $oses) {
            foreach ($oses as $os => $versions) {
                foreach ($versions as $version => $data) {
                    // We aren't interested in combinations with fewer than 10 users
                    if ($data['count'] < 10 || (empty($data['tmain_all']) && empty($data['tfirstpaint_all']) && empty($data['tsessionrestored_all']))) {
                        $this->log("{$date} - Combination omitted ({$app}/{$os}/{$version})");
                        continue;
                    }
                    
                    sort($data['tmain_all']);
                    sort($data['tfirstpaint_all']);
                    sort($data['tsessionrestored_all']);
                    
                    $data['tmain_avg'] = round(array_sum($data['tmain_all']) / count($data['tmain_all']), 0);
                    $data['tfirstpaint_avg'] = round(array_sum($data['tfirstpaint_all']) / count($data['tfirstpaint_all']), 0);
                    $data['tsessionrestored_avg'] = round(array_sum($data['tsessionrestored_all']) / count($data['tsessionrestored_all']), 0);
                    
                    $data['tmain_median'] = $data['tmain_all'][floor(count($data['tmain_all']) / 2)];
                    $data['tfirstpaint_median'] = $data['tfirstpaint_all'][floor(count($data['tfirstpaint_all']) / 2)];
                    $data['tsessionrestored_median'] = $data['tsessionrestored_all'][floor(count($data['tsessionrestored_all']) / 2)];
                    
                    ksort($data['tmain']);
                    ksort($data['tfirstpaint']);
                    ksort($data['tsessionrestored']);
                    
                    $qry = "INSERT INTO {$this->table} (date, app, os, version, count, addons, tmain_avg, tmain_median, tmain_seconds_distro, tfirstpaint_avg, tfirstpaint_median, tfirstpaint_seconds_distro, tsessionrestored_avg, tsessionrestored_median,  tsessionrestored_seconds_distro) VALUES ('{$date}', '".addslashes($app)."', '".addslashes($os)."', '".addslashes($version)."', {$data['count']}, {$data['addons']}, {$data['tmain_avg']}, {$data['tmain_median']}, '".json_encode($data['tmain'])."', {$data['tfirstpaint_avg']}, {$data['tfirstpaint_median']}, '".json_encode($data['tfirstpaint'])."', {$data['tsessionrestored_avg']}, {$data['tsessionrestored_median']}, '".json_encode($data['tsessionrestored'])."')";

                    if ($this->db->query_stats($qry))
                        $this->log("{$date} - Inserted row ({$app}/{$os}/{$version})");
                    else
                        $this->log("{$date} - Problem inserting row ({$app}/{$os}/{$version})".mysql_error());
                }
            }
        }
        $apps = null;
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
     * Generate the CSV for graphs
     */
    public function generateCSV($graph, $date, $app, $os, $version, $limit = 0) {
        header('Content-type: text/plain');
        
        if ($graph == 'distro-sec') {
            echo "Label,tMain,tFirstPaint,tSessionRestored\n";
            
            $_date = !empty($date) ? " AND date='".addslashes($date)."'" : '';
            $_values = $this->db->query_stats("SELECT tmain_seconds_distro, tfirstpaint_seconds_distro, tsessionrestored_seconds_distro FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."'{$_date} ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            $distro = array();
            
            foreach ($values as $column => $data) {
                $data = json_decode($data, true);
                
                foreach ($data as $seconds => $count) {
                    if (empty($distro[$seconds]))
                        $distro[$seconds] = array(
                            'tmain_seconds_distro' => 0,
                            'tfirstpaint_seconds_distro' => 0,
                            'tsessionrestored_seconds_distro' => 0
                        );

                    $distro[$seconds][$column] += $count;
                }
            }
            ksort($distro);
            
            $i = 0;
            foreach ($distro as $label => $columns) {
                if (!empty($limit) && $i >= $limit) break;
                
                echo "{$label},".implode(',', $columns)."\n";
                $i++;
            }
        }
        elseif ($graph == 'distro-min') {
            echo "Label,tMain,tFirstPaint,tSessionRestored\n";
            
            $_values = $this->db->query_stats("SELECT tmain_seconds_distro, tfirstpaint_seconds_distro, tsessionrestored_seconds_distro FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."' ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            $distro = array();
            
            foreach ($values as $column => $data) {
                $data = json_decode($data, true);
                
                foreach ($data as $seconds => $count) {
                    $minutes = floor($seconds / 60);
                    
                    if (empty($distro[$minutes]))
                        $distro[$minutes] = array(
                            'tmain_seconds_distro' => 0,
                            'tfirstpaint_seconds_distro' => 0,
                            'tsessionrestored_seconds_distro' => 0
                        );
                    
                    $distro[$minutes][$column] += $count;
                }
            }
            ksort($distro);
            
            $i = 0;
            foreach ($distro as $label => $columns) {
                if (!empty($limit) && $i >= $limit) break;
                
                echo "{$label},".implode(',', $columns)."\n";
                $i++;
            }
        }
        elseif ($graph == 'average') {
            echo "Date,tMain,tFirstPaint,tSessionRestored\n";

            $dates = $this->db->query_stats("SELECT date, tmain_avg, tfirstpaint_avg, tsessionrestored_avg FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'median') {
            echo "Date,tMain,tFirstPaint,tSessionRestored\n";

            $dates = $this->db->query_stats("SELECT date, tmain_median, tfirstpaint_median, tsessionrestored_median FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."' AND date >= '2011-02-08' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'count') {
            echo "Date,Start-ups Recorded,Add-ons Installed\n";

            $dates = $this->db->query_stats("SELECT date, count, addons FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new PerformanceStartupdistro;
    //$report->analyzeDay('2011-03-01');exit;
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    if ($action == 'graph') {
        $graph = !empty($_GET['graph']) ? $_GET['graph'] : '';
        $app = !empty($_GET['app']) ? $_GET['app'] : '';
        $os = !empty($_GET['os']) ? $_GET['os'] : '';
        $version = !empty($_GET['version']) ? $_GET['version'] : '';
        $date = !empty($_GET['date']) ? $_GET['date'] : '';
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 0;
        $report->generateCSV($graph, $date, $app, $os, $version, $limit);
    }
    elseif ($action == 'filters') {
        $report->outputFilterJSON();
    }
}

?>