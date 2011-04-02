<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class PerformanceAddonimpact extends Report {
    public $table = 'performance_addons';
    
    /**
     * Output the available filters for app, os, and version
     */
    public function outputFilterJSON() {
        $filters = array(
            'os' => array('WINNT', 'Darwin'),
            'date' => array()
        );
        
        $_dates = $this->db->query_stats("SELECT DISTINCT date FROM {$this->table} ORDER BY date DESC");
        while ($date = mysql_fetch_array($_dates, MYSQL_ASSOC)) $filters['date'][] = $date['date'];
        
        echo json_encode($filters);
    }
    
    /**
     * Generate the HTML
     */
    public function generateHTML($date = '', $os = 'WINNT') {
        $amo_statuses = array(
            'Incomplete',
            'Awaiting Preliminary Review (Previously Unreviewed)',
            'Pending (Files only)',
            'Awaiting Full Review',
            'Fully Reviewed',
            'Admin Disabled',
            'Self-hosted',
            'Beta (Files only)',
            'Preliminarily Reviewed',
            'Preliminarily Reviewed and Awaiting Full Review',
            'Purgatory'
        );
        
        if (empty($date)) {
            $_date = $this->db->query_stats("SELECT date FROM {$this->table} ORDER BY date DESC LIMIT 1");
            $date = mysql_fetch_array($_date);
            $date = $date[0];
        }
        
        $unhosted = array();
        $_known = $this->db->query_stats("SELECT * FROM _unhosted_guids");
        while ($row = mysql_fetch_array($_known)) {
            $unhosted[$row['guid']] = $row['name'];
        }
        
        echo '<div class="report-section">';
        echo "<h3>Slow Add-ons {$os} {$date}</h3>";
        
        $_qry = $this->db->query_stats("SELECT guid, {$os}_tsessionrestored_avg FROM {$this->table} WHERE date = '{$date}' ORDER BY {$os}_tsessionrestored_avg DESC LIMIT 100");
        
        echo '<ol>';
        while ($guids = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $guid = $guids['guid'];
            //$perf = json_decode($guids['winnt'], true);
                
            $_amo = $this->db->query_amo("SELECT addons.id, addons.status, translations.localized_string as name FROM addons INNER JOIN translations ON translations.id=addons.name AND translations.locale=addons.defaultlocale WHERE addons.guid='".addslashes($guid)."'");
            if (mysql_num_rows($_amo) > 0) {
                $amo = mysql_fetch_array($_amo, MYSQL_ASSOC);
                
                echo '<li class="hosted"><strong><a href="https://addons.mozilla.org/addon/'.$amo['id'].'" title="'.$guid.'" target="_blank">'.$amo['name'].'</a></strong> - AMO: '.$amo_statuses[$amo['status']].' - '.$guids[$os.'_tsessionrestored_avg'].' tSessionRestored avg</li>';
            }
            elseif (!empty($unhosted[$guid])) {
                echo '<li><strong><a href="http://www.google.com/search?q='.$guid.'" title="'.$guid.'" target="_blank">'.$unhosted[$guid].'</a></strong> - AMO: Not Hosted - '.$guids[$os.'_tsessionrestored_avg'].' tSessionRestored avg</li>';
            }
            else {
                echo '<li><strong><a href="http://www.google.com/search?q='.$guid.'" target="_blank">'.$guid.'</a></strong> - '.$guids[$os.'_tsessionrestored_avg'].' tSessionRestored avg</li>';
            }
        }
        echo '</ol>';
        echo '</div>';

    }
}

// If this is not being controlled by something else, output the HTML by default
if (!defined('OVERLORD')) {
    $report = new PerformanceAddonimpact;
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    if ($action == 'html') {
        $date = !empty($_GET['date']) ? $_GET['date'] : '';
        $os = !empty($_GET['os']) ? $_GET['os'] : '';
        $report->generateHTML($date, $os);
    }
    elseif ($action == 'filters') {
        $report->outputFilterJSON();
    }
}

?>