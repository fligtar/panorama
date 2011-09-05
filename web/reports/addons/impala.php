<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/report.class.php';

class AddonImpala extends Report {
    public $table = '';
    public $backfillable = false;
    
    /**
     * Generate the CSV for graphs
     */
    public function generateHTML($date) {
        $time = strtotime($date);
        
        echo '<h1>Impala Download Referrers - '.date('M. j, Y', $time).'</h1>';
        echo '<p>This report shows the downloads that each page and source have referred, whether that download actually occurred on the page or not.</p>';
        
        $pages = array(
            'homepage' => array(
                'name' => 'Homepage',
                'webtrends' => 'AMO Homepage - all locales',
                'sources' => array(
                    'Promo Module' => array(
                        'hp-btn-promo' => 'Downloads from promo button',
                        'hp-dl-promo' => 'Downloads referred through details page'
                    ),
                    'Featured Carousel' => array(
                        'hp-hc-featured' => 'Downloads from hovercard',
                        'hp-dl-featured' => 'Downloads referred through details page'
                    ),
                    'Up & Coming Carousel' => array(
                        'hp-hc-upandcoming' => 'Downloads from hovercard',
                        'hp-dl-upandcoming' => 'Downloads referred through details page'
                    ),
                    'Most Popular List' => array(
                        'hp-dl-mostpopular' => 'Downloads referred through details page'
                    )
                )
            ),
            'details' => array(
                'name' => 'Add-on Details',
                'webtrends' => '',
                'sources' => array(
                    'Download Button (no source)' => array(
                        #'dp-btn-primary' => 'Downloads from primary button',
                        #'dp-btn-version' => 'Downloads from version button',
                        'addondetail' => 'Downloads from primary button',
                        'addon-detail-version' => 'Downloads from version button',
                        'dp-btn-devchannel' => 'Downloads from development channel button'
                    ),
                    'Often Used With' => array(
                        'dp-hc-oftenusedwith' => 'Downloads from hovercard',
                        'dp-dl-oftenusedwith' => 'Downloads referred through details page'
                    ),
                    'Others By Author' => array(
                        'dp-hc-othersby' => 'Downloads from hovercard',
                        'dp-dl-othersby' => 'Downloads referred through details page'
                    ),
                    'Dependencies' => array(
                        'dp-hc-dependencies' => 'Downloads from hovercard',
                        'dp-dl-dependencies' => 'Downloads referred through details page'
                    ),
                    'Upsell' => array(
                        'dp-hc-upsell' => 'Downloads from hovercard',
                        'dp-dl-upsell' => 'Downloads referred through details page'
                    )
                )
            ),
            'category-landing' => array(
                'name' => 'Category Landing',
                'webtrends' => '',
                'sources' => array(
                    'Featured Carousel' => array(
                        'cb-hc-featured' => 'Downloads from hovercard',
                        'cb-dl-featured' => 'Downloads referred through details page'
                    ),
                    'Top Rated Carousel' => array(
                        'cb-hc-toprated' => 'Downloads from hovercard',
                        'cb-dl-toprated' => 'Downloads referred through details page'
                    ),
                    'Most Popular Carousel' => array(
                        'cb-hc-mostpopular' => 'Downloads from hovercard',
                        'cb-dl-mostpopular' => 'Downloads referred through details page'
                    ),
                    'Recently Added Carousel' => array(
                        'cb-hc-recentlyadded' => 'Downloads from hovercard',
                        'cb-dl-recentlyadded' => 'Downloads referred through details page'
                    )
                )
            ),
            'category-browse' => array(
                'name' => 'Category Browse',
                'webtrends' => '',
                'sources' => array(
                    'Featured Sort' => array(
                        'cb-btn-featured' => 'Downloads from button',
                        'cb-dl-featured' => 'Downloads referred through details page'
                    ),
                    'Users Sort' => array(
                        'cb-btn-users' => 'Downloads from button',
                        'cb-dl-users' => 'Downloads referred through details page'
                    ),
                    'Rating Sort' => array(
                        'cb-btn-rating' => 'Downloads from button',
                        'cb-dl-rating' => 'Downloads referred through details page'
                    ),
                    'Created Sort' => array(
                        'cb-btn-created' => 'Downloads from button',
                        'cb-dl-created' => 'Downloads referred through details page'
                    ),
                    'Name Sort' => array(
                        'cb-btn-name' => 'Downloads from button',
                        'cb-dl-name' => 'Downloads referred through details page'
                    ),
                    'Downloads Sort' => array(
                        'cb-btn-popular' => 'Downloads from button',
                        'cb-dl-popular' => 'Downloads referred through details page'
                    ),
                    'Updated Sort' => array(
                        'cb-btn-updated' => 'Downloads from button',
                        'cb-dl-updated' => 'Downloads referred through details page'
                    ),
                    'Up & Coming Sort' => array(
                        'cb-btn-hotness' => 'Downloads from button',
                        'cb-dl-hotness' => 'Downloads referred through details page'
                    )
                )
            )
        );
        
        $webtrends = $this->webtrends('https://ws.webtrends.com/v3/Reporting/spaces/6370/profiles/11812/reports/kMKL354n7l5/?start_period='.date('Y\mm\dd', $time).'&end_period='.date('Y\mm\dd', $time).'&language=en-US&format=json');
        
        $ads = array();
        $dates = $this->db->query_stats("SELECT date, total, sources FROM addons_downloads_sources WHERE date = '{$date}' ORDER BY date");
        while ($_date = mysql_fetch_array($dates, MYSQL_ASSOC)) {
            $total_downloads[$_date['date']] = $_date['total'];
            $ads[$_date['date']] = json_decode($_date['sources'], true);
        }
        
        foreach ($pages as $page) {
            $page_downloads = 0;
            $cat_downloads = array();
            
            foreach ($page['sources'] as $_cat => $_srcs) {
                $cat_downloads[$_cat] = 0;
                
                foreach ($_srcs as $_src => $_desc) {
                    if (!empty($ads[$date][$_src])) {
                        $page_downloads += $ads[$date][$_src];
                        $cat_downloads[$_cat] += $ads[$date][$_src];
                    }
                    else {
                        $ads[$date][$_src] = 0;
                    }
                }
            }
            arsort($cat_downloads);
            
            // Output the pretties
            echo '<section>';
            echo '<h2>'.$page['name'].'</h2>';
            echo '<p>'.number_format($page_downloads).' downloads ('.round($page_downloads / $total_downloads[$date] * 100, 2).'% of site-wide total)';
            if (!empty($webtrends['data'][date('n/j/Y', $time)]['SubRows'][$page['webtrends']]['measures']['Page Views'])) {
                $pv = $webtrends['data'][date('n/j/Y', $time)]['SubRows'][$page['webtrends']]['measures']['Page Views'];
                echo ' / '.number_format($pv).' page views';
                echo ' / '.round($page_downloads / $pv * 100, 2).'% download conversion';
            }
            echo '</p>';
            echo '<table>';
            foreach ($cat_downloads as $_cat => $downloads) {
                $_srcs = $page['sources'][$_cat];
                echo '<tr><th class="heading">'.$_cat.'</th><th class="data">'.number_format($cat_downloads[$_cat]).' downloads</th><th class="data">'.round($cat_downloads[$_cat] / $page_downloads * 100, 0).'% of page total</th></tr>';
                foreach ($_srcs as $_src => $_desc) {
                    echo '<tr><td class="item">'.$_desc.'</td><td class="data">'.number_format($ads[$date][$_src]).'</td><td class="data">'.(empty($cat_downloads[$_cat]) ? 0 : round($ads[$date][$_src] / $cat_downloads[$_cat] * 100, 0)).'% of category total</td></tr>';
                }
            }
            echo '</table>';
            echo '</section>';
        }

    }

    /**
     * Output the available filters for app, os, and version
     */
    public function outputFilterJSON() {
        $filters = array(
            'date' => array()
        );

        $_dates = $this->db->query_stats("SELECT DISTINCT date FROM addons_downloads_sources WHERE date >= '2011-09' ORDER BY date DESC");
        while ($date = mysql_fetch_array($_dates, MYSQL_ASSOC)) $filters['date'][] = $date['date'];

        echo json_encode($filters);
    }
}

// If this is not being controlled by something else, output the HTML by default
if (!defined('OVERLORD')) {
    $report = new AddonImpala;
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    if ($action == 'html') {
        $date = !empty($_GET['date']) ? $_GET['date'] : '';
        $report->generateHTML($date);
    }
    elseif ($action == 'filters') {
        $report->outputFilterJSON();
    }
}

?>