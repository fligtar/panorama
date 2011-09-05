<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonThemeUsage extends Report {
    public $table = 'addons_updatepings_details';
    public $backfillable = false;
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV() {
        
        $dates = array();
        
        $qry = $this->db->query_stats("SELECT date, status FROM addons_updatepings_addontypes WHERE type = 2 AND date >= '2008-07-09'");
        while ($r = mysql_fetch_array($qry, MYSQL_ASSOC)) {
            if (empty($dates[$r['date']]))
                $dates[$r['date']] = array();
            $status = json_decode($r['status'], true);
            $dates[$r['date']]['enabled'] = $status['userEnabled'];
        }
        
        $_adu = $this->db->query_metrics("SELECT date, SUM(adu_count) as adu_count FROM raw_adu WHERE product_name='Firefox' GROUP BY date");
        while ($row = mysql_fetch_array($_adu)) {
            if (!empty($dates[$row['date']]))
                $dates[$row['date']]['adu'] = $row['adu_count'];
        }
        
        echo "Date,Themes in Use,Firefox ADU,Percentage\n";
        foreach ($dates as $date => $data) {
            $adu = (!empty($data['adu']) ? $data['adu'] : 0);
            $p = (!empty($adu) ? round($data['enabled'] / $adu * 100, 2) : 0);
            echo $date.','.$data['enabled'].','.$adu.','.$p."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new AddonThemeUsage;
    $report->generateCSV();
}

?>