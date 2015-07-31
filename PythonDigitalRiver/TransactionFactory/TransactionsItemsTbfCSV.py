__author__ = 'root'
import MySQLdb
import warnings
import sys


class TransactionsItemsTbfCSV():
    def make_table(self, cursor):
        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """CREATE TABLE IF NOT EXISTS InTransactList(
                    transID INT(10),
                    amount FLOAT(5,2),
                    currency VARCHAR(4),
                    transTime VARCHAR(30),
                    fileName VARCHAR(20),
                    PRIMARY KEY (transID)
                 )"""
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """CREATE TABLE IF NOT EXISTS notInExternalList(
                    transID INT(10),
                    amount FLOAT(5,2),
                    currency VARCHAR(4),
                    transTime VARCHAR(30),
                    fileName VARCHAR(20),
                    PRIMARY KEY (transID)
                )"""
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

    def to_database(self, cursor, filename):
        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """LOAD DATA LOCAL INFILE %s
                    IGNORE
                    INTO TABLE InTransactList
                    FIELDS TERMINATED BY ','
                    OPTIONALLY ENCLOSED BY '\"'
                    LINES TERMINATED BY '\n'
                    IGNORE 2 LINES
                    (@dummy,@dummy,@dummy,@dummy,@col4,@col5,@col6,@col7)
                    set transID = @col4, amount = @col5 / 100.00, currency = @col6,
                    transTime = @col7, fileName = %s """ % (filename, filename)
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))