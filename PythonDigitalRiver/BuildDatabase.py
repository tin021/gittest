__author__ = 'root'
import MySQLdb
import sys

from TransactionFactory.TransactionsFactory import FactoryMaker

def build_db(con):
    try:
        filename = './DRfiles/20150514_Settlement_Summary_Report_1020.csv'
        factory_maker = FactoryMaker.create_trans_factory(filename)
        factory = getattr(factory_maker, 'factory')()
    except MySQLdb.Error, e:
        sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))
