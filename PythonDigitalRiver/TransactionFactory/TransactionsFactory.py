__author__ = 'root'
import MySQLdb
import warnings
import sys
import TransactionsItemsTbfCSV
import TransactionsItemsDigRivCSV
import TransactionsItemsSettleCSV

class AbstractTransactionFactory:
    def make_transactions_db(self):
        raise NotImplementedError("""make_transactions_db
             must be defined in subclass""")


class TBFCSVFactory(AbstractTransactionFactory):
    def __init__(self):
        self.factory = TransactionsItemsTbfCSV

    def make_transactions_db(self):
        return TransactionsItemsTbfCSV()


class DigRivCSVFactory(AbstractTransactionFactory):
    def __init__(self):
        self.factory = TransactionsItemsDigRivCSV

    def make_transactions_db(self):
        return TransactionsItemsDigRivCSV()


class SettleSumCSVFactory(AbstractTransactionFactory):
    def __init__(self):
        self.factory = TransactionsItemsSettleCSV

    def make_transactions_db(self):
        return TransactionsItemsSettleCSV()


class FactoryMaker:

    def create_trans_factory(self, filename):
        try:
            exploded = filename.split(".")
            extension = exploded[1]
            if extension == "csv":
                if "tbf.csv" in filename:
                    return TBFCSVFactory
                elif "Settlement_Summary_report" in filename:
                    return SettleSumCSVFactory
                elif "Standard_settlement_Transaction_Report" in filename:
                    return DigRivCSVFactory
                else:
                    sys.stderr.write("[ERROR] File type invalid\n""")
        except MySQLdb.Error, e:
            sys.stderr.write("[ERROR] %d: %s\n""" % (e.args[0], e.args[1]))
