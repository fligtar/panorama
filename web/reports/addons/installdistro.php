<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonInstalled extends Report {
    public $table = 'addons_installed';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        return;
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $data = file_get_contents($data_dir.'/metadata-installed-fx.txt');
        $data = explode("\n", $data);
        /*$qry = "SELECT distro FROM old_addons_installdistro WHERE date = '%DATE%'";
        
        $import = $this->db->query_stats(str_replace('%DATE%', $date, $qry));
        $installed = mysql_fetch_array($import);
        $installed = json_decode($installed[0], true);*/
        
        foreach ($data as $val) {
            if (empty($val)) continue;
            $s = explode("\t", $val);
            $installed[$s[0]] = $s[1];
        }
        
        $insert['users_with_addons'] = array_sum($installed);
        
        $insert['addons_installed'] = 0;
        foreach ($installed as $num => $count) {
            $insert['addons_installed'] += $num * $count;
        }
        $insert['average_installed'] = round($insert['addons_installed'] / $insert['users_with_addons'], 0);
        
        $qry = "SELECT adu_count FROM raw_adu WHERE date = '%DATE%' AND product_name = 'Firefox' AND product_version = '4.0'";
        
        $_adu = $this->db->query_metrics(str_replace('%DATE%', $date, $qry));
        $adu = mysql_fetch_array($_adu);
        $insert['penetration'] = round($insert['users_with_addons'] / $adu[0], 2);
        
        $qry = "INSERT INTO {$this->table} (date, addons_installed, average_installed, users_with_addons, penetration, distro) VALUES ('{$date}', {$insert['addons_installed']}, {$insert['average_installed']}, {$insert['users_with_addons']}, {$insert['penetration']} '".json_encode($installed)."')";
        echo $qry;exit;
        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV($graph) {
        if ($graph == 'current') {
            echo "Label,Count\n";

            $_values = $this->db->query_stats("SELECT distro FROM {$this->table} ORDER BY date DESC LIMIT 1");
            $values = mysql_fetch_array($_values, MYSQL_ASSOC);
            $distro = json_decode($values['distro']);

            foreach ($distro as $installed => $count) {
                echo "{$installed},{$count}\n";
            }
        }
        elseif ($graph == 'history') {
            echo "Date,Total Installed,Average Installed\n";

            $dates = $this->db->query_stats("SELECT date, total, average FROM {$this->table} ORDER BY date");
            while ($date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
                echo implode(',', $date)."\n";
            }
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $graph = !empty($_GET['graph']) ? $_GET['graph'] : 'current';
    $report = new AddonInstalled;
    $report->generateCSV($graph);
}

?>