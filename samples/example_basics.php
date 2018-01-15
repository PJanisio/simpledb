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

$DB = new DB_MYSQL('localhost', 'root', 'passwd','db', 3306); //connect to database (host, login, password, database, port, debug level)

// $DB = new DB_MYSQL('localhost','root','password', 'mysql', 3306, 2); //connect to database with debug mode = 2

$query1 = $DB->query("SELECT nick FROM players"); //send query

/*
$DB->fetch() will get results from last query.
You can define an argument like fetch($query2)
To get results from another launched query
*/

while ($buffer = $DB->fetch($query1)) //fetch result to array - basic method

	{
	echo $buffer['nick'] . '<br />'; //where 'user' is a name of desired row in table
	}


// Additional functions

echo 'Number of rows from last query: ' . $DB->numRows();
echo '<br />';
echo 'Number of rows from query1: ' . $DB->numRows($query1);
echo '<br />';
echo 'MySQLQueries: ' . $DB->queries; //Number of queries done by script
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