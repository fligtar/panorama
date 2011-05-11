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

# DEBUG
#hive -e "SHOW PARTITIONS research_logs;"

# Discovery Pane views
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%discovery/pane%';" | tee $DATA/discovery.txt

# Discovery Pane details
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%discovery/addon%';" | tee $DATA/discovery-details.txt

# RETIRED by CDN
# ABP Icon views by referrer
#hive -e "SELECT empty_string_1, COUNT(1) as num FROM research_logs WHERE ds = '$DATE' AND (domain='addons.mozilla.org' OR domain='static.addons.mozilla.org') AND request_url LIKE '%/images/addon_icon/1865/%' GROUP BY empty_string_1 ORDER BY num DESC;" > $DATA/abp-icon-referrers.txt

# RETIRED by CDN
# ABP Icon views total
#hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND (domain='addons.mozilla.org' OR domain='static.addons.mozilla.org') AND request_url LIKE '%/images/addon_icon/1865/%';" | tee $DATA/abp-icon-total.txt

# API Total
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api%';" | tee $DATA/api-total.txt

# API Featured
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/list/featured/%';" | tee $DATA/api-featured.txt

# API Search
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/%';" | tee $DATA/api-search.txt

# API GUID Search
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%';" | tee $DATA/api-guid-search.txt

# API Add-on
hive -e "SELECT COUNT(1) FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/addon/%';" | tee $DATA/api-addon.txt

# Number of add-ons installed (Firefox)
#hive -e "SELECT commas, COUNT(1) FROM (SELECT (LENGTH(request_url) - LENGTH(REGEXP_REPLACE(request_url, ',', '')) + 1) as commas FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%972ce4c6-7e08-4474-a285-3208198ce6fd%src=firefox%') temp GROUP BY commas ORDER BY commas;" | tee $DATA/metadata-installed-fx.txt

# Number of add-ons installed (Mobile)
#hive -e "SELECT commas, COUNT(1) FROM (SELECT (LENGTH(request_url) - LENGTH(REGEXP_REPLACE(request_url, ',', '')) + 1) as commas FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%972ce4c6-7e08-4474-a285-3208198ce6fd%src=mobile%') temp GROUP BY commas ORDER BY commas;" | tee $DATA/metadata-installed-mobile.txt

# Firefox start-up performance
#hive -e "SELECT guid, src, appos, appversion, tmain, tfirstpaint, tsessionrestored FROM addons_pings WHERE ds = '$DATE';" > $DATA/metadata-perf.txt

scp -r $DATA/ fligtar@khan:./panorama/hadoop-drop/
