<?php

/*
SimpleDB
by Pawel 'Pavlus' Janisio
License: GPL v3
Example file
*/

/*
In this example i will show you how to change host, user or database during script execution
without changing initial variables inside mysql class.

Under that mysql_change_user does not exist till php3, i have to introduce this one.

Remember that you have to use forceDisconnect() function first, than reconnect.

Dont use forceDisconnect() function when you DONT want to reconnect, class automatically will close connection at the
end of script (__destruct function).

*/


require_once('../mysql_driver.php'); //include class file

$DB = new DB_MYSQL; //connect to database
$DB->forceDisconnect(); //closing connection

$DB->reconnect('localhost', '3306', 'login', 'password', 'database'); //reconnect with changed inputs such as user, host or database



?>
