<?php
/*
SimpleDB - Mysql driver class
Version: 1.2.10
Author: Pawel 'Pavlus' Janisio
License: GPL v3
github: https://github.com/PJanisio/simpledb
*/
class DB_MySQL

  {
    private int $exit = 0;
    /*
    Exit while error.
    This variable when ednabled (1) terminates your program in case of ay error
    Default value is 0
    int
    */

    public $connection = NULL;
    protected $database = NULL;
    protected ?bool $error = NULL;
    protected $disconnect = NULL;
    private string $db_host = '';
    private int $db_port = 3306;
    private string $db_user = '';
    private string $db_password = '';
    private string $db_database = '';
    private $fetched;
    private array $vars;
    private int $rows = 0;
    private int $debugLevel = 0;
    public $result;
    public int $queries = 0;
    public string $backtrace;
    public int $errors = 0;
    public string $syntaxes = '';
    public string $syntax = '';
    public $resource;
    public ?bool $mode;

    public $exe = NULL;

    /*
    Look at the debug level, default is 1  error_reporting(E_ERROR | E_WARNING | E_PARSE) but if
    you have troubles with connection the best would be to enable 2 with full backtrace.
    */

    public function __construct(string $db_host, string $db_user, string $db_password, string $db_database, int $db_port = 3306, int $debug_level = 1)
      {

        // assign variables
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_database = $db_database;
        $this->debugLevel = $debug_level;

        switch ($this->debugLevel)
          {
        case 0:
            error_reporting(0);
        break;

        case 1:
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
        break;

        case 2:
            error_reporting(E_ALL);
        break;

        default:
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
          }

        // connection starts here
        $this->connection = mysqli_connect($this->db_host, $this->db_user,  $this->db_password, $this->db_database, $this->db_port = 3306, $debug_level = NULL);

        if (!$this->connection)
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
    public function throwError(int $exit)
      {
        if (!$this->connection)
          {
            echo $this->error = 'Can not connect to database: ' . mysqli_connect_error();
            $this->errors++;
            if ($this->exit == 1)
              {
                exit();
              }
          }

        if (mysqli_error($this->connection) != NULL)
          {
            $_SESSION['error_env'] = $_SERVER['SERVER_NAME'];
            $_SESSION['error_script'] = $_SERVER['PHP_SELF'];
            $_SESSION['error_sdb'] = __FILE__ . ':' . __LINE__;
            $_SESSION['error_user'] = $this->db_user;
            $_SESSION['error_time'] = date("j-m-Y, H:i:s");
            $_SESSION['error_num'] = mysqli_errno($this->connection);
            $_SESSION['error_syn'] = mysqli_error($this->connection);
            $_SESSION['output_backtrace'] = $this->parseBacktrace(debug_backtrace());
            echo $this->error = 'MySQL Error #' . mysqli_errno($this->connection) . ' Syntax: ' . mysqli_error($this->connection) . '<br />';
            $this->errors++;
            if ($this->exit == 1)
              {
                exit();
              }
          }
      }

    /*
    This function throws full backtrace if error occurs.
    Fixed displaying connection details like host, password...
    */
    public function parseBacktrace(array $raw)
      {
        $forbidden = array(
            1045,
            2003
        );
        foreach ($raw as $entry)
          {
            $this->backtrace .= "File: " . $entry['file'] . " (Line: " . $entry['line'] . ")<br />";
            $this->backtrace .= "Function: " . $entry['function'] . "<br />";
            if (!in_array($_SESSION['error_num'], $forbidden)) //why would we parse login or password data? :)
            
              {
                $this->backtrace .= "Args: " . implode(", ", $entry['args']) . "<br />";
              }
          }

        return $this->backtrace;
      }

    /*
    Make query to database
    Default - with result
    $resource = 0 - only execution, no results avaible
    */
    public function query(string $syntax, $resource = NULL)
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
                $this->result = @mysqli_query($this->connection, $this->syntax);
                if (!$this->result)
                  {
                    $this->throwError($this->exit);
                    return FALSE;
                  }
              }
            else if ($this->resource == 0)
              {
                $this->exe = @mysqli_query($this->connection, $this->syntax);
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
                    $this->syntaxes .= round($end - $start, 4) . ' sec. ' . $this->syntax . '<br />';
                  }
              }

            if (isset($this->result)) return $this->result; //returning resource
            else if (isset($this->exe)) return TRUE; //returning bool :)
            
          }
      }

    /*
    TODO: Get rid of this!
    Fetch results from last query, you can choose mode
    1- MYSQL_BOTH
    2- MYSQL_ASSOC
    3- MYSQL_NUM
    */
    public function fetch($result = NULL, $mode = NULL)
      {
        if ($this->result && $this->connection)
          {
            $this->mode = $mode;
            switch ($mode)
              {
            case 1:
                $this->mode = MYSQLI_BOTH;
            break;

            case 2:
                $this->mode = MYSQLI_ASSOC;
            break;

            case 3:
                $this->mode = MYSQLI_NUM;
            break;

            default:
                $this->mode = MYSQLI_BOTH;
              }

            if (isset($result)) //if you want to choose other than last result
            // but you have to make query like this: $q = $DB->query(...)
            
              {
                $this->result = $result;
              }

            $this->fetched = @mysqli_fetch_array($this->result, $this->mode);
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
    public function createDB(string $name, string $charset)
      {
        if ($this->connection)
          {
            $creator = $this->query('CREATE DATABASE ' . $name . ' DEFAULT CHARACTER SET ' . $charset . '');
            if ($creator)
              {
                return TRUE;
              }
          }
      }

    /*
    Lock tables (WRITE) table will be locked from WRITE access, READ access allowed
    */
    public function LockTableWrite()
      {
        if ($this->connection)
          {
            foreach (func_get_args() as $tablename)
              {
                $lockedWrite = $this->query('LOCK TABLES ' . $tablename . ' READ', 0); //dont look at arg. READ it will lock WRITING in :)
                
              }

            if ($lockedWrite) return TRUE;
          }
      }

    /*
    Lock tables (READ) table will be locked from READ and WRITE access
    */
    public function LockTableRead()
      {
        if ($this->connection)
          {
            foreach (func_get_args() as $tablename)
              {
                $lockedRead = $this->query('LOCK TABLES ' . $tablename . ' WRITE', 0);
              }

            if ($lockedRead) return TRUE;
          }
      }

    /*
    Unlock tables locked before
    */
    public function Unlock()
      {
        if ($this->connection)
          {
            $unlock = $this->query('UNLOCK TABLES', 0);
          }

        if ($unlock)
          {
            return TRUE;
          }
      }

    /*
    Optimize database
    */
    public function optimizeDB($output = NULL)
      {
        if ($this->connection)
          {
            $this->query('SHOW TABLES');
            while ($table = $this->fetch(NULL, 2))
              {
                foreach ($table as $db)
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
    public function clearTable(string $table)
      {
        if ($this->connection)
          {
            $clear = $this->query('TRUNCATE TABLE ' . $table . '', 0);
            if ($clear)
              {
                return TRUE;
              }
          }
      }

    // FORCE disconnect from mysql. Killing connection.
    public function disconnect()
      {
        $this->disconnect = @mysqli_close($this->connection);
        unset($this->connection);
        unset($this->db_host);
        unset($this->db_user);
        unset($this->db_password);
        unset($this->db_database);
        unset($this->db_port);
        if ($this->disconnect)
          {
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
    public function dbVars()
      {
        if ($this->connection)
          {
            /*
            this function has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.
            $this->vars['client_encoding'] = mysqli_client_encoding($this->connection);
            */
            $this->vars['server_version'] = mysqli_get_server_version($this->connection);
            $this->vars['mysqli_get_client_info'] = mysqli_get_client_info();
            $this->vars['mysqli_get_host_info'] = mysqli_get_host_info($this->connection);
            $this->vars['mysqli_get_proto_info'] = mysqli_get_proto_info($this->connection);
            return array(
                //$this->vars['client_encoding'],
                $this->vars['server_version'],
                $this->vars['mysqli_get_client_info'],
                $this->vars['mysqli_get_host_info'],
                $this->vars['mysqli_get_proto_info']
            );
          }
      }

    /*
    Show mysql statistics like queries per second, long queries, uptime and so one
    */
    public function dbStatistics()
      {
        if ($this->connection)
          {
            $statistics = mysqli_stat($this->connection);
            return $statistics;
          }
      }

    /*
    Show queries qtty
    */
    public function numQueries()
      {
        if ($this->connection)
          {
            return intval($this->queries);
          }
      }

    /*
    Returns affected rows, results made by query.
    You can choose query, from which the number of results will be displayed
    */
    public function numRows($res = NULL)
      {
        if ($this->connection)
          {
            if (isset($res))
              {
                $this->result = $res;
                $this->rows = @mysqli_num_rows($this->result);
              }
            else
              {
                if (isset($this->result)) $this->rows = @mysqli_num_rows($this->result);
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
    public function showSyntaxes()
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
    public  function getTime()
      {
        static $a;
        if ($a == 0) $a = microtime(TRUE);
        else return (string)(microtime(TRUE) - $a);
      }

    /*
    Show last errors
    */
    public function showError()
      {
        if ($this->errors > 0)
          {
            return $this->error;
          }
        else return FALSE;
      }

    /*
    Function to count number of errors
    */
    public function countErrors()
      {
        return $this->errors;
      }

    /*
    Show actual debug level
    */
    public function showDebugLevel()
      {
        return $this->debugLevel;
      }

  }

?>