from __future__ import division # holy crap, python
import collections
import sys
import re
import urllib2
import json
import hive
import math
from lifter import Lifter
from settings import *

class StartupPerformance(Lifter):
    """This lifter looks through daily add-on metadata pings and gathers
    lots of data about the ecosystem for that day, like the number of 
    users with add-ons, how many of the add-ons are hosted on AMO, etc."""
    
    def lift(self):
        for app in ['firefox']:
            hive_file = self.hive_data(app)
            data = self.analyze_performance(hive_file)
            #self.commit(data)
            #self.hive_cleanup(hive_file)
    
    def hive_data(self, app):
        """Performs a HIVE query and writes it to a text file."""
        
        if HIVE_ALTERNATE is not None:
            self.log('Hive alternate file used')
            return HIVE_ALTERNATE
        
        hive_file = hive.query("""SELECT guid, appos, appversion, tmain, 
                    tfirstpaint, tsessionrestored FROM addons_pings 
                    WHERE ds = '%s' AND src='%s' AND guid LIKE 
                    '%972ce4c6-7e08-4474-a285-3208198ce6fd%';""" % (self.date, app))
        
        self.time_event('hive_data')
        self.log('Hive file finished')
        
        return hive_file

    def analyze_performance(self, hive_file):
        """This function reads a file of add-on GUID combinations and start-up
        data and splits it into distributions of start-up seconds by number
        of add-ons installed"""
        
        raw_times = {}
        addon_count = {}

        with open(hive_file) as f:
            for line in f:
                _guids, _appos, _appversion, _tmain, _tfirstpaint, _tsessionrestored = line.split()
                
                # The default theme doesn't count, so it's one fewer add-on
                #TODO: Filter out bad guids (personas, greasemonkey)
                num_addons = _guids.count(',')
                
                # Number of occurrences of each time
                if _appos not in raw_times:
                    raw_times[_appos] = {}
                if _appversion not in raw_times[_appos]:
                    raw_times[_appos][_appversion] = {
                        'tmain': {
                            'distro': collections.defaultdict(int),
                            'times': [],
                        },
                        'tfirstpaint': {
                            'distro': collections.defaultdict(int),
                            'times': [],
                        },
                        'tsessionrestored': {
                            'distro': collections.defaultdict(int),
                            'times': [],
                        },
                    }
                
                # Times per number of add-ons
                if num_addons not in addon_count:
                    addon_count[num_addons] = collections.defaultdict(list)
                
                if _tmain.isdigit():
                    _tmain = int(_tmain)
                    if _tmain >= 0 and _tmain < 3600000:
                        raw_times[_appos][_appversion]['tmain']['distro'][int(round(_tmain / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tmain']['times'].append(_tmain)
                        addon_count[num_addons]['tmain'].append(_tmain)
                
                if _tfirstpaint.isdigit():
                    _tfirstpaint = int(_tfirstpaint)
                    if _tfirstpaint >= 0 and _tfirstpaint < 3600000:
                        raw_times[_appos][_appversion]['tfirstpaint']['distro'][int(round(_tfirstpaint / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tfirstpaint']['times'].append(_tfirstpaint)
                        addon_count[num_addons]['tfirstpaint'].append(_tfirstpaint)
        
                if _tsessionrestored.isdigit():
                    _tsessionrestored = int(_tsessionrestored)
                    if _tsessionrestored >= 0 and tfirstpaint < 3600000:
                        raw_times[_appos][_appversion]['tsessionrestored']['distro'][int(round(_tsessionrestored / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tsessionrestored']['times'].append(_tsessionrestored)
                        addon_count[num_addons]['tsessionrestored'].append(_tsessionrestored)
        
        self.log('GUIDs from file processed')
        
        # Do calculations on overall distribution
        for appos, appversions in raw_times.iteritems():
            for appversion, measures in appversions.iteritems():
                for measure in measures:
                    raw_times[appos][appversion][measure]['times'] = self.calculations(measures[measure]['times'])
                    
        
        # Do calculations on add-on count distribution
        for num_addons, measures in addon_count.iteritems():
            for measure, times in measures.iteritems():
                addon_count[num_addons][measure] = self.calculations(times)
        
        self.log('Additional calculations made')
        self.time_event('analyze_performance')
        
        print json.dumps(addon_count, indent=4)
        
        return {
            'addon_count': addon_count,
            'raw_times': raw_times,
        }
    
    def calculations(self, times):
        """Perform calculations on a list of times."""
        r = {}
        _len = len(times)
        
        if _len == 0:
            return r
        
        return {
            'count': _len,
            'avg': round(sum(times) / _len, 2),
            'median': times[int(math.floor(_len / 2))],
            'max': max(times),
            'min': min(times),
        }
    
    def commit(self, data):
        """Save our findings to the db."""
        
        db = self.get_database().cursor()
        self.log('Inserting addons_installed...')
        db.execute("""INSERT INTO addons_installed (%s) 
                    VALUES ('%s')""" % (', '.join(data['addons_installed']), 
                    "','".join(map(str, data['addons_installed'].values()))))
        
        self.log('Inserting addons_usage...')
        for guid, count in data['addons_usage'].iteritems():
            if count >= 10:
                db.execute("""INSERT INTO addons_usage (date, guid, installs)
                            VALUES ('%s', '%s', %d)""" % (self.date, guid, count))

        db.close()

if __name__ == '__main__':
    StartupPerformance()