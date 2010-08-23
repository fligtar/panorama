<?php
require_once dirname(__FILE__).'/db.class.php';

class Report {
    public $db = null;
    public $backfillable = false;
    
    /**
     * Report constructor. Instantiates the db
     */
    public function __construct() {
        $this->db = new Database();
        
    }
    
    /**
     * Analyzes multiple days
     */
    public function backfill($start_date, $end_date = '') {
        if (!$this->backfillable) {
            $this->log('Not backfillable');
            return false;
        }
        
        // If end date not given, use today
        if (empty($end_date))
            $end_date = date('Y-m-d');
        
        $this->log("Backfilling between {$start_date} and {$end_date}");
        
        // Convert dates to time
        $end_date = strtotime($end_date);
        $date = strtotime($start_date);
        
        while ($date <= $end_date) {
            // Analyze the given date
            $this->analyzeDay(date('Y-m-d', $date));
            
            // Increment date
            $date = strtotime('+1 day', $date);
        }
    }
    
    /**
     * This is called once a day. If the report has anything to run daily,
     * it should override this method and call it.
     */
    public function daily() {
        $this->log('Nothing to run daily.');
    }
    
    /**
     * Log a message with class name
     */
    public function log($msg) {
        echo '['.get_class($this).'] '.$msg."<br/>\n";
        
        if (defined('OVERLORD'))
            ob_flush();
    }
}

?>