<!DOCTYPE html>
<html lang="en">

<head>
    <title>^v^v^v polygraph ^v^v^v</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
    <header>
        <h1>polygraph</h1>
    </header>
    
    <nav>
        <ul>
            <li>add-ons</li>
            <ul>
                <li><a href="#addon-categories" onclick="polygraph.getReport(reports.addons.categories);" class="todo">categories</a></li>
                <li><a href="#addon-contributions" onclick="polygraph.getReport(reports.addons.contributions);" class="todo">contributions</a></li>
                <li><a href="#addon-creation" onclick="polygraph.getReport(reports.addons.creation, this);">creation</a></li>
                <li><a href="#addon-downloads" onclick="polygraph.getReport(reports.addons.downloads);" class="todo">downloads</a></li>
                <li><a href="#addon-eulas" onclick="polygraph.getReport(reports.addons.eulas);" class="todo">eulas</a></li>
                <li><a href="#addon-license" onclick="polygraph.getReport(reports.addons.license);" class="todo">license</a></li>
                <li><a href="#addon-privacy" onclick="polygraph.getReport(reports.addons.privacy);" class="todo">privacy policies</a></li>
                <li><a href="#addon-stats" onclick="polygraph.getReport(reports.addons.stats);" class="todo">public stats</a></li>
                <li><a href="#addon-shares" onclick="polygraph.getReport(reports.addons.shares);" class="todo">shares</a></li>
                <li><a href="#addon-status" onclick="polygraph.getReport(reports.addons.status);" class="todo">status</a></li>
                <li><a href="#addon-tags" onclick="polygraph.getReport(reports.addons.tags);" class="todo">tags</a></li>
                <li><a href="#addon-translations" onclick="polygraph.getReport(reports.addons.translations);" class="todo">translations</a></li>
                <li><a href="#addon-updatepings" onclick="polygraph.getReport(reports.addons.updatepings);" class="todo">update pings</a></li>
                <li><a href="#addon-reviews" onclick="polygraph.getReport(reports.addons.reviews);" class="todo">user reviews</a></li>
                <li><a href="#addon-viewsource" onclick="polygraph.getReport(reports.addons.viewsource);" class="todo">view source</a></li>
            </ul>
            
            <li>collections</li>
            <ul>
                <li><a href="#collection-creation" onclick="polygraph.getReport(reports.collections.creation);" class="todo">creation</a></li>
                <li><a href="#collection-published" onclick="polygraph.getReport(reports.collections.published);" class="todo">published add-ons</a></li>
                <li><a href="#collection-votes" onclick="polygraph.getReport(reports.collections.votes);" class="todo">votes</a></li>
                <li><a href="#collection-watchers" onclick="polygraph.getReport(reports.collections.watchers);" class="todo">watchers</a></li>
            </ul>
            
            <li>review queues</li>
            <ul>
                <li><a href="#queue-nominations" onclick="polygraph.getReport(reports.queues.nominations);" class="todo">nominations</a></li>
                <li><a href="#queue-reviews" onclick="polygraph.getReport(reports.queues.reviews);" class="todo">user reviews</a></li>
                <li><a href="#queue-updates" onclick="polygraph.getReport(reports.queues.updates);" class="todo">updates</a></li>
            </ul>
            
            <li>threat assessment</li>
            <ul>
                <li><a href="#threat-new" onclick="polygraph.getReport(reports.threat.new);" class="todo">new add-ons</a></li>
                <li><a href="#threat-spam" onclick="polygraph.getReport(reports.threat.spam);" class="todo">spam reviews</a></li>
            </ul>
            
            <li>users</li>
            <ul>
                <li><a href="#user-activity" onclick="polygraph.getReport(reports.users.activity);" class="todo">activity</a></li>
                <li><a href="#user-creation" onclick="polygraph.getReport(reports.users.creation);" class="todo">creation</a></li>
                <li><a href="#user-confirmation" onclick="polygraph.getReport(reports.users.confirmation);" class="todo">confirmation</a></li>
                <li><a href="#user-deletion" onclick="polygraph.getReport(reports.users.creation);" class="todo">deletion</a></li>
                <li><a href="#user-pictures" onclick="polygraph.getReport(reports.users.deletion);" class="todo">pictures</a></li>
            </ul>
        </ul>
    </nav>
    
    <section id="content">
    
    </section>
    
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/polygraph.js"></script>
</body>
</html>
