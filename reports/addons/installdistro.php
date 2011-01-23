<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class AddonInstalldistro extends Report {
    public $table = 'addons_installdistro';
    public $backfillable = true;
    public $cron_type = 'yesterday';
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d', strtotime('yesterday'));
        
        $data_dir = HADOOP_DATA.'/'.$date;
        
        $data = file_get_contents($data_dir.'/metadata-installed-distro.txt');
        $data = explode("\n", $data);
        
        foreach ($data as $val) {
            if (empty($val)) continue;
            $s = explode("\t", $val);
            $installed[$s[0]] = $s[1];
        }
        
        $total = array_sum($installed);
        
        $subtotal = 0;
        foreach ($installed as $num => $count) {
            $subtotal += $num * $count;
        }
        $average = round($subtotal / $total, 0);
        
        $qry = "INSERT INTO {$this->table} (date, total, average, distro) VALUES ('{$date}', {$total}, {$average}, '".json_encode($installed)."')";
        
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
    $report = new AddonInstalldistro;
    $report->generateCSV($graph);
}

?>