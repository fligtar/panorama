<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class EcosystemAddonusage extends Report {
    public $table = 'ecosystem_addonusage';
    
    /**
     * Output the available filters for app, os, and version
     */
    public function outputFilterJSON() {
        $filters = array(
            'app' => array()
        );
        
        $_apps = $this->db->query_stats("SELECT DISTINCT app FROM {$this->table} ORDER BY app");
        while ($app = mysql_fetch_array($_apps, MYSQL_ASSOC)) $filters['app'][] = $app['app'];
        
        echo json_encode($filters);
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph, $app, $limit = 0) {
        header('Content-type: text/plain');
        
        if ($graph == 'distro') {
            echo "Label,Count\n";

            $_values = $this->db->query_stats("SELECT distro FROM {$this->table} WHERE app='".addslashes($app)."' ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            $distro = json_decode($values['distro']);
            
            $i = 0;
            foreach ($distro as $installed => $count) {
                if (!empty($limit) && $i >= $limit) break;
                
                echo "{$installed},{$count}\n";
                $i++;
            }
        }
        elseif ($graph == 'users') {
            echo "Date,Application ADU,Default Theme Users,Users with an Add-on,Penetration (based on ADU)";
            if ($app != 'mobile')
                echo ",Penetration (based on default theme)";
            echo "\n";
            
            $_app = $app;
            switch($app) {
                case 'firefox':
                    $version = '4.0';
                    break;
                case 'mobile':
                    $version = '4.0';
                    $_app = 'fennec';
                    break;
                case 'seamonkey':
                    $version = '2.1';
                    break;
            }
            
            $adu = array();
            $_adu = $this->db->query_metrics("SELECT date, SUM(adu_count) as adu_count FROM raw_adu WHERE product_name='{$_app}' AND product_version >= '{$version}' GROUP BY date");
            while ($row = mysql_fetch_array($_adu)) {
                $adu[$row['date']] = $row['adu_count'];
            }

            $dates = $this->db->query_stats("SELECT ea.date AS 0_date, users_with_addons AS 3_users_with_addons, (penetration * 100) AS 5_penetration, (penetration_adu * 100) AS 4_penetration_adu, et.installs AS 2_default_theme FROM {$this->table} as ea INNER JOIN ecosystem_topaddons AS et ON ea.date = et.date AND et.guid = '{972ce4c6-7e08-4474-a285-3208198ce6fd}' WHERE ea.app='".addslashes($app)."' ORDER BY ea.date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                $date['1_adu'] = $adu[$date['0_date']];
                ksort($date);
                if ($app == 'mobile')
                    array_pop($date);
                echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'total-usage') {
            echo "Date,Active Daily Add-ons,User-Installed ADA,Known to AMO,Active on AMO\n";

            $dates = $this->db->query_stats("SELECT date, addons_installed_all, addons_installed, amo_known_adu, amo_active_adu FROM {$this->table} WHERE app='".addslashes($app)."' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'total-counts') {
            echo "Date,Unique GUIDs,Known to AMO,Active on AMO\n";

            $dates = $this->db->query_stats("SELECT date, unique_guids, amo_known_count, amo_active_count FROM {$this->table} WHERE app='".addslashes($app)."' AND date > '2011-02-18' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
        elseif ($graph == 'avg') {
            echo "Date,Average Installed Add-ons\n";

            $dates = $this->db->query_stats("SELECT date, average_installed FROM {$this->table} WHERE app='".addslashes($app)."' ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new EcosystemAddonusage;
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    if ($action == 'graph') {
        $graph = !empty($_GET['graph']) ? $_GET['graph'] : '';
        $app = !empty($_GET['app']) ? $_GET['app'] : '';
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 0;
        $report->generateCSV($graph, $app, $limit);
    }
    elseif ($action == 'filters') {
        $report->outputFilterJSON();
    }
}

?>