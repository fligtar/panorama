from __future__ import division # holy crap, python
import collections
import sys
import re
import urllib2
import json
import hive
import operator
import os
from lifter import Lifter
from settings import *

class AddonBangForBuck(Lifter):
    """This lifter determines which add-ons to target for a particular purpose
    in order to get to 80% of Firefox users."""
    
    def lift(self):
        # Make output directory and index
        self.make_dir()
        # Get data
        hive_file = self.hive_data()
        # Get Firefox ADU
        adu = self.get_adu()
        # Get add-on names
        self.addon_names = self.cache_addon_names()
        # Filter add-on combinations from HIVE
        combos = self.filter_combos(hive_file)
        # Calculate and output results
        data = self.calculate_bfb(combos, adu)
        
        self.hive_cleanup(hive_file)
    
    def make_dir(self):
        self.data_path = FILES + '/bfb/' + self.date
        os.mkdir(self.data_path)
        
        f = open(self.data_path + '/index.html', 'w')
        
        f.write("""
        <h1>High Value Add-on Targets Report - {date}</h1>
        <p>In certain situations, we want to know which add-ons to target in 
        order to cover the highest number of Firefox users. In order for a user 
        to be covered, all of the add-ons they have installed must be targeted. 
        This report looks at what add-ons, if targeted, would account for 80% 
        of Firefox users.</p>
        
        <p>To obtain this data, we look at the add-ons a user has installed and 
        group together combinations, e.g. 10000 people have Skype Toolbar and 
        Adblock Plus installed. When have a list of the top combinations, we go 
        down it, pulling out the add-ons until we reach 80% of Firefox's ADU for 
        that day.</p>
        
        <p>We then have a list of the unique add-on GUIDs that, if targeted, 
        would cover 80% of Firefox users in the most effective way.</p>
        
        <p><strong>These reports are CONFIDENTIAL.</strong></p>
        
        <ul>
        <li><a href="addons.html">Unique Add-ons for {date}</a></li>
        <li><a href="combos.html">Top Combinations Showing Percentage Progress 
        for {date}</a> - this page is very large (over 100MB) -- you should 
        right click and save</li>
        </ul>
        """.format(date=self.date))
        
        f.close()
        
    def hive_data(self):
        """Performs a HIVE query and writes it to a text file."""
        
        if HIVE_ALTERNATE is not None:
            self.log('Hive alternate file used')
            return HIVE_ALTERNATE
        
        self.log('Starting HIVE query...')
        hive_file = hive.query("""SELECT guid, COUNT(1) as num 
                    FROM addons_pings WHERE ds = '{date}' AND src='firefox' AND 
                    guid LIKE '%972ce4c6-7e08-4474-a285-3208198ce6fd%' 
                    GROUP BY guid HAVING num > 1 ORDER BY num DESC;""".format(date=self.date))
        
        self.time_event('hive_data')
        self.log('HIVE data obtained')
        
        return hive_file

    def filter_combos(self, hive_file):
        """This function reads a file of add-on GUID combinations and filters
        out undesirable GUIDs, combines duplicates, and sorts them."""
        
        self.log('Filtering combinations...')
        
        # These GUIDs aren't stored in the DB
        not_stored = [
            '\d+$',
            '.+%40greasespot.net',
            '\d+%40personas\.mozilla\.org',
        ]
        
        not_stored = re.compile('(%s)' % '|'.join(not_stored))
        
        combos = collections.defaultdict(int)

        with open(hive_file) as f:
            for line in f:
                _guids, _count = line.split()
                _count = int(_count)
                filtered = []
                
                if _guids == '%7B972ce4c6-7e08-4474-a285-3208198ce6fd%7D':
                    filtered.append('No add-ons installed')
                else:
                    for guid in _guids.split(','):
                        if guid[-1:] == '?':
                            guid = guid[:-1]
                        if not not_stored.match(guid):
                            filtered.append(urllib2.unquote(guid).lower())
                
                combos[','.join(sorted(filtered))] += _count
        
        combos = sorted(combos.iteritems(), key=operator.itemgetter(1), reverse=True)
            
        self.log('Combinations from file processed')
        
        self.time_event('filter_combos')
        
        return combos
    
    def calculate_bfb(self, combos, adu):
        """Takes a sorted list of add-on combinations and retains those that
        make up the top 80% of Firefox users and ouputs them. Then selects
        unique add-ons from that list and outputs them."""
        
        f = open(self.data_path + '/combos.html', 'w')
        f.write('<h1>Add-on combinations accounting for the most users - {date}</h1>'.format(date=self.date))
        
        target_adu = adu * 0.8
        users = 0
        combos_counted = 0
        unique_guids = collections.defaultdict(int)
        
        for _guids, _count in combos:
            if users >= target_adu:
                break
            
            users += _count
            combos_counted += 1
            pretty_names = []
            
            for guid in _guids.split(','):
                unique_guids[guid] += _count
                pretty_names.append(self.get_addon_name(guid))
            
            
            f.write('[%.2f%%] %s<br/>\n' % (users / adu * 100, ', '.join(pretty_names)))
        
        f.close()
        self.log("""{counted} out of {total} combinations counted representing {users} 
        users ({percentage}%)""".format(counted=combos_counted, total=len(combos), 
        users=users, percentage=round(users / adu * 100, 2)))
        self.time_event('calculate_bfb-combos')
        
        f = open(self.data_path + '/addons.html', 'w')
        f.write("""<h1>{count} Unique Add-ons making up {percentage}% of Firefox users 
        - {date}</h1>""".format(count=len(unique_guids), 
        percentage=round(users / adu * 100, 2), date=self.date))
        f.write('<ul>')
        unique_guids = sorted(unique_guids.iteritems(), key=operator.itemgetter(1), reverse=True)
        
        for guid, count in unique_guids:
            f.write('<li>{name} - {users} users\n</li>'.format(name=self.get_addon_name(guid), users=count))
        
        f.write('</ul>')
        f.close()
        self.time_event('calculate_bfb-addons')
                
    
    def get_adu(self):
        """Gets application ADUs for the date from metrics."""
        
        db = self.get_database('metrics').cursor()
        db.execute("""SELECT SUM(adu_count) FROM raw_adu WHERE date = '%s' AND 
        product_name = 'Firefox' AND product_version >= '4.0'""" % self.date)
        adu = int(db.fetchall()[0][0])
        
        self.log('%d active daily users' % adu)
        
        return adu
    
    def get_addon_name(self, guid):
        """Return the name of an add-on in HTML if we know it"""
        
        if guid in self.addon_names:
            name = ''
            if self.addon_names[guid][1] is not None:
                name += """<a href="https://addons.mozilla.org/addon/{id}" 
                title="{guid}">""".format(id=self.addon_names[guid][1], guid=guid)
            name += self.addon_names[guid][0]
            if self.addon_names[guid][1] is not None:
                name += '</a>'
        else:
            name = guid
        
        return name
        
    def cache_addon_names(self):
        """Pull all add-on names from AMO and unhosted sources"""
        
        # Get AMO names
        addon_names = {}
        db = self.get_database('amo').cursor()
        db.execute("""SELECT guid, localized_string as name, addons.id 
        FROM addons INNER JOIN translations ON translations.id = addons.name AND 
        translations.locale = addons.defaultlocale WHERE guid IS NOT NULL AND 
        guid != '' AND addontype_id != 9""")
        
        self.log('%d GUIDs pulled from AMO' % db.rowcount)
        for r in db.fetchall():
            if r[1] is not None:
                addon_names[r[0].lower()] = (r[1], r[2])
        db.close()
        
        # Get non-hosted names
        db = self.get_database('panorama').cursor()
        db.execute("SELECT guid, name FROM _unhosted_guids")
        self.log('%d GUIDs pulled from unhosted' % db.rowcount)
        for r in db.fetchall():
            addon_names[r[0].lower()] = (r[1], None)
        db.close()
        
        return addon_names

if __name__ == '__main__':
    AddonBangForBuck()