<?php
require_once '../../lib/db.php';

echo "Label,Count\n";

$labels = array(
    'Viewing Disabled',
    'Viewing Enabled'
);

$values = $db->query_amo("SELECT viewsource, COUNT(*) FROM addons WHERE addontype_id < 4 GROUP BY viewsource ORDER BY viewsource");
while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
    echo "{$labels[$value[0]]},{$value[1]}\n";
}
?>