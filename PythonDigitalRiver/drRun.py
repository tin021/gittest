#!/usr/bin/python
import MySQLdb
import warnings
import sys
import os, time

from Compare import compare
from Calculate import group_month
from BuildDatabase import build_db
import globals


con = MySQLdb.connect(host="localhost", user="root", passwd="some_pass")
cursor = con.cursor()
try:
    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        sql = "CREATE DATABASE IF NOT EXISTS TransactionsDB;"
        cursor.execute(sql)
except MySQLdb.Error, e:
    sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

try:
    sql = 'USE TransactionsDB'
    cursor.execute(sql)
except MySQLdb.Error, e:
    sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

build_db(con)
#compare(con)
#group_month(con)
con.close()

