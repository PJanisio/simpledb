<?php
/*
SimpleDB
by Pawel 'Pavlus' Janisio
License: GPL v3
Example file
*/
require_once ('../mysql_driver.php');

 // include class file
// How to connect and get results from any mysql table

$DB = new DB_MYSQL('localhost', 'root', 'password','database_name', 3306, 2); //connect to database (host, login, password, database, port, debug level)

// $DB = new DB_MYSQL('localhost','root','password', 'mysql', 3306, 2); //connect to database with debug mode = 2

$query1 = $DB->query('SELECT nick FROM players'); //send query
$query2 = $DB->query('SELECT password FROM user'); //send query

// First option -> u use only $DB->fetch() and $buffer will get result from LAST query

while ($buffer = $DB->fetch()) //fetch result to array - basic method

	{
	echo $buffer['nick'] . '<br />'; //where 'user' is a name of desired row in table
	}

// Second option -> you can choose, from which query you want to get result $DB->fetch($query2)

while ($buffer = $DB->fetch($query2))
	{
	echo $buffer['nick'] . '<br />';
	}

// Additional functions

echo 'Number of rows from last query: ' . $DB->numRows();
echo '<br />';
echo 'Number of rows from query3: ' . $DB->numRows($query3);
echo '<br />';
echo 'Queries: ' . $DB->queries; //Number of queries done by script
echo '<br /><br />';
echo 'Last MySQL error: ' . $DB->showError(); //Show last mysql error
echo '<hr>';

// Sample error output

if ($DB->countErrors() > 0) //if error exist

	{
	echo '<br /><u>Detailed info about errors:</u><br />';
	echo '<b>Caused by: </b>' . $_SESSION['error_sdb'] . '<br />'; //class file error
	echo '<b>Server at: </b>' . $_SESSION['error_env'] . '<br />'; //mysql host
	echo '<b>Mysql user: </b>' . $_SESSION['error_user'] . '<br />'; //actual mysql user
	echo '<b>Error time: </b>' . $_SESSION['error_time'] . '<br /><br />'; //error time
	echo '<b>Full backrace: </b><br />' . $_SESSION['output_backtrace'] . '<br />'; //fully backtrace reproducing error
	}

?>