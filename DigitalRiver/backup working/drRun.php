#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/13/15
 * Time: 1:40 PM
 */

require_once('BuildDatabase.php');
require_once('Compare.php');
//echo("waiting on build\n");

buildDB();
//echo("Database built\n");
compare();
//compareTest();
//echo("Missing transactions saved\n");
$output = fopen("lastRun.txt", "w");
date_default_timezone_set('UTC');
fwrite($output,"Last run time was " . date("h:i:sa"));
fclose($output);
