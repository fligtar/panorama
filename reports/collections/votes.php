<?php
require_once dirname(dirname(dirname(__FILE__))).'/lib/report.class.php';

class CollectionVotes extends Report {
    public $table = 'collections_votes';
    public $backfillable = true;
    
    /**
     * Called daily
     */
    public function daily() {
        $this->analyzeDay();
    }
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $qry = "SELECT collection_type, COUNT(*) FROM collections INNER JOIN collections_votes ON collections_votes.collection_id = collections.id WHERE DATE(collections_votes.created) = '%DATE%' GROUP BY collection_type ORDER BY collection_type";
        
        $_types = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $fields = '';
        $values = '';
        $total = 0;
        if (mysql_num_rows($_types) > 0) {
            while ($type = mysql_fetch_array($_types, MYSQL_NUM)) {
                $fields .= ", type{$type[0]}";
                $values .= ','.$type[1];
                $total += $type[1];
            }
        }
        
        $qry = "INSERT INTO {$this->table} (date, total{$fields}) VALUES ('{$date}', {$total}{$values})";

        if ($this->db->query_stats($qry))
            $this->log("{$date} - Inserted row ({$total} total)");
        else
            $this->log("{$date} - Problem inserting row ({$total} total)");
    }
    
    /**
     * Generate the CSV for graphs
     */
    public function generateCSV() {
        echo "Date,All Types,Handcrafted,Synchronized,Featured,Recommendations,Favorites,Mobile,Anonymous\n";

        $dates = $this->db->query_stats("SELECT date, total, type0, type1, type2, type3, type4, type5, type6 FROM {$this->table} ORDER BY date");
        while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
            echo implode(',', $date)."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new CollectionVotes;
    $report->generateCSV();
}

?>