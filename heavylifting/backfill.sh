# Backfills only a specific report for a date
# e.g. ./backfill.sh 2011-01-15

if [ -n "$1" ]
then
    DATE=$1
else
    DATE=`date --date='yesterday' +%Y-%m-%d`
fi

echo "Beginning backfill processing for $1"
DATA=/home/fligtar/data/$DATE

# Number of add-ons installed
hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "SELECT commas, COUNT(1) FROM (SELECT (LENGTH(request_url) - LENGTH(REGEXP_REPLACE(request_url, ',', '')) + 1) as commas FROM research_logs WHERE ds = '$DATE' AND domain='services.addons.mozilla.org' AND request_url LIKE '%api/%/search/guid%') temp GROUP BY commas ORDER BY commas;" | tee $DATA/metadata-installed-distro.txt

scp -r $DATA/metadata-installed-distro.txt fligtar@khan:./hadoop-drop/$DATE/
