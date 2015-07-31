<?php

/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/23/15
 * Time: 5:01 PM
 */
class transactions_items_tbf_csv implements transactions_usable{
    /** Function Name: makeTable
     *  Description: Creates the tables that will hold the file's transactions and the transactions
     *               missing from the file.
     * @param $con The mysqli connection to send queries to.
     * @return none
     */
    public function makeTable($con){
        $sql = "CREATE TABLE IF NOT EXISTS InTransactList(
            transID INT(10),
            amount FLOAT(5,2),
            currency VARCHAR(4),
            transTime VARCHAR(30),
            fileName VARCHAR(20),
            PRIMARY KEY (transID)
            )";
        if ($con->query($sql) === FALSE) {
            error_log('['.date("F j, Y, g:i a e O").']'." Cannot create table: " . $con->error . "\n", 3, "./errors.log");
        }

        $sql = "CREATE TABLE IF NOT EXISTS notInExternalList(
            transID INT(10),
            amount FLOAT(5,2),
            currency VARCHAR(4),
            transTime VARCHAR(30),
            fileName VARCHAR(20),
            PRIMARY KEY (transID)
            )";
        if ($con->query($sql) === FALSE) {
            error_log('['.date("F j, Y, g:i a e O").']'." Cannot create table: " . $con->error . "\n", 3, "./errors.log");
        }
    }

    /** Function Name: toDatabase
     *  Description: Uploads the file's transactions to the database
     * @param $con The mysqli connection to send queries to.
     * @param $filename The file to extract data from
     * @return none
     */
    public function toDatabase($con,$filename){
        //Loads the tbf file's csv data into a MySQL table
       // $this->makeTable($con);
        $sql = "LOAD DATA LOCAL INFILE '$filename'
            IGNORE
            INTO TABLE InTransactList
            FIELDS TERMINATED BY ','
            OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY '\n'
            IGNORE 2 LINES
            (@dummy,@dummy,@dummy,@dummy,@col4,@col5,@col6,@col7)
            set transID = @col4, amount = @col5 / 100.00, currency = @col6, transTime = @col7, fileName = '$filename' ";
        if($con->query($sql) === FALSE) {
            error_log('['.date("F j, Y, g:i a e O").']'." Something went wrong in uploading CSV to database:" . $con->error . "\n", 3, "./errors.log");
        }
    }
}