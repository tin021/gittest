<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/23/15
 * Time: 11:46 AM
 */

/** Function Name: transactions_usable
 *  Description: An interface for a transactions item.
 * @param none
 * @return none
 */
interface transactions_usable{
    /** Function Name: makeTable
     *  Description: Creates the tables that will hold the file's transactions and the transactions
     *               missing from the file.
     * @param $con The mysqli connection to send queries to.
     * @return none
     */
    public function makeTable($con);

    /** Function Name: toDatabase
     *  Description: Uploads the file's transactions to the database
     * @param $con The mysqli connection to send queries to.
     * @param $filename The file to extract data from
     * @return none
     */
    public function toDatabase($con,$filename);
    //public function getEscape();
}

