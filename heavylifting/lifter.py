import sys
import datetime
import MySQLdb as mysql
import time
from settings import *

class Lifter:
    date = None
    db = None
    selected_db = None
    file_path = FILES
    times = {}
    
    def __init__(self, date = None):
        self.time_event('start')
        
        if date is None:
            try:
                date = sys.argv[1]
            except:
                date = datetime.date.today() - datetime.timedelta(1)
        self.date = date
        
        self.log('Starting for %s' % self.date)
        self.lift()
        
    def get_database(self, which='panorama'):
        if self.selected_db != which:
            self.db = mysql.connect(**DATABASES[which])
            self.db.autocommit(True)
            self.selected_db = which
        
        return self.db
    
    def log(self, msg):
        print '[%s] %s' % (self.__class__, msg)
    
    def time_event(self, event):
        self.times[event] = time.time()
    
    def __del__(self):
        self.time_event('end')
        
        self.log('Finished. %s' % ['%s: %0.2fs' % (e, t - self.times['start']) for e, t in self.times.iteritems() if e != 'start'])