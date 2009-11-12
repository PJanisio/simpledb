<?php
  /*
SimpleDB
by Pawel 'Pavlus' Janisio
License: GPL v3
Example file
*/

require_once('../mysql_driver.php'); //include class file

$DB = new DB_MYSQL; //connect to database 


//$DB->createDB('forgotten', 'latin1'); //create database 'forgotten' with latin1 default charset
//echo '<br>';

$DB->optimizeDB();  //database optimization

//$DB->importDumpexec('/home/pavlus/public_html/class_database/samples/mysqldump.sql'); //import sql file REMEMBER to use global path!

//Functions displaying mysql server statistics and variables

echo $DB->dbVars();
echo '<br>';

//Display various statistics

echo $DB->dbStatistics();
echo '<br>';

//Queries syntax display

echo $DB->showSyntaxes();
echo '<br>';

//Show debug level

echo $DB->showDebugLevel();
echo '<br>';

//Locks tables db, event, func from WRITE access

if($DB->LockTableRead('db','bans','func') == TRUE)
echo 'Tables locked from write';
echo '<br>';


//unlocks all locked tables  

$DB->Unlock();
?>
