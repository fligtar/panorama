<?php
require_once '../../lib/db.php';

echo "Label,Count\n";

$labels = array(
    'Private Stats',
    'Public Stats'
);

$values = $db->query_amo("SELECT publicstats, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY publicstats ORDER BY publicstats");
while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
    echo "{$labels[$value[0]]},{$value[1]}\n";
}
?>