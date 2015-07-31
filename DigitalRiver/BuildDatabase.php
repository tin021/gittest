<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/9/15
 * Time: 3:39 PM
 */

/** Function Name: createIgnoreList
 *  Description: Creates the table to store files that are already added to the db and should
 *               be ignored.
 * @param $con The mysqli connection to send queries to
 * @return none
 */
function createIgnoreList($con){
    $sql = "CREATE TABLE IF NOT EXISTS ignoreList(
            fileName varchar(70),
            PRIMARY KEY (fileName)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'." Cannot create table: " . $con->error . "\n", 3, "./errors.log");
    }
}

/** Function Name: transFee
 *  Description: Deducts the transaction fee from the amount paid for each Boleto or Credit
 *               card transaction.
 * @param $con The mysqli connection to send queries to
 * @param $table The table to deduct transaction fees from
 * @return none
 */
function transFee($con,$table) {
    //Subtracts transaction fees from Boleto and Credit card transactions
    $sql = "UPDATE $table
            SET amount = amount - 1.50, fee = TRUE WHERE payMethod = 'Boleto' AND amount > 0 AND fee = FALSE";
    if ($con->query($sql) === FALSE) {
        error_log('[' . date("F j, Y, g:i a e O") . ']' . " Cannot take out transaction fees for Boleto: " . $con->error . "\n", 3, "./errors.log");
    }
    $sql = "UPDATE $table
            SET amount = amount - (0.06*amount), fee = TRUE WHERE transType = 'Credit' AND amount > 0 AND fee = FALSE";
    if ($con->query($sql) === FALSE) {
        error_log('[' . date("F j, Y, g:i a e O") . ']' . " Cannot take out transaction fees for Credit: " . $con->error . "\n", 3, "./errors.log");
    }
}

/** Function Name: toUSD
 *  Description: Converts the transaction amounts to USD currency
 * @param $con The mysqli connection to send queries to.
 * @param $settleID The specified settlement ID
 * @param $FXRate The rate to use to convert from the original currency
 * @return none
 */
function toUSD($con,$settleID, $FXRate) {
    //Subtracts transaction fees from Boleto and Credit card transactions
    $sql = "UPDATE ExTransactList
            SET amount = amount/'$FXRate', currency = 'USD'
            WHERE   settleID = '$settleID' AND currency = 'BRL'
            ";
    if ($con->query($sql) === FALSE) {
        error_log('[' . date("F j, Y, g:i a e O") . ']' . " Cannot convert to USD" . $con->error . "\n", 3, "./errors.log");
    }
}
/** Function Name: buildDB
 *  Description: Creates the database tables from files in a specified folder.
 * @param $con The mysqli connection to send queries to.
 * @return none
 */
function buildDB($con)
{
    createIgnoreList($con);
    $factoryMaker = new FactoryMaker();
    $fileNames = scandir(TRANS_PATH);
    foreach ($fileNames as $file) {
        if(($file[0] != '.')) {
            $fullPath = TRANS_PATH.$file;
            $factory = $factoryMaker->createFactory($file);
            $dbMaker = $factory->makeTransactionsDB();
            $dbMaker->makeTable($con);
            $dbMaker->toDatabase($con,$fullPath);
        }
    }
    $sql = "SHOW TABLES LIKE 'SettleFXRates' ";
    if(mysqli_num_rows($con->query($sql)) !== 0) {
        $sql = 'SELECT * FROM SettleFXRates';
        $result = $con->query($sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($data as $settlement) {
            toUSD($con, $settlement['settleID'], $settlement['settleFXRate']);
        }
    }
    transFee($con, 'ExTransactList');
}

