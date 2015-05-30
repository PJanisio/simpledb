<?php
/*
SimpleDB
by Pawel 'Pavlus' Janisio
License: GPL v3
Example file
*/

require_once('../mysql_driver.php'); //include class file

$DB = new DB_MYSQL('localhost', '3306', 'root', 'password', 'mysql'); //connect to database (host, port, user, password, database)

//$DB = new DB_MYSQL('localhost','3306','root','password', 'mysql', 2); //connect to database with debug mode = 2


//$DB->createDB('forgotten', 'latin1'); //create database 'forgotten' with latin1 default charset
//echo '<br>';

$DB->optimizeDB(TRUE); //database optimization  TRUE - to get output of optimized tables

//Functions displaying mysql server statistics and variables

$variables = $DB->dbVars();

echo $variables[0];
echo '<br><br>';
echo $variables[1];
echo '<br><br>';
echo $variables[2];
echo '<br><br>';
echo $variables[3];
echo '<br><br>';
echo $variables[4];
echo '<br><br>';

//Display various statistics

echo $DB->dbStatistics();
echo '<br><br>';

//Queries syntax display

echo $DB->showSyntaxes();
echo '<br><br>';

//Show debug level

echo 'Debug Level is now: ' . $DB->showDebugLevel();
echo '<br><br>';

//Locks tables db, event, func from WRITE access

if ($DB->LockTableRead('db', 'bans', 'func'))
    echo 'Tables locked from write';
echo '<br><br>';


//unlocks all locked tables  

$DB->Unlock();
?>
