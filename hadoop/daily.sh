# Call without parameters for yesterday's date. Call with date to backfill
# e.g. ./daily.sh 2011-01-15

if [ -n "$1" ]
then
    DATE=$1
else
    DATE=`date --date='yesterday' +%Y-%m-%d`
fi

echo "Beginning log processing for $1"
DATA=/home/fligtar/data/$DATE
mkdir $DATA

# Discovery Pane views
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%discovery/pane%';" | tee $DATA/discovery.txt

# Discovery Pane details
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%discovery/addon%';" | tee $DATA/discovery-details.txt

# ABP Icon views by referrer
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT empty_string_1, COUNT(1) as num FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND (domain='addons.mozilla.org' OR domain='static.addons.mozilla.org') AND request_url LIKE '%/images/addon_icon/1865/%' GROUP BY empty_string_1 ORDER BY num DESC;" | tee $DATA/abp-icon-referrers.txt

# ABP Icon views total
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND (domain='addons.mozilla.org' OR domain='static.addons.mozilla.org') AND request_url LIKE '%/images/addon_icon/1865/%';" | tee $DATA/abp-icon-total.txt

# API Total
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api%';" | tee $DATA/api-total.txt

# API Featured
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/list/featured/%';" | tee $DATA/api-featured.txt

# API Search
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/%';" | tee $DATA/api-search.txt

# API GUID Search
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%';" | tee $DATA/api-guid-search.txt

# API Add-on
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT COUNT(1) FROM research_logs WHERE ip_address != 'NULL' AND ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/addon/%';" | tee $DATA/api-addon.txt

# 
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT commas, COUNT(1) FROM (SELECT (LENGTH(request_url) - LENGTH(REGEXP_REPLACE(request_url, ',', ''))) as commas FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%') temp GROUP BY commas ORDER BY commas;" | tee $DATA/at.txt

scp -r $DATA/ fligtar@khan:./hadoop-drop/
