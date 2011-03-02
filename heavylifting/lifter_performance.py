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
            self.commit(data, app)
            self.hive_cleanup(hive_file)
    
    def hive_data(self, app):
        """Performs a HIVE query and writes it to a text file."""
        
        if HIVE_ALTERNATE is not None:
            self.log('Hive alternate file used')
            return HIVE_ALTERNATE
        
        self.log('Starting HIVE query...')
        hive_file = hive.query("""SELECT guid, appos, appversion, tmain, 
                    tfirstpaint, tsessionrestored FROM addons_pings 
                    WHERE ds = '{date}' AND src='{app}' AND guid LIKE 
                    '%972ce4c6-7e08-4474-a285-3208198ce6fd%';""".format(date=self.date, app=app))
        
        self.time_event('hive_data')
        self.log('HIVE data obtained')
        
        return hive_file

    def analyze_performance(self, hive_file):
        """This function reads a file of add-on GUID combinations and start-up
        data and splits it into distributions of start-up seconds by number
        of add-ons installed"""
        
        self.log('Analyzing performance...')
        
        # These GUIDs aren't counted or stored
        not_counted = [
            '%7B972ce4c6-7e08-4474-a285-3208198ce6fd%7D',
            '\d+$',
            '.+%40greasespot.net',
            '\d+%40personas\.mozilla\.org',
        ]
        not_counted = re.compile('(%s)' % '|'.join(not_counted))
        raw_times = {}
        addon_count = {}

        with open(hive_file) as f:
            for line in f:
                _guids, _appos, _appversion, _tmain, _tfirstpaint, _tsessionrestored = line.split()
                guids = []
                
                for guid in _guids.split(','):
                    if not not_counted.match(guid):
                        guids.append(urllib2.unquote(guid))
                        
                num_addons = len(guids)
                
                # Number of occurrences of each time
                if _appos not in raw_times:
                    raw_times[_appos] = {}
                if _appversion not in raw_times[_appos]:
                    raw_times[_appos][_appversion] = {
                        'count': 0,
                        'tmain_distro': collections.defaultdict(int),
                        'tmain_times': [],
                        'tfirstpaint_distro': collections.defaultdict(int),
                        'tfirstpaint_times': [],
                        'tsessionrestored_distro': collections.defaultdict(int),
                        'tsessionrestored_times': [],
                    }
                
                # Times per number of add-ons
                if num_addons not in addon_count:
                    addon_count[num_addons] = collections.defaultdict(list)
                    
                raw_times[_appos][_appversion]['count'] += 1
                
                if _tmain.isdigit():
                    _tmain = int(_tmain)
                    if _tmain >= 0 and _tmain < 3600000:
                        raw_times[_appos][_appversion]['tmain_distro'][int(round(_tmain / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tmain_times'].append(_tmain)
                        addon_count[num_addons]['tmain'].append(_tmain)
                
                if _tfirstpaint.isdigit():
                    _tfirstpaint = int(_tfirstpaint)
                    if _tfirstpaint >= 0 and _tfirstpaint < 3600000:
                        raw_times[_appos][_appversion]['tfirstpaint_distro'][int(round(_tfirstpaint / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tfirstpaint_times'].append(_tfirstpaint)
                        addon_count[num_addons]['tfirstpaint'].append(_tfirstpaint)
        
                if _tsessionrestored.isdigit():
                    _tsessionrestored = int(_tsessionrestored)
                    if _tsessionrestored >= 0 and _tsessionrestored < 3600000:
                        raw_times[_appos][_appversion]['tsessionrestored_distro'][int(round(_tsessionrestored / 1000, 0))] += 1
                        raw_times[_appos][_appversion]['tsessionrestored_times'].append(_tsessionrestored)
                        addon_count[num_addons]['tsessionrestored'].append(_tsessionrestored)
        
        self.log('GUIDs from file processed')
        
        # Do calculations on overall distribution
        for appos, appversions in raw_times.iteritems():
            for appversion in appversions:
                for measure in ['tmain', 'tfirstpaint', 'tsessionrestored']:
                    raw_times[appos][appversion][measure] = self.calculations(raw_times[appos][appversion][measure + '_times'])
                    del raw_times[appos][appversion][measure + '_times']
                    
        
        # Do calculations on add-on count distribution
        for num_addons, measures in addon_count.iteritems():
            for measure, times in measures.iteritems():
                addon_count[num_addons][measure] = self.calculations(times)
        
        self.log('Additional calculations made')
        self.time_event('analyze_performance')
        
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
            'avg': '%.2f' % sum(times) / _len,
            'median': times[int(math.floor(_len / 2))],
        }
    
    def commit(self, data, app):
        """Save our findings to the db."""
        
        valid_version = re.compile('(\w*\d+\.?)+(pre)?$')
        
        db = self.get_database().cursor()
        self.log('Inserting performance_startupdistro2...')
        for appos, appversions in data['raw_times'].iteritems():
            sql = {}
            for appversion in appversions:
                for measure in ['tmain', 'tfirstpaint', 'tsessionrestored']:
                    for stat, value in appversions[appversion][measure].iteritems():
                        sql[measure + '_' + stat] = value
                    sql[measure + '_seconds_distro'] = json.dumps(appversions[appversion][measure + '_distro'])
                
                sql['count'] = appversions[appversion]['count']
                sql['app'] = app
                sql['os'] = appos
                sql['version'] = appversion
                sql['date'] = self.date
                
                if sql['count'] >= 10 and valid_version.match(sql['version']):
                    db.execute("""INSERT INTO performance_startupdistro2 ({keys}) 
                            VALUES ('{vals}')""".format(keys=', '.join(sql.keys()), 
                            vals="', '".join(map(str, sql.values()))))
                    self.log('Inserted {app} {version} {os}'.format(**sql))
                else:
                    self.log('Skipped {app} {version} {os}'.format(**sql))
        
        self.log('Inserting performance_addondistro...')
        db.execute("""INSERT INTO performance_addondistro (date, app, distro)
                    VALUES ('{date}', '{app}', '{distro}')""".format(
                    date=self.date, app=app, distro=json.dumps(data['addon_count'])))

        db.close()

if __name__ == '__main__':
    StartupPerformance()