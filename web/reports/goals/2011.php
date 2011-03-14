<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class Goals2011 extends Report {
    public $table = 'goals_2011';
    public $backfillable = true;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        $data = $this->getData();
        $this->sendData($data);     
    }
    
    public function getData() {
        header('Content-type: text/plain');
        $data = array();
        
        // Add-on creation last 30 days
        $_qry = $this->db->query_stats("SELECT SUM(type1) AS extensions_created, SUM(sdk) AS sdk_created, SUM(restartless) AS restartless_created FROM addons_creation WHERE date >= CURDATE() - INTERVAL 30 DAY");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        $data['extensions_created'] = array(
            '30days' => $_row['extensions_created'],
            'chart' => array()
        );
        $data['sdk_created'] = array(
            '30days' => $_row['sdk_created'],
            'chart' => array()
        );
        $data['restartless_created'] = array(
            '30days' => $_row['restartless_created'],
            'chart' => array()
        );
        
        // Add-on creation monthly history
        $_qry = $this->db->query_stats("SELECT LEFT(date, 7) AS month, SUM(type1) AS extensions_created, SUM(sdk) AS sdk_created, SUM(restartless) AS restartless_created FROM addons_creation WHERE date >= '2010' GROUP BY month ORDER BY month");
        while ($_row = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $year = substr($_row['month'], 0, 4);
            $data['extensions_created']['chart'][$year][] = $_row['extensions_created'];
            $data['sdk_created']['chart'][$year][] = $_row['sdk_created'];
            $data['restartless_created']['chart'][$year][] = $_row['restartless_created'];
        }
        
        // Ecosystem add-on usage last 30 days (desktop)
        $_qry = $this->db->query_stats("SELECT penetration, amo_active_adu, addons_installed FROM ecosystem_addonusage WHERE app = 'firefox' ORDER BY date DESC LIMIT 1");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        $data['penetration'] = array(
            'latest' => $_row['penetration'],
            'chart' => array()
        );
        $data['amo_percentage'] = array(
            'latest' => !empty($_row['addons_installed']) ? round(($_row['amo_active_adu'] / $_row['addons_installed']) * 100, 2) : 0,
            'chart' => array()
        );
        
        // Ecosystem add-on usage history (desktop)
        $_qry = $this->db->query_stats("SELECT date, penetration, amo_active_adu, addons_installed FROM ecosystem_addonusage WHERE app = 'firefox' ORDER BY date");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        while ($_row = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $data['penetration']['chart'][$_row['date']] = $_row['penetration'];
            $data['amo_percentage']['chart'][$_row['date']] = !empty($_row['addons_installed']) ? round(($_row['amo_active_adu'] / $_row['addons_installed']) * 100, 2) : 0;
        }
        
        // Ecosystem add-on usage last 30 days (mobile)
        $_qry = $this->db->query_stats("SELECT penetration_adu FROM ecosystem_addonusage WHERE app = 'mobile' ORDER BY date DESC LIMIT 1");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        $data['penetration_mobile'] = array(
            'latest' => $_row['penetration_adu'],
            'chart' => array()
        );
        
        // Ecosystem add-on usage history (mobile)
        $_qry = $this->db->query_stats("SELECT date, penetration_adu FROM ecosystem_addonusage WHERE app = 'mobile' ORDER BY date");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        while ($_row = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $data['penetration_mobile']['chart'][$_row['date']] = $_row['penetration_adu'];
        }
        
        //print_r($data);
        return $data;
    }
    
    public function sendData($data) {
        $file = HADOOP_DATA.'/goals-2011.json';
        file_put_contents($file, json_encode($data));
        
        $this->log(shell_exec("scp {$file} areweaddeduponyet@areweaddeduponyet.com:./panorama-drop/"));
        
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new Goals2011;
    $report->analyzeDay();
}

?>