<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class CollectionVotesVote extends Report {
    public $table = 'collections_votes_vote';
    public $backfillable = true;
    
    /**
     * Pull data and store it for a single day's report
     */
    public function analyzeDay($date = '') {
        if (empty($date))
            $date = date('Y-m-d');
        
        $qry = "SELECT vote, COUNT(*) FROM collections_votes WHERE DATE(collections_votes.created) = '%DATE%' GROUP BY vote ORDER BY vote";
        
        $_types = $this->db->query_amo(str_replace('%DATE%', $date, $qry));
        
        $fields = '';
        $values = '';
        $total = 0;
        $labels = array(
            -1 => 'negative',
            1 => 'positive'
        );
        
        if (mysql_num_rows($_types) > 0) {
            while ($type = mysql_fetch_array($_types, MYSQL_NUM)) {
                $fields .= ", {$labels[$type[0]]}";
                $values .= ", {$type[1]}";
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
        echo "Date,All Types,Positive,Negative\n";

        $dates = $this->db->query_stats("SELECT date, total, positive, negative FROM {$this->table} ORDER BY date");
        while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
            echo implode(',', $date)."\n";
        }
    }
}

// If this is not being controlled by something else, output the CSV by default
if (!defined('OVERLORD')) {
    $report = new CollectionVotesVote;
    $report->generateCSV();
}

?>