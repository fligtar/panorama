<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class MarketplaceOverview extends Report {
    public $table = 'marketplace_overview';
    
    /**
     * Generate the HTML
     */
    public function generateHTML() {
        
        echo '<div class="report-section">';
        echo "<h1>Marketplace Overview</h1><ul>";
        
        $queries = array(
            'apps installed' => "SELECT COUNT(*) FROM users_install",
            'apps purchased' => "SELECT COUNT(*) FROM addon_purchase",
            '# purchases (including in-app)' => "SELECT COUNT(*) FROM stats_contributions WHERE type = 1 AND transaction_id IS NOT NULL",
            'purchase revenue' => "SELECT CONCAT('$', SUM(amount)) FROM stats_contributions WHERE type = 1 AND transaction_id IS NOT NULL",
            'refunds' => "SELECT COUNT(*) FROM stats_contributions WHERE type = 2",
            'users' => "SELECT COUNT(*) FROM users WHERE notes = '__market__'",
            'users with preauth' => "SELECT COUNT(*) FROM users_preapproval WHERE paypal_key IS NOT NULL",
            'delayed total downloads' => "SELECT SUM(totaldownloads) FROM addons WHERE addontype_id = 11"
        );
        
        foreach ($queries as $name => $_qry) {
            $qry = $this->db->query_amo($_qry);
            $r = mysql_fetch_array($qry);
            
            echo "<li>{$name}: {$r[0]}</li>";
        }
        
        echo '</ul></div>';

    }
}

// If this is not being controlled by something else, output the HTML by default
if (!defined('OVERLORD')) {
    $report = new MarketplaceOverview;
    $report->generateHTML();
}

?>