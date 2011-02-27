import os
import time
from settings import *


def query(query):
    file_path = FILES + '/' + time.time() + '.txt'
    os.system("""hive --auxpath '/usr/lib/hive/lib/hive_contrib.jar' -e "%s" > %s""" % (query, file_path))
    
    return file_path

def cleanup(file_path):
    os.remove(file_path)