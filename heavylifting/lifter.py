import sys
import datetime
import MySQLdb as mysql
import time
from settings import *

class Lifter:
    """Lifter is a base class for all of the differents reports to
    be processed with HIVE data."""
    
    date = None
    db = None
    selected_db = None
    file_path = FILES
    times = {}
    
    def __init__(self, date = None):
        """Constructor to determine the date and kick things off."""
        
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
        """Connects to the given database if it's not already selected."""
        
        if self.selected_db != which:
            self.db = mysql.connect(**DATABASES[which])
            self.db.autocommit(True)
            self.selected_db = which
        
        return self.db
    
    def log(self, msg):
        """Print a message along with the class name."""
        
        print '[%s] %s' % (self.__class__, msg)
    
    def time_event(self, event):
        """Records a time event that will be output at the end."""
        
        self.times[event] = time.time()
    
    def __del__(self):
        """Destructor that shows time events."""
        
        self.time_event('end')
        
        self.log('Finished. %s' % ['%s: %0.2fs' % (e, t - self.times['start']) for e, t in self.times.iteritems() if e != 'start'])
