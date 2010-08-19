<?php
require_once '../../lib/db.php';

echo "Label,Count\n";

$values = $db->query_amo("SELECT status, COUNT(*) FROM (SELECT IF(eula, 'Has EULA', 'No EULA') as status FROM addons WHERE addontype_id < 4) AS temp GROUP BY status ORDER BY status");
while ($value = mysql_fetch_array($values, MYSQL_NUM)) {
    echo "{$value[0]},{$value[1]}\n";
}
?>