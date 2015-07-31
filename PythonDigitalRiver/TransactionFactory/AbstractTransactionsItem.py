__author__ = 'root'


class AbstractTransactionsItem:
    def make_table(self):
        raise NotImplementedError("""make_transactions_db
             must be defined in subclass""")

    def to_database(self):
        raise NotImplementedError("""make_transactions_db
             must be defined in subclass""")