<?php
require_once '../../lib/db.php';

echo "Date,All Types,Extensions,Themes,Dictionaries,Search Providers,Language Packs,Personas\n";

$_addontypes = $db->query_stats("SELECT date, total, id1, id2, id3, id4, id5, id9 FROM addontypes ORDER BY date");
while ($addontype = mysql_fetch_array($_addontypes, MYSQL_NUM)) {
    echo implode(',', $addontype)."\n";
}
?>