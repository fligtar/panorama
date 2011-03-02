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
        $data = array(
            'addons_creation' => array(
                '30days' => array(),
                'history' => array()
            )
        );
        
        // Add-on creation last 30 days
        $_qry = $this->db->query_stats("SELECT SUM(type1) AS extensions_created, SUM(sdk) AS sdk_created, SUM(restartless) AS restartless_created FROM addons_creation WHERE date >= CURDATE() - INTERVAL 30 DAY");
        $_row = mysql_fetch_array($_qry, MYSQL_ASSOC);
        $data['addons_creation']['30days'] = $_row;
        
        // Add-on creation monthly history
        $_qry = $this->db->query_stats("SELECT LEFT(date, 7) AS month, SUM(type1) AS extensions_created, SUM(sdk) AS sdk_created, SUM(restartless) AS restartless_created FROM addons_creation WHERE date >= '2010' GROUP BY month ORDER BY month");
        while ($_row = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $data['addons_creation']['history'][$_row['month']] = $_row;
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