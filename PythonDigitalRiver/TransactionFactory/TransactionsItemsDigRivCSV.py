__author__ = 'root'
import MySQLdb
import warnings
import sys


class TransactionsItemsDigRivCSV():
    def make_table(self, cursor):
        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """CREATE TABLE IF NOT EXISTS ExTransactList(
                    payMethod VARCHAR(10),
                    transType VARCHAR(8),
                    transID INT(10),
                    amount FLOAT(5,2),
                    currency VARCHAR (4),
                    transTime VARCHAR(30),
                    settleID INT(10),
                    fileName VARCHAR(60),
                    fee BOOLEAN,
                    PRIMARY KEY (transID)
                )"""
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """CREATE TABLE IF NOT EXISTS notInInternalList(
                    payMethod VARCHAR(10),
                    transType VARCHAR(8),
                    transID INT(10),
                    amount FLOAT(5,2),
                    currency VARCHAR (4),
                    transTime VARCHAR(30),
                    settleID INT(10),
                    fileName VARCHAR(60),
                    fee BOOLEAN,
                    PRIMARY KEY (transID)
                )"""
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))

    def to_database(self, cursor, filename):
        try:
            with warnings.catch_warnings():
                warnings.simplefilter("ignore")
                sql = """IGNORE
                INTO TABLE ExTransactList
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\n'
                IGNORE 1 LINES
                (@dummy,@dummy,@col2,@col3,@col4,@dummy,@col6,@dummy,@col8,@col9,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@col16)
                set payMethod = @col2, transID = @col3, transType = @col4, transTime = @col6,
                amount = @col8, currency = @col9, settleID = @col16, fileName = %s """ % (filename, filename)
                cursor.execute(sql)
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))
