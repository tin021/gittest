<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/9/15
 * Time: 3:39 PM
 */
function csvToAllTrans ($con ,$infile) {
    $fullPath = "./DRfiles/".$infile;
    $sql = "LOAD DATA LOCAL INFILE '$fullPath'
            IGNORE
            INTO TABLE allTrans
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\r\n'
            IGNORE 1 LINES
            (@dummy,@dummy,@dummy,@col3,@dummy,@dummy,@col6,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@col16)
            set transID = @col3, transTime = @col6, settleID = @col16, fileName = '$infile' ";
    if($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Something went wrong in uploading CSV to database: " . $con->error . "\n", 3, "./errors.log");
    }
   // fclose($fileHandle);
}

function csvToTbfTable($con){
    //$tbf = './DRfiles/20150514_Standard_Settlement_Transaction_Report_1020.csv';
    $tbf = './tbf.csv';
    $sql = "LOAD DATA LOCAL INFILE '$tbf'
            IGNORE
            INTO TABLE tbfTable
            FIELDS
                TERMINATED BY ','
                ESCAPED BY '\"'
            LINES TERMINATED BY '\n'
            IGNORE 2 LINES
            (@dummy,@dummy,@dummy,@dummy,@col4)
            set transID = @col4, fileName = '$tbf' ";
    if($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Something went wrong in uploading CSV to database: " . $con->error . "\n", 3, "./errors.log");
    }
}

function init($con){
    //create db
    $sql = "CREATE DATABASE IF NOT EXISTS myDB;";
    if ($con->query($sql) === FALSE) {
        error_log("Error in database creation: " . $con->error . "\n", 3, "./errors.log");
    }

    $sql = "USE myDB\n";
    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error in using database: ".$con->error."\n", 3, "./errors.log");
    }

    //build table from DR file values
    $sql = "CREATE TABLE IF NOT EXISTS allTrans(
            transID varchar(12),
            transTime varchar(30),
            settleID varchar(12),
            fileName varchar(60),
            PRIMARY KEY (transID)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error creating table: " . $con->error . "\n", 3, "./errors.log");
    }
    //build table from tbf.csv values
    $sql = "CREATE TABLE IF NOT EXISTS tbfTable(
            transID varchar(12),
            fileName varchar(10),
            PRIMARY KEY (transID)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error creating table: " . $con->error . "\n", 3, "./errors.log");
    }

    $sql = "CREATE TABLE IF NOT EXISTS ignoreList(
            fileName varchar(60),
            PRIMARY KEY (fileName)
            )";

    if ($con->query($sql) === FALSE) {
        error_log('['.date("F j, Y, g:i a e O").']'."Error creating table: " . $con->error . "\n", 3, "./errors.log");
    }
}

function buildDB()
{
    date_default_timezone_set('UTC');
    $con = new mysqli('localhost', 'root', NULL);
    // Check con
    if (mysqli_connect_errno()) {
        error_log("Failed to connect to MySQL: " . mysqli_connect_error(), 3, "./errors.log");
    }

    init($con);

    $fileNames = scandir("./DRfiles");
    foreach ($fileNames as $file) {
        if (strpos($file, "Standard_Settlement_Transaction_Report") !== FALSE) {
            $sql = "SELECT * FROM ignoreList WHERE fileName='$file' ";
            if(mysqli_num_rows($con->query($sql)) === 0) {
                echo("Adding $file contents to allTrans table\n");
                csvToAllTrans($con, $file);
                $sql = "INSERT INTO ignoreList
                (fileName)
                VALUES
                ('$file')";
                if ($con->query($sql) === FALSE) {
                    error_log('['.date("F j, Y, g:i a e O").']'."Error in adding $file to ignore list $con->error\n", 3, "./errors.log");
                }
            }
        }
    }

    csvToTbfTable($con);

    $con->close();
}
?>
