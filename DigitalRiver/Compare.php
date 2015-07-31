<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/9/15
 * Time: 3:39 PM
 */

/** Function Name: diff
 *  Description:Compares the two files and creates a table with the transactions that are in $table1 but not
 *              $table2
 * @param $con The mysqli connection to send queries to.
 * @param $insert The table to put the differences into.
 * @param $table1 The table to determine what transactions to check for in table2
 * @param $table2 The table to check what transactions are missing from
 * @return none
 */
function diff($con,$insert,$table1, $table2){
    //Finds the transactions in table1 that are missing from table 2
    $sql = "INSERT IGNORE INTO $insert
            SELECT  *
            FROM    $table1 a
            WHERE   NOT EXISTS
                    (
                    SELECT  null
                    FROM    $table2 b
                    WHERE   a.transID = b.transID
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'." Cannot find transactions missing from $table2: " . $con->error . "\n", 3, "./errors.log");
    }
}

/** Function Name: compare
 *  Description:Compares the two files and creates tables for the transactions missing in each.
 * @param $con The mysqli connection to send queries to.
 * @return none
 */
function compare($con)
{
    date_default_timezone_set('America/Los_Angeles');
    diff($con,"notInExternalList", "InTransactList", "ExTransactList");
    diff($con,"notInInternalList", "ExTransactList", "InTransactList");
}

