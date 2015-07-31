<?php

function diff($con,$insert,$table1, $table2){
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
        error_log('['.date("F j, Y, g:i a e O").']'."Error in finding transactions missing from $table2: " . $con->error . "\n", 3, "./errors.log");
    }
}

function initDiffTables($con){
    $sql = "CREATE TABLE IF NOT EXISTS notInTbf(
            transID varchar(12),
            transTime varchar(30),
            settleID varchar(12),
            fileName varchar(60),
            PRIMARY KEY (transID)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error creating table: " . $con->error . "\n", 3, "./errors.log");
    }

    $sql = "CREATE TABLE IF NOT EXISTS notInAllTrans(
            transID varchar(12),
            fileName varchar(10),
            PRIMARY KEY (transID)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error creating table: " . $con->error . "\n", 3, "./errors.log");
    }
}
function compare()
{
    date_default_timezone_set('UTC');
    $con = new mysqli('localhost', 'root', NULL);
    // Check connection
    if (mysqli_connect_errno()) {
        error_log('['.date("F j, Y, g:i a e O").']'."Failed to connect to MySQL: " . mysqli_connect_error(), 3, "./errors.log");
    }
    //create table
    $sql = "CREATE DATABASE IF NOT EXISTS myDB;";
    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error in database creation: " . $con->error."\n", 3, "./errors.log");
    }

    $sql = "USE myDB\n";
    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error in using database: ".$con->error."\n", 3, "./errors.log");
    }

    initDiffTables($con);
    diff($con,"notInAllTrans", "tbfTable", "allTrans");
    diff($con,"notInTbf", "allTrans", "tbfTable");
}
?>