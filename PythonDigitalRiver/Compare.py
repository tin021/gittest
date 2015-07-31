__author__ = 'root'
import MySQLdb
import sys


def compare(con):
    try:
        return 1
    except MySQLdb.Error, e:
        sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))