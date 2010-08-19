<?php
require_once '../../lib/db.php';

echo "Date,All Types,Extensions,Themes,Dictionaries,Search Providers,Language Packs,Personas\n";

$dates = $db->query_stats("SELECT date, total, type1, type2, type3, type4, type5, type9 FROM addons_creation ORDER BY date");
while ($date = mysql_fetch_array($dates, MYSQL_NUM)) {
    echo implode(',', $date)."\n";
}
?>