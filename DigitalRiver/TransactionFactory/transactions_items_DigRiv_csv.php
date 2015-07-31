<?php

/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/23/15
 * Time: 5:01 PM
 */
class transactions_items_DigRiv_csv implements transactions_usable{
    /** Function Name: makeTable
     *  Description: Creates the tables that will hold the file's transactions and the transactions
     *               missing from the file.
     * @param $con The mysqli connection to send queries to.
     * @return none
     */
    public function makeTable($con){
        $sql = "CREATE TABLE IF NOT EXISTS ExTransactList(
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
            )";
        if ($con->query($sql) === FALSE) {
            error_log('['.date("F j, Y, g:i a e O").']'." Cannot create table: " . $con->error . "\n", 3, "./errors.log");
        }

        $sql = "CREATE TABLE IF NOT EXISTS notInInternalList(
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
        $sql = "SELECT * FROM ignoreList WHERE fileName='$filename' ";
        if((mysqli_num_rows($con->query($sql)) === 0)) {
            $sql = "LOAD DATA LOCAL INFILE '$filename'
                IGNORE
                INTO TABLE ExTransactList
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\n'
                IGNORE 1 LINES
                (@dummy,@dummy,@col2,@col3,@col4,@dummy,@col6,@dummy,@col8,@col9,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@col16)
                set payMethod = @col2, transID = @col3, transType = @col4, transTime = @col6, amount = @col8, currency = @col9, settleID = @col16, fileName = '$filename', fee = FALSE ";

            if ($con->query($sql) === FALSE) {
                error_log('[' . date("F j, Y, g:i a e O") . ']' . "Something went wrong in uploading CSV to database: " . $con->error . "\n", 3, "./errors.log");
            }
            $sql = "INSERT INTO ignoreList
                    (fileName)
                    VALUES
                    ('$filename')";
            if ($con->query($sql) === FALSE) {
                error_log('[' . date("F j, Y, g:i a e O") . ']' . " Cannot add $filename to ignore list $con->error\n", 3, "./errors.log");
            }
        }
    }
}