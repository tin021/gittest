<?php

/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/27/15
 * Time: 2:31 PM
 */
class transactions_items_DigRiv_Settle_csv implements transactions_usable{
    /** Function Name: makeTable
     *  Description: Creates the tables that will hold the file's transactions and the transactions
     *               missing from the file.
     * @param $con The mysqli connection to send queries to.
     * @return none
     */
    public function makeTable($con){
        $sql = "CREATE TABLE IF NOT EXISTS SettleFXRates(
            settleID INT(10),
            settleFXRate FLOAT(9,8),
            fileName VARCHAR(60),
            PRIMARY KEY (settleID)
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
                INTO TABLE SettleFXRates
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\n'
                IGNORE 1 LINES
                (@dummy,@dummy,@dummy,@dummy,@col4,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
                @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@col23)
                set settleID = @col4, settleFXRate = @col23, fileName = '$filename'";
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
            //eliminates 0's
            $sql = "UPDATE SettleFXRates
            SET settleFXRate = 1
            WHERE settleFXRate = 0";
            if ($con->query($sql) === FALSE) {
                error_log('[' . date("F j, Y, g:i a e O") . ']' . " Cannot convert to USD" . $con->error . "\n", 3, "./errors.log");
            }
        }
    }
}