<!DOCTYPE html>
<html lang="en">

<head>
    <title>panorama</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link rel="shortcut icon" type="image/png" href="images/favicon.ico" />
</head>
<body>
    <header>
        <div class="corner"><a href="#" onclick="panorama.groupBy('week'); return false;">group by week</a> | <a href="#" onclick="panorama.groupBy('month'); return false;">month</a></div>
        <h1>panorama</h1>
    </header>
    
    <nav>
        <ul>
            <li>add-ons</li>
            <ul>
                <li><a href="#addon-categories" onclick="panorama.getReport(reports.addons.categories, this);" class="todo">categories</a></li>
                <li><a href="#addon-creation" onclick="panorama.getReport(reports.addons.creation, this, this);">creation</a></li>
                <li><a href="#addon-downloads" onclick="panorama.getReport(reports.addons.downloads, this);">downloads</a></li>
                <li><a href="#addon-eulas" onclick="panorama.getReport(reports.addons.eulas, this);">eulas</a></li>
                <li class="popular"><a href="#addon-impala" onclick="panorama.getReport(reports.addons.impala, this);">impala downloads</a></li>
                <li><a href="#addon-impressions" onclick="panorama.getReport(reports.addons.impressions, this);">impressions</a></li>
                <li><a href="#addon-packager" onclick="panorama.getReport(reports.addons.packager, this);">packager</a></li>
                <li><a href="#addon-privacy" onclick="panorama.getReport(reports.addons.privacy, this);">privacy policies</a></li>
                <li><a href="#addon-publicstats" onclick="panorama.getReport(reports.addons.publicstats, this);">public stats</a></li>
                <li><a href="#addon-shares" onclick="panorama.getReport(reports.addons.shares, this);" class="todo">shares</a></li>
                <li><a href="#addon-status" onclick="panorama.getReport(reports.addons.status, this);">status</a></li>
                <li><a href="#addon-tags" onclick="panorama.getReport(reports.addons.tags, this);">tags</a></li>
                <li><a href="#addon-themeusage" onclick="panorama.getReport(reports.addons.themeusage, this);">theme usage</a></li>
                <li><a href="#addon-translations" onclick="panorama.getReport(reports.addons.translations, this);" class="todo">translations</a></li>
                <li><a href="#addon-updatepings" onclick="panorama.getReport(reports.addons.updatepings, this);">update pings</a></li>
                <li><a href="#addon-reviews" onclick="panorama.getReport(reports.addons.reviews, this);">user reviews</a></li>
                <li><a href="#addon-viewsource" onclick="panorama.getReport(reports.addons.viewsource, this);">view source</a></li>
            </ul>
            
            <li>collections</li>
            <ul>
                <li><a href="#collection-creation" onclick="panorama.getReport(reports.collections.creation, this);">creation</a></li>
                <li><a href="#collection-privacy" onclick="panorama.getReport(reports.collections.privacy, this);">privacy</a></li>
                <li><a href="#collection-publishing" onclick="panorama.getReport(reports.collections.publishing, this);">publishing</a></li>
                <li><a href="#collection-votes" onclick="panorama.getReport(reports.collections.votes, this);">votes</a></li>
                <li><a href="#collection-watchers" onclick="panorama.getReport(reports.collections.watchers, this);">watchers</a></li>
            </ul>
            
            <li>contributions</li>
            <ul>
                <li><a href="#contributions-summary" onclick="panorama.getReport(reports.contributions.summary, this);">summary</a></li>
                <li><a href="#contributions-annoyance" onclick="panorama.getReport(reports.contributions.annoyance, this);">annoyance</a></li>
                <li><a href="#contributions-recipients" onclick="panorama.getReport(reports.contributions.recipients, this);">recipients</a></li>
                <li><a href="#contributions-sources" onclick="panorama.getReport(reports.contributions.sources, this);">sources</a></li>
            </ul>
            
            <li>ecosystem</li>
            <ul>
                <li class="popular"><a href="#ecosystem-addonusage" onclick="panorama.getReport(reports.ecosystem.addonusage, this);">add-on usage</a></li>
                <li><a href="#ecosystem-topaddons" onclick="panorama.getReport(reports.ecosystem.topaddons, this);">top add-ons</a></li>
            </ul>
            
            <li>editors</li>
            <ul>
                <li><a href="#editors-queues" onclick="panorama.getReport(reports.editors.queues, this);">queues</a></li>
            </ul>
            
            <li>marketplace</li>
            <ul>
                <li class="popular"><a href="#marketplace-overview" onclick="panorama.getReport(reports.marketplace.overview, this);">overview</a></li>
            </ul>
            
            <li>performance</li>
            <ul>
                <li class="popular"><a href="#performance-startupdistro" onclick="panorama.getReport(reports.performance.startupdistro, this);">start-up time</a></li>
                <li><a href="#performance-addonimpact" onclick="panorama.getReport(reports.performance.addonimpact, this);">add-on impact</a></li>
            </ul>
            
            <li>services</li>
            <ul>
                <li><a href="#services-api" onclick="panorama.getReport(reports.services.api, this);">api usage</a></li>
                <li class="popular"><a href="#services-discovery" onclick="panorama.getReport(reports.services.discovery, this);">discovery pane</a></li>
            </ul>
            
            <li>users</li>
            <ul>
                <li><a href="#user-creation" onclick="panorama.getReport(reports.users.creation, this);" >creation</a></li>
            </ul>
        </ul>
    </nav>
    
    <section id="content">
    
    </section>
    
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="js/date.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/reports.js"></script>
	<script type="text/javascript" src="js/panorama.js"></script>
</body>
</html>
