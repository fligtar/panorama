<?php
require_once dirname(dirname(__FILE__)).'/config.php';


class Database {
    private $db;
    private $selected = '';
    
    public function query_amo($qry) {
        if ($this->selected != 'amo')
            $this->connect_amo();
        
        return mysql_query($qry, $this->db);
    }
    
    public function query_stats($qry) {
        if ($this->selected != 'stats')
            $this->connect_stats();
        
        return mysql_query($qry, $this->db);
    }
    
    private function connect_amo() {
        $this->disconnect();
        $this->db = mysql_connect(AMO_DB_HOST, AMO_DB_USER, AMO_DB_PASS);
        mysql_select_db(AMO_DB_NAME, $this->db);
        $this->selected = 'amo';
    }
    
    private function connect_stats() {
        $this->disconnect();
        $this->db = mysql_connect(STATS_DB_HOST, STATS_DB_USER, STATS_DB_PASS);
        mysql_select_db(STATS_DB_NAME, $this->db);
        $this->selected = 'stats';
        
        // Default is 120 and isn't sufficient for at least one report
        $this->query_stats("SET GLOBAL wait_timeout = 600");
    }
    
    private function disconnect() {
        if (is_object($this->db))
            mysql_close($this->db);
        $this->selected = '';
    }

}

?>