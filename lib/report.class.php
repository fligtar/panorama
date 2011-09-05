<?php
require_once dirname(__FILE__).'/db.class.php';
require_once dirname(__FILE__).'/functions.php';

class Report {
    public $db = null;
    public $backfillable = false;
    public $cron_type = 'today';
    
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
     * Called once daily for each cron type. Possible types are
     * 'today' and 'yesterday'. Yesterday is used for things like logs
     * that aren't present until the next day.
     */
    public function daily($cron_type, $date = '') {
        // Check if the currently running cron is the correct one for this report
        if ($cron_type == $this->cron_type)
            $this->analyzeDay($date);
    }
    
    /**
     * This is called once a day. If the report has anything to run daily,
     * it should override this method and call it.
     */
    public function analyzeDay() {
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
    
    /**
     * cURL utility function to fetch a URL and return the output
     */
    public function load_url($url, $post = '', $credentials = '') {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        
        if (!empty($credentials)) {
            curl_setopt($ch, CURLOPT_USERPWD, $credentials);
        }

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
    
    /**
     * Fetches a Webtrends report and returns the JSON
     */
    public function webtrends($url) {
        $response = $this->load_url($url, '', WEBTRENDS_USER.':'.WEBTRENDS_PASS);
        
        return json_decode($response, true);
    }
}

?>