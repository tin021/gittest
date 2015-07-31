__author__ = 'root'
import MySQLdb
import warnings
import sys


class TransactionsItemsSettleCSV():
    def make_table(self, cursor):
        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """Create Table IF NOT EXISTS SettleFXRates(
                      settleID INT (10),
                      settleFXRate FLOAT (9,8),
                      fileName VARCHAR (60),
                      PRIMARY KEY (settleID)
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
                INTO TABLE SettleFXRates
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\n'
                IGNORE 1 LINES
                (@dummy,@dummy,@dummy,@dummy,@col4,@dummy,@dummy,@dummy,@dummy,
                @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
                @dummy,@dummy,@dummy,@dummy,@dummy,@col23)
                set settleID = @col4, settleFXRate = @col23,
                fileName = %s """ % (filename, filename)
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """INSERT INTO ignoreList
                (fileName)
                VALUES
                (%s)""" % filename
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """UPDATE SettleFXRates
                SET settleFXRate = 1
                WHERE settleFXRate = 0"""
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))