import os
import time
from settings import *

def query(query):
    """Performs a HIVE query and writes it to a file, returning the file path."""
    
    file_path = FILES + '/' + time.time() + '.txt'
    os.system("""hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "%s" > %s""" % (query, file_path))
    
    return file_path

def cleanup(file_path):
    """Delete the file."""
    
    os.remove(file_path)