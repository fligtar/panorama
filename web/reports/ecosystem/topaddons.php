<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class EcosystemTopaddons extends Report {
    public $table = 'ecosystem_topaddons';
    
    /**
     * Generate the HTML
     */
    public function generateHTML() {
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
        
        $_date = $this->db->query_stats("SELECT date FROM {$this->table} ORDER BY date DESC LIMIT 1");
        $date = mysql_fetch_array($_date);
        $date = $date[0];
        
        $unhosted = array();
        $_known = $this->db->query_stats("SELECT * FROM _unhosted_guids");
        while ($row = mysql_fetch_array($_known)) {
            $unhosted[$row['guid']] = $row['name'];
        }
        
        echo '<div class="report-section">';
        echo "<h1>Top Add-ons {$date}</h1>";
        
        $_qry = $this->db->query_stats("SELECT guid, installs FROM {$this->table} WHERE date = '{$date}' ORDER BY installs DESC LIMIT 100");
        
        echo '<ol>';
        while ($guids = mysql_fetch_array($_qry, MYSQL_ASSOC)) {
            $guid = $guids['guid'];
            $adu = $guids['installs'];
                
            $_amo = $this->db->query_amo("SELECT addons.id, addons.status, translations.localized_string as name FROM addons INNER JOIN translations ON translations.id=addons.name AND translations.locale=addons.defaultlocale WHERE addons.guid='".addslashes($guid)."'");
            if (mysql_num_rows($_amo) > 0) {
                $amo = mysql_fetch_array($_amo, MYSQL_ASSOC);
                
                echo '<li class="hosted"><strong><a href="https://addons.mozilla.org/addon/'.$amo['id'].'" title="'.$guid.'" target="_blank">'.$amo['name'].'</a></strong> - AMO: '.$amo_statuses[$amo['status']].' - '.number_format($adu).' users</li>';
            }
            elseif (!empty($unhosted[$guid])) {
                echo '<li><strong><a href="http://www.google.com/search?q='.$guid.'" title="'.$guid.'" target="_blank">'.$unhosted[$guid].'</a></strong> - AMO: Not Hosted - '.number_format($adu).' users</li>';
            }
            else {
                echo '<li><strong><a href="http://www.google.com/search?q='.$guid.'" target="_blank">'.$guid.'</a></strong> - '.number_format($adu).' users</li>';
            }
        }
        echo '</ol>';
        echo '</div>';

    }
}

// If this is not being controlled by something else, output the HTML by default
if (!defined('OVERLORD')) {
    $report = new EcosystemTopaddons;
    $report->generateHTML();
}

?>