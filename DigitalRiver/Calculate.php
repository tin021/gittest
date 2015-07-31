<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/20/15
 * Time: 1:59 PM
 */

/** Function Name: sumMonth
 *  Description: Sums the amount made from each transaction for the specified month.
 * @param $con The mysqli connection to send queries to.
 * @param $monthDate The month to sum transactions for.
 * @return none
 */
function sumMonth($con, $monthDate) {
    //Sums the total payments for the given month
    $sql = "UPDATE months
            SET totAmount = (SELECT SUM(amount) FROM notInInternalList WHERE SUBSTRING(transTime,1,7) = '$monthDate' )
            WHERE month = '$monthDate'";
    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."
        Cannot sum for $monthDate: " . $con->error . "\n", 3, "./errors.log");
    }
}



/** Function Name: groupMonth
 *  Description: Initializes the months table and fills it with the total transaction
 *               revenue for that month.
 * @param $con The mysqli connection to send queries to.
 * @return none
 */
function groupMonth($con) {
    date_default_timezone_set('America/Los_Angeles');
    $table = "ExTransactList";
    //Sets up table that will hold the list of months that the transactions span across
    $sql =  "CREATE TABLE IF NOT EXISTS months(
            month VARCHAR(7),
            totAmount FLOAT(12,2),
            PRIMARY KEY (month)
            )";
    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'." Cannot create table: " . $con->error . "\n", 3, "./errors.log");
    }

    //Puts distinct months from transaction times from the Digital River Reports into a table
    $sql = "INSERT IGNORE INTO months
            SELECT DISTINCT SUBSTRING(transTime,1,7), 0
            FROM $table";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'." Cannot group by month: " . $con->error . "\n", 3, "./errors.log");
    }

    $sql = "SELECT month FROM months";

    if ($result = $con->query($sql)) {
        while ($row = $result->fetch_row()) {
            sumMonth($con,$row[0]);
        }
    }
}

