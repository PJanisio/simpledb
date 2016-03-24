<?php
/*
SimpleDB - Mysql driver class
Version: 0.2.4
Author: Pawel 'Pavlus' Janisio
License: GPL v3
GIT: https://github.com/PJanisio/simpledb.git
*/
class DB_MySQL

	{
	/*
	Exit while error.
	This variable when ednabled (1) terminates your program in case of ay error
	Default value is 0
	int
	*/
	private $exit = 0;
	protected $connection = NULL;
	protected $database = NULL;
	protected $error = NULL;
	protected $disconnect = NULL;
	private $db_host = '';
	private $db_port = 0;
	private $db_user = '';
	private $db_password = '';
	private $db_database = '';
	private $fetched = array();
	private $rows = 0;
	private $result = NULL;
	public $queries = 0;

	public $errors = 0;

	public $exe = NULL;

	public

	function __construct($db_host, $db_port, $db_user, $db_password, $db_database, $debug_level = NULL)
		{

		// assign variables

		$this->db_host = $db_host;
		$this->db_port = $db_port;
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		$this->db_database = $db_database;
		if (isset($debug_level)) $this->debugLevel = $debug_level;
		  else $this->debugLevel = 1;
		switch ($this->debugLevel)
			{
		case 0:
			error_reporting(0);
			break;

		case 1:
			error_reporting(E_ALL ^ E_NOTICE);
			break;

		case 2:
			error_reporting(E_ALL);
			break;

		default:
			$error_reporting(E_ALL ^ E_NOTICE);
			$this->debugLevel = 1;
			}

		// connection starts here

		$this->connection = @mysql_connect($this->db_host . ':' . $this->db_port, $this->db_user, $this->db_password);
		if ($this->connection)
			{
			$this->database = @mysql_select_db($this->db_database);
			}

		if (!$this->connection)
			{
			$this->throwError($this->exit);
			return FALSE;
			}
		  else return TRUE;
		if (!$this->database)
			{
			$this->throwError($this->exit);
			return FALSE;
			}
		  else return TRUE;
		}

	/*
	Function throwing error.
	Also if you want to terminate your script after an error, change $exit value to (1) at the top of the class
	*/
	public

	function throwError($exit)
		{
		if (mysql_error() != NULL)
			{
			$_SESSION['error_env'] = $_SERVER['SERVER_NAME'];
			$_SESSION['error_script'] = $_SERVER['PHP_SELF'];
			$_SESSION['error_sdb'] = __FILE__ . ':' . __LINE__;
			$_SESSION['error_user'] = $this->db_user;
			$_SESSION['error_time'] = date("j-m-Y, H:i:s");
			$_SESSION['error_num'] = mysql_errno();
			$_SESSION['error_syn'] = mysql_error();
			$_SESSION['output_backtrace'] = $this->parseBacktrace(debug_backtrace());
			echo $this->error = 'MySQL Error #' . mysql_errno() . ' Syntax: ' . mysql_error() . '<br />';
			$this->errors++;
			if ($this->exit == 1)
				{

				// echo 'Application terminated';

				exit();
				}
			}
		}

	/*
	This function throws full backtrace if error occurs.
	Fixed displaying connection details like host, password...
	*/
	public

	function parseBacktrace($raw)
		{
		$forbidden = array(
			1045,
			2003
		);
		foreach($raw as $entry)
			{
			$this->backtrace.= "File: " . $entry['file'] . " (Line: " . $entry['line'] . ")<br />";
			$this->backtrace.= "Function: " . $entry['function'] . "<br />";
			if (!in_array($_SESSION['error_num'], $forbidden)) //why would we parse login or password data? :)
				{
				$this->backtrace.= "Args: " . implode(", ", $entry['args']) . "<br />";
				}
			}

		return $this->backtrace;
		}

	/*
	Make query to database
	Default - with result
	$resource = 0 - only execution, no results avaible
	*/
	public

	function query($syntax, $resource = NULL)
		{
		if ($this->connection)
			{
			$this->syntax = $syntax;
			if (!isset($resource))
				{
				$this->resource = 1;
				}
			  else
				{
				$this->resource = $resource;
				}

			if ($this->debugLevel == 2)
				{
				$start = $this->getTime();
				}

			if ($this->resource == 1)
				{
				$this->result = @mysql_query($this->syntax);
				if (!$this->result)
					{
					$this->throwError($this->exit);
					return FALSE;
					}
				}
			  else
			if ($this->resource == 0)
				{
				$this->exe = @mysql_query($this->syntax);
				if (!$this->result)
					{
					$this->throwError($this->exit);
					return FALSE;
					}
				}

			if ($this->result == TRUE || $this->exe == TRUE)
				{
				$this->queries++;
				if ($this->debugLevel == 2)
					{
					$end = $this->getTime();
					$this->syntaxes.= round($end - $start, 4) . ' sec. ' . $this->syntax . '<br />';
					}
				}

			if (isset($this->result)) return $this->result; //returning resource
			  else
			if (isset($this->exe)) return TRUE; //returning bool :)
			}
		}

	/*
	Fetch results from last query, you can choose mode
	1- MYSQL_BOTH
	2- MYSQL_ASSOC
	3- MYSQL_NUM
	*/
	public

	function fetch($result = NULL, $mode = NULL)
		{
		if ($this->result && $this->connection)
			{
			$this->mode = $mode;
			switch ($mode)
				{
			case 1:
				$this->mode = MYSQL_BOTH;
				break;

			case 2:
				$this->mode = MYSQL_ASSOC;
				break;

			case 3:
				$this->mode = MYSQL_NUM;
				break;

			default:
				$this->mode = MYSQL_BOTH;
				}

			if (isset($result)) //if you want to choose other than last result

			// but you have to make query like this: $q = $DB->query(...)

				{
				$this->result = $result;
				}

			$this->fetched = @mysql_fetch_array($this->result, $this->mode);
			if (is_array($this->fetched))
				{
				return $this->fetched;
				}
			  else
				{
				$this->throwError($this->exit);
				return FALSE;
				}
			}
		}

	/*
	Create new database with setted name and charset, throwing an error while not sufficient access
	*/
	public

	function createDB($name, $charset)
		{
		if ($this->connection)
			{
			$this->creator = $this->query('CREATE DATABASE ' . $name . ' DEFAULT CHARACTER SET ' . $charset . '');
			if ($this->creator)
				{
				return TRUE;
				}
			}
		}

	/*
	Lock tables (WRITE) table will be locked from WRITE access, READ access allowed
	*/
	public

	function LockTableWrite()
		{
		if ($this->connection)
			{
			foreach(func_get_args() as $tablename)
				{
				$this->lockedWrite = $this->query('LOCK TABLES ' . $tablename . ' READ', 0); //dont look at arg. READ it will lock WRITING in :)
				}

			if ($this->lockedWrite) return TRUE;
			}
		}

	/*
	Lock tables (READ) table will be locked from READ and WRITE access
	*/
	public

	function LockTableRead()
		{
		if ($this->connection)
			{
			foreach(func_get_args() as $tablename)
				{
				$this->lockedRead = $this->query('LOCK TABLES ' . $tablename . ' WRITE', 0);
				}

			if ($this->lockedRead) return TRUE;
			}
		}

	/*
	Unlock tables locked before
	*/
	public

	function Unlock()
		{
		if ($this->connection)
			{
			$this->unlock = $this->query('UNLOCK TABLES', 0);
			}

		if ($this->unlock)
			{
			return TRUE;
			}
		}

	/*
	Optimize database
	*/
	public

	function optimizeDB($output = NULL)
		{
		if ($this->connection)
			{
			$this->query('SHOW TABLES');
			while ($table = $this->fetch(NULL, 2))
				{
				foreach($table as $db)
					{
					$this->query('OPTIMIZE TABLE ' . $db . '', 0);
					if ($output == TRUE)
						{
						echo $db . ' - Optimized<br />';
						}
					}
				}
			}
		}

	/*
	Clear (truncate) table from records
	*/
	public

	function clearTable($table)
		{
		if ($this->connection)
			{
			$this->clear = $this->query('TRUNCATE TABLE ' . $table . '', 0);
			if ($this->clear)
				{
				return TRUE;
				}
			}
		}

	// FORCE disconnect from mysql. Killing connection.

	public

	function disconnect()
		{
		$this->disconnect = @mysql_close($this->connection);
		unset($this->connection);
		unset($this->db_host);
		unset($this->db_login);
		unset($this->db_password);
		unset($this->db_database);
		unset($this->db_port);
		if ($this->disconnect)
			{
			if ($this->disconnect && $this->debugLevel == 2)
				{
				echo 'Disconnected';
				return TRUE;
				}

			return TRUE;
			}
		  else
			{
			$this->throwError($this->exit);
			return FALSE;
			}
		}

	/*
	List mysql variables such as client encoding and version, host and protocol info
	*/
	public

	function dbVars()
		{
		if ($this->connection)
			{
			$this->vars['client_encoding'] = mysql_client_encoding($this->connection);
			$this->vars['server_version'] = mysql_client_encoding($this->connection);
			$this->vars['mysql_get_client_info'] = mysql_get_client_info();
			$this->vars['mysql_get_host_info'] = mysql_get_host_info($this->connection);
			$this->vars['mysql_get_proto_info'] = mysql_get_proto_info($this->connection);
			return array(
				$this->vars['client_encoding'],
				$this->vars['server_version'],
				$this->vars['mysql_get_client_info'],
				$this->vars['mysql_get_host_info'],
				$this->vars['mysql_get_proto_info']
			);
			}
		}

	/*
	Show mysql statistics like queries per second, long queries, uptime and so one
	*/
	public

	function dbStatistics()
		{
		if ($this->connection)
			{
			$this->statistics = mysql_stat($this->connection);
			return $this->statistics;
			}
		}

	/*
	Returns affected rows, results made by query.
	You can choose query, from which the number of results will be displayed
	*/
	public

	function numRows($res = NULL)
		{
		if ($this->connection)
			{
			if (isset($res))
				{
				$this->result = $res;
				$this->rows = @mysql_num_rows($this->result);
				}
			  else
				{
				if (isset($this->result)) $this->rows = @mysql_num_rows($this->result);
				}

			if ($this->rows) return (int)$this->rows;
			  else
				{
				$this->throwError($this->exit);
				return 0;
				}
			}
		}

	/*
	Show all queries syntaxes during script work
	*/
	public

	function showSyntaxes()
		{
		if ($this->queries > 0)
			{
			if ($this->debugLevel == 2)
				{
				return $this->syntaxes;
				}
			  else echo 'Debug mode must be ENABLED (2) to use this function';
			}

		// no queries

		}

	/*
	Counts time needed to finish query
	*/
	public

	function getTime()
		{
		static $a;
		if ($a == 0) $a = microtime(true);
		  else return (string)(microtime(true) - $a);
		}

	/*
	Show last errors
	*/
	public

	function showError()
		{
		if ($this->errors > 0)
			{
			return $this->error;
			}
		  else return 'No errors found';
		}

	/*
	Function to count number of errors
	*/
	public

	function countErrors()
		{
		return $this->errors;
		}

	/*
	Show actual debug level
	*/
	public

	function showDebugLevel()
		{
		return $this->debugLevel;
		}

	/*
	Close connections and unset all variables (automatically)
	*/
	public

	function __destruct()
		{
		if ($this->connection)
			{
			$this->disconnect();
			unset($this->connection);
			}

		// free memory

		if (isset($this->database)) unset($this->database);
		if (isset($this->fetched)) unset($this->fetched);
		if (isset($this->error)) unset($this->error);
		if (isset($this->db_host)) unset($this->db_host);
		if (isset($this->db_port)) unset($this->db_port);
		if (isset($this->db_user)) unset($this->db_user);
		if (isset($this->db_password)) unset($this->db_password);
		if (isset($this->database)) unset($this->db_database);
		}
	}