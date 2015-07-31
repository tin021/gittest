<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/23/15
 * Time: 11:47 AM
 */
abstract class TransactionFactory {
    /** Function Name: makeTransactionsDB
     *  Description: Creates the transactions item object
     * @param none
     * @return A new transactions_items object
     */
    abstract function makeTransactionsDB();

}

class TBFCSVFactory extends TransactionFactory {
    /** Function Name: makeTransactionsDB
     *  Description: Creates the transactions item object
     * @param none
     * @return A new transactions_items_tbf_csv object
     */
    function makeTransactionsDB(){
        return new transactions_items_tbf_csv();
    }
}
class DigRivCSVFactory extends TransactionFactory {
    /** Function Name: makeTransactionsDB
     *  Description: Creates the transactions item object
     * @param none
     * @return A new transactions_items_DigRiv_csv object
     */
    function makeTransactionsDB(){
        return new transactions_items_DigRiv_csv();
    }
}
class SettleSumCSVFactory extends TransactionFactory {
    /** Function Name: makeTransactionsDB
     *  Description: Creates the transactions item object
     * @param none
     * @return A new transactions_items_DigRiv_Settle_csv object
     */
    function makeTransactionsDB(){
        return new transactions_items_DigRiv_Settle_csv();
    }
}

class FactoryMaker{
    /** Function Name: createFactory
     *  Description: Creates the appropriate factory for the filetype
     * @param $filename The file to make the factory for
     * @return A new factory object
     */
    public function createFactory($filename){
        $extension = explode('.',$filename);
        switch($extension[1]) {
            case 'csv' :
                switch(true){
                    case stristr($extension[0], 'tbf') :
                        return new TBFCSVFactory();
                    case stristr($extension[0], 'Settlement_Summary_Report') :
                        return new SettleSumCSVFactory();
                    case stristr($extension[0], 'Standard_Settlement_Transaction_Report'):
                        return new DigRivCSVFactory();
                    default:
                        error_log('[' . date("F j, Y, g:i a e O") . ']' .
                            "File type invalid for $extension\n", 3, "./errors.log");
                }
            default :
                error_log('[' . date("F j, Y, g:i a e O") . ']' .
                    "File type invalid for $extension\n", 3, "./errors.log");
        }
    }
}