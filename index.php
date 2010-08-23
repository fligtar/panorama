<!DOCTYPE html>
<html lang="en">

<head>
    <title>panorama</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
    <header>
        <h1>panorama</h1>
    </header>
    
    <nav>
        <ul>
            <li>add-ons</li>
            <ul>
                <li><a href="#addon-categories" onclick="panorama.getReport(reports.addons.categories);" class="todo">categories</a></li>
                <li><a href="#addon-contributions" onclick="panorama.getReport(reports.addons.contributions);" class="todo">contributions</a></li>
                <li><a href="#addon-creation" onclick="panorama.getReport(reports.addons.creation, this);">creation</a></li>
                <li><a href="#addon-downloads" onclick="panorama.getReport(reports.addons.downloads);" class="todo">downloads (sources)</a></li>
                <li><a href="#addon-eulas" onclick="panorama.getReport(reports.addons.eulas);">eulas</a></li>
                <li><a href="#addon-license" onclick="panorama.getReport(reports.addons.license);" class="todo">license</a></li>
                <li><a href="#addon-privacy" onclick="panorama.getReport(reports.addons.privacy);">privacy policies</a></li>
                <li><a href="#addon-publicstats" onclick="panorama.getReport(reports.addons.publicstats);">public stats</a></li>
                <li><a href="#addon-shares" onclick="panorama.getReport(reports.addons.shares);" class="todo">shares</a></li>
                <li><a href="#addon-status" onclick="panorama.getReport(reports.addons.status);" class="todo">status</a></li>
                <li><a href="#addon-tags" onclick="panorama.getReport(reports.addons.tags);" class="todo">tags</a></li>
                <li><a href="#addon-themes" onclick="panorama.getReport(reports.addons.themes);" class="todo">theme usage</a></li>
                <li><a href="#addon-translations" onclick="panorama.getReport(reports.addons.translations);" class="todo">translations</a></li>
                <li><a href="#addon-updatepings" onclick="panorama.getReport(reports.addons.updatepings);" class="todo">update pings</a></li>
                <li><a href="#addon-reviews" onclick="panorama.getReport(reports.addons.reviews);" class="todo">user reviews</a></li>
                <li><a href="#addon-viewsource" onclick="panorama.getReport(reports.addons.viewsource);">view source</a></li>
            </ul>
            
            <li>collections</li>
            <ul>
                <li><a href="#collection-creation" onclick="panorama.getReport(reports.collections.creation);" class="todo">creation</a></li>
                <li><a href="#collection-published" onclick="panorama.getReport(reports.collections.published);" class="todo">published add-ons</a></li>
                <li><a href="#collection-votes" onclick="panorama.getReport(reports.collections.votes);" class="todo">votes</a></li>
                <li><a href="#collection-watchers" onclick="panorama.getReport(reports.collections.watchers);" class="todo">watchers</a></li>
            </ul>
            
            <li>editors</li>
            <ul>
                <li><a href="#editors-queues" onclick="panorama.getReport(reports.editors.queues);">queues</a></li>
                <li><a href="#editors-activity" onclick="panorama.getReport(reports.editors.activity);" class="todo">activity</a></li>
            </ul>
            
            <li>threat assessment</li>
            <ul>
                <li><a href="#threat-new" onclick="panorama.getReport(reports.threat.new);" class="todo">new add-ons</a></li>
                <li><a href="#threat-spam" onclick="panorama.getReport(reports.threat.spam);" class="todo">spam reviews</a></li>
            </ul>
            
            <li>users</li>
            <ul>
                <li><a href="#user-activity" onclick="panorama.getReport(reports.users.activity);" class="todo">activity</a></li>
                <li><a href="#user-creation" onclick="panorama.getReport(reports.users.creation);" class="todo">creation</a></li>
                <li><a href="#user-confirmation" onclick="panorama.getReport(reports.users.confirmation);" class="todo">confirmation</a></li>
                <li><a href="#user-deletion" onclick="panorama.getReport(reports.users.creation);" class="todo">deletion</a></li>
                <li><a href="#user-pictures" onclick="panorama.getReport(reports.users.deletion);" class="todo">pictures</a></li>
            </ul>
        </ul>
    </nav>
    
    <section id="content">
    
    </section>
    
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/reports.js"></script>
	<script type="text/javascript" src="js/panorama.js"></script>
</body>
</html>
