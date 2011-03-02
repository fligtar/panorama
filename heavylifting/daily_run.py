import datetime
import sys
import lifter_usage
import lifter_performance

try:
    date = sys.argv[1]
except:
    date = datetime.date.today() - datetime.timedelta(1)

u = lifter_usage.AddonUsage(date)
del u

p = lifter_performance.StartupPerformance(date)
del p