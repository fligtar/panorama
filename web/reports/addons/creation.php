<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonCreation extends Report {
    public $table = 'addons_creation';
    public $backfillable = true;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
            
        $insert = array();
        //$update = array();

        $queries = array(
            'total' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%'",
            'type1' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 1",
            'type2' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 2",
            'type3' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 3",
            'type4' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 4",
            'type5' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 5",
            'type9' => "SELECT COUNT(*) FROM addons WHERE DATE(created) = '%DATE%' AND addontype_id = 9",
            'sdk' => "SELECT COUNT(*) FROM addons INNER JOIN versions ON addons.current_version = versions.id INNER JOIN files ON versions.id = files.version_id AND files.platform_id = 1 WHERE files.jetpack = 1 AND DATE(addons.created) = '%DATE%'",
            'restartless' => "SELECT COUNT(*) FROM addons INNER JOIN versions ON addons.current_version = versions.id INNER JOIN files ON versions.id = files.version_id AND files.platform_id = 1 WHERE files.no_restart = 1 AND DATE(addons.created) = '%DATE%'"
        );

        foreach ($queries as $queryname => $query) {
            $qry = str_replace('%DATE%', $date, $query);

            $_rows = $this->db->query_amo($qry);
            $row = mysql_fetch_array($_rows, MYSQL_NUM);

            if (empty($row[0]))
                $row[0] = 0;

            $insert["{$queryname}"] = $row[0];
            //$update[] = "{$queryname} = {$row[0]}";
        }

        $qry = "INSERT INTO {$this->table} (date, ".implode(', ', array_keys($insert)).") VALUES ('{$date}', ".implode(', ', $insert).")";
        //$qry = "UPDATE {$this->table} SET ".implode(', ', $update)." WHERE date = '{$date}'";
        
        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row");
        else
            $this->log("{$date} - Problem inserting row");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV() {
        echo "Date,All Types,Extensions,Themes,Dictionaries,Search Providers,Language Packs,Personas\n";

        $dates = $this->db->query_stats("SELECT date, total, type1, type2, type3, type4, type5, type9 FROM {$this->table} ORDER BY date");
        while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
            echo implode(',', $date)."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new AddonCreation;
    $report->generateCSV();
}

?>