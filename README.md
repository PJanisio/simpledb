=SimpleDB 0.2.3=
`Code simple - solute everything` 

Mysql driver class (wrapper) for PHP5.
Clearly programmed, very easy to use in your application.

Not overengineered!

----

===Changelog===
 * new version *0.2.3* available (27 Mar 2012)
 * new version 0.2.2a available (02 Nov 2011)
 * new version 0.2.2 available (12 Nov 2010)
 * new version 0.2.1 available (19 Feb 2010)
 * new version 0.2.0 available (09 Jan 2010)
 * new version 0.1.9 available (23 Nov 2009)
 * new version 0.1.8 available (12 Nov 2009)
 * repository clear and reset (10 Nov 2009)
 * new version 0.1.7 available (10 Oct 2009) 

----

===Class Methods===


 * connect, query, fetch to array (MYSQL_ASSOC, MYSQL_NUM and MYSQL_BOTH)
 * affected records, query count, query time
 * empty queries (no resource!)
 * mysql variables, statistics, last errors
 * locking, unlocking tables
 * fast database optimization
 * force disconnect and reconnect possibility
 * debugging with full backtrace!

----

===Simple example - getting result from MySQL database===


This is the most simples example, shows how to get results from database.

{{{

require_once('mysql_driver.php'); //include class file


$DB = new DB_MYSQL('localhost','3306','root','password', 'database'); //connect to database


$DB->query('SELECT host FROM user'); //send query


	$buffer = $DB->fetch()) //fetch result to array - basic method (from last query)

	echo $buffer['host']; //where 'host' is a name of desired row in table

}}}

----

But is better to handle variable on query to make sure that you are fetching from right query!


{{{

$query1 = $DB->query('SELECT host FROM user'); //send query
$query2 = $DB->query('SELECT password FROM user'); //send query
$query3 = $DB->query('SELECT * FROM help_topic'); //send query :)
$query4 = $DB->query('SELECT user FROM user'); //send query :)

$buffer = $DB->fetch($query2)) 

	echo $buffer['password'].'<br>'; 


}}}

Now you are sure, that result will come from: 


{{{
$query2 = $DB->query('SELECT password FROM user'); //send query
}}}


----


===Would you like to know number of affected rows by your query?=== 
No problem :)


{{{
echo 'Affected rows: '.$DB->numRows(); //List affected records by last query

}}}

----


===Hmm, maybe you want to count queries and and them to your site footer? Here you go:===

{{{
echo 'Queries: '.$DB->queries; //Number of queries done by script

}}}


Simple yes? All functions are simple :)

----


===You are sitting in your admin panel and doing nothing, lets optimize our database!===


{{{
$DB->optimizeDB(TRUE);  //database optimization
}}}


What?! That`s all? Yes :) You will get result like this:

accounts - Optimized

bans - Optimized

columns_priv - Optimized

db - Optimized

func - Optimized

global_storage - Optimized

guild_invites - Optimized

guild_ranks - Optimized

guilds - Optimized

help_category - Optimized

help_keyword - Optimized

houses - Optimized

...and the rest of tables 

----

===I know, you want to see all queries done by the script, and time of executions===

First, you will have to enable debug mode (0-2):

To do this, you need to add last parameter while initiating class:
{{{
$DB = new DB_MYSQL('localhost','3306','root','password', 'database', 2);
//This digit in the last argument means that debug level is now equal 2
}}}

Now after some queries and other functions you can paste this:

{{{
echo $DB->showSyntaxes();
}}}

The output will look like that:

0.0012 sec. SHOW TABLES

0.0909 sec. OPTIMIZE TABLE accounts

0.0151 sec. OPTIMIZE TABLE bans

0.0006 sec. OPTIMIZE TABLE columns_priv

0.0003 sec. OPTIMIZE TABLE db

0.0002 sec. OPTIMIZE TABLE func

0.0116 sec. OPTIMIZE TABLE global_storage

0.0157 sec. OPTIMIZE TABLE house_data

etc...


----

===You made a mistake?===

Find out detailed backtrace of your errors:

{{{
Detailed info about errors:
Caused by: /home/pavlus/pubic_html/mysql_driver.php:119
Server at: localhost
Mysql user: pavlus_sql
Error time: 12-11-2010, 21:37:41

Full backrace: 
File: /home/pavlus/pubic_html/mysql_driver.php (Line: 191)
Function: throwError
Args: 0
File: /home/pavlus/pubic_html/example_basics.php (Line: 22)
Function: query
Args: SELECT password FROM use*sr*
}}}

----

===Any issue?===

Leave your feedback here: [http://code.google.com/p/simplemysqlclass/issues/list]

----

===Already using this class?===

Please insert this small button: http://otsoft.pl/images/buttons/simpledb-class.png
in your page footer.


----

===Want to join the project?===


Code for SQLite and PostrgeSQL developement?

Mail me: *p.janisio@gmail.com*

----
