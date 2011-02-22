<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class UserCreation extends Report {
    public $table = 'users_creation';
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
            'total' => "SELECT COUNT(*) FROM users WHERE DATE(created) = '%DATE%'",
            'new_extension_devs' => "SELECT COUNT(*) FROM users INNER JOIN addons_users ON users.id = addons_users.user_id INNER JOIN addons ON addons.id = addons_users.addon_id WHERE DATE(users.created) = '%DATE%' AND addons.addontype_id = 1",
            'new_nonpersona_devs' => "SELECT COUNT(*) FROM users INNER JOIN addons_users ON users.id = addons_users.user_id INNER JOIN addons ON addons.id = addons_users.addon_id WHERE DATE(users.created) = '%DATE%' AND addons.addontype_id != 9"
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
        echo "Date,New Users\n";

        $dates = $this->db->query_stats("SELECT date, total FROM {$this->table} ORDER BY date");
        while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
            echo implode(',', $date)."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new UserCreation;
    $report->generateCSV();
}

?>