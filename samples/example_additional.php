<?php
/*
SimpleDB
by Pawel 'Pavlus' Janisio
License: GPL v3
Example file
*/
require_once ('../mysql_driver.php');

 // include class file

$DB = new DB_MYSQL('localhost', 'root', 'passwd','db', 3306, 2); //connect to database (host, login, password, database, port, debug level)

$DB->optimizeDB(TRUE); //database optimization  TRUE - to get output of optimized tables

// Functions displaying mysql server statistics and variables

$variables = $DB->dbVars();

echo '<pre>';
var_dump($variables);
echo '</pre>';

// Display various statistics

echo $DB->dbStatistics();
echo '<br /><br />';

// Queries syntax display

echo $DB->showSyntaxes();
echo '<br /><br />';

// Show debug level

echo 'Debug Level is now: ' . $DB->showDebugLevel();
echo '<br /><br />';

// Locks tables db, event, func from WRITE access

if ($DB->LockTableRead('db', 'bans', 'func')) echo 'Tables locked from write';
echo '<br /><br />';

// unlocks all locked tables

$unlock = $DB->Unlock();
    if($unlock == TRUE) {
        echo 'Tables unlocked';
    }
?>