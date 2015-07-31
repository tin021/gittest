#!/usr/bin/php
<?php
/**
 * Created by Timmy Ngo.
 * User: root
 * Date: 7/13/15
 * Time: 1:40 PM
 */
//Required files
require_once('BuildDatabase.php');
require_once('Compare.php');
require_once('Calculate.php');
require_once('./TransactionFactory/TransactionInterface.php');
require_once('./TransactionFactory/TransactionsFactory.php');
require_once('./TransactionFactory/transactions_items_tbf_csv.php');
require_once('./TransactionFactory/transactions_items_DigRiv_csv.php');
require_once('./TransactionFactory/transactions_items_DigRiv_Settle_csv.php');

//constants
//change this to specify the folder with the transactions files
define('TRANS_PATH', './DRfiles/');

date_default_timezone_set('America/Los_Angeles');
$start = time();
//connect to mysql
$con = new mysqli('localhost', 'root', NULL);
// Check connection
if (mysqli_connect_errno()) {
    error_log('['.date("F j, Y, g:i a e O").']'." Failed to connect to MySQL: " . mysqli_connect_error(), 3, "./errors.log");
}
//Create database
$sql = "CREATE DATABASE IF NOT EXISTS TransactionsDB;";
if ($con->query($sql) === FALSE) {
    error_log('['.date("F j, Y, g:i a e O").']'." Cannot create database: " . $con->error."\n", 3, "./errors.log");
}

//Use database
$sql = "USE TransactionsDB\n";
if ($con->query($sql) === FALSE) {
    error_log('['.date("F j, Y, g:i a e O").']'." Cannot use database: ".$con->error."\n", 3, "./errors.log");
}

buildDB($con);
compare($con);
groupMonth($con);

$end = time();
//Log last time the program was run
$output = fopen("lastRun.txt", "w");
fwrite($output,"Last run time was " . date("h:i:sa")." Time Taken: ".($end-$start)." Start was $start, end was $end");
fclose($output);
$con->close();