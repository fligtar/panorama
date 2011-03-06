<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class PerformanceStartupdistro extends Report {
    public $table = 'performance_startupdistro';
    
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
        elseif ($graph == 'addons-median') {
            echo "Label,tMain,tFirstPaint,tSessionRestored,Users\n";
            
            $_date = !empty($date) ? " AND date='".addslashes($date)."'" : '';
            $_row = $this->db->query_stats("SELECT distro FROM performance_addondistro WHERE app = '".addslashes($app)."'{$_date} ORDER BY date DESC LIMIT 1");
            $row = mysql_fetch_array($_row, MYSQL_ASSOC);
            $distro = json_decode($row['distro'], true);
            
            foreach ($distro as $addons => $measures) {
                $data[$addons] = array(
                    'tmain' => $measures['tmain']['median'],
                    'tfirstpaint' => $measures['tfirstpaint']['median'],
                    'tsessionrestored' => $measures['tsessionrestored']['median'],
                    'count' => $measures['tmain']['count']
                );
            }

            ksort($data);
            
            $i = 0;
            foreach ($data as $label => $columns) {
                if (!empty($limit) && $i >= $limit) break;
                
                echo "{$label},".implode(',', $columns)."\n";
                $i++;
            }
        }
        elseif ($graph == 'addons-avg') {
            echo "Label,tMain,tFirstPaint,tSessionRestored,Users\n";
            
            $_date = !empty($date) ? " AND date='".addslashes($date)."'" : '';
            $_row = $this->db->query_stats("SELECT distro FROM performance_addondistro WHERE app = '".addslashes($app)."'{$_date} ORDER BY date DESC LIMIT 1");
            $row = mysql_fetch_array($_row, MYSQL_ASSOC);
            $distro = json_decode($row['distro'], true);
            
            foreach ($distro as $addons => $measures) {
                $data[$addons] = array(
                    'tmain' => $measures['tmain']['avg'],
                    'tfirstpaint' => $measures['tfirstpaint']['avg'],
                    'tsessionrestored' => $measures['tsessionrestored']['avg'],
                    'count' => $measures['tmain']['count']
                );
            }

            ksort($data);
            
            $i = 0;
            foreach ($data as $label => $columns) {
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
            echo "Date,Start-ups Recorded\n";

            $dates = $this->db->query_stats("SELECT date, count FROM {$this->table} WHERE app = '".addslashes($app)."' AND os = '".addslashes($os)."' AND version = '".addslashes($version)."' ORDER BY date");
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