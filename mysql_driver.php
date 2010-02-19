<?php

/*
SimpleDB - Mysql driver class
Version: 0.2.1
Author: Pawel 'Pavlus' Janisio
License: GPL v3
SVN: http://code.google.com/p/simplemysqlclass/source/browse/#svn/
*/

/*
TODO:
# $this->exit, dont touch it in class file
# optimizing fnction rewrite
# check samples

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
protected $syntaxes = NULL;
private $db_host = '';
private $db_port = 0;
private $db_user = '';
private $db_password = '';
private $db_database = '';
private $fetched = array();
private $lockedRead = NULL;
private $lockedWrite = NULL;
private $rows = 0;
private $result = NULL;
private $vars = NULL;
public $statistics = NULL;
public $queries = 0;
public $errors = 0;
public $debugLevel = 0;
public $exe = NULL;
public $backtrace = NULL;

	public function __construct ($db_host, $db_port, $db_user, $db_password, $db_database, $debug_level = NULL)
			{
				//assign variables
				$this->db_host = $db_host;
				$this->db_port = $db_port;
				$this->db_user = $db_user;
				$this->db_password = $db_password;
				$this->db_database = $db_database;
				
				if(isset($debug_level))
				$this->debugLevel = $debug_level;
					else
						$this->debugLevel = 1;
                
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
			
			//connection starts here
		$this->connection = @mysql_connect($this->db_host.':'.$this->db_port, $this->db_user, $this->db_password);
                
                    if($this->connection)
                    {                              
			$this->database = @mysql_select_db ($this->db_database);
                    }

				if(!$this->connection)
					{
					$this->throwError($this->exit);
						return FALSE;
					} 
                            			else 
                            				return TRUE;
                            
					if(!$this->database)
						{
							$this->throwError($this->exit); 
							return FALSE;
						}
							else
								return TRUE;

			}


      /*
	Function throwing error.
	If you want detailed information, do not forger to add session_start(); at the begining of
	your file.

	Also if you want to terminate your script, change $exit value to (1) at the top of the class
      */
	 
	public function throwError($exit) 
		{
			
		if(mysql_error() != NULL)
		{
			
			
			$_SESSION['error_env'] = $_SERVER['SERVER_NAME'];
			$_SESSION['error_script'] =  $_SERVER['PHP_SELF']; 
			$_SESSION['error_sdb'] = __FILE__.':'.__LINE__;
			$_SESSION['error_user'] = $this->db_user; 
			$_SESSION['error_time'] = date("j-m-Y, H:i:s");
			$_SESSION['error_num'] = mysql_errno();
			$_SESSION['error_syn'] = mysql_error();
			$_SESSION['output_backtrace'] = $this->parseBacktrace(debug_backtrace());

			echo $this->error ='MySQL Error #'.mysql_errno().' Syntax: '.mysql_error().'<br>';
				$this->errors++;

				if($this->exit == 1)
					{
					echo 'Application terminated';
					exit();
					}
		}


		}
	/*
	This function throws full backtrace if error occurs.
	*/
		
	public function parseBacktrace($raw)
		{

        
        foreach($raw as $entry){ 
                $this->backtrace.="File: ".$entry['file']." (Line: ".$entry['line'].")<br>"; 
                $this->backtrace.="Function: ".$entry['function']."<br>"; 
                $this->backtrace.="Args: ".implode(", ", $entry['args'])."<br>"; 
        } 

        return $this->backtrace; 
		
		
		
		}

	/*
	Make query to database
	*/            

	public function query($syntax, $resource = NULL)
		
		{
			if($this->connection)
			{
			$this->syntax = $syntax;
			if(!isset($resource))
			{
			$this->resource = 1;
			}
				else
				{
				$this->resource = $resource;
				}

			if($this->debugLevel == 2)
				{
                        	$start = $this->getTime();
				}
			
			if($this->resource == 1)
			{
				$this->result = @mysql_query($this->syntax);
				
				
				if(!$this->result)
				{
				$this->throwError($this->exit);
                            	return FALSE;
				}

			}
				else if($this->resource == 0)
				{
				$this->exe = @mysql_query($this->syntax);
				

				if(!$this->result)
				{
				$this->throwError($this->exit);
                            	return FALSE;
				}
				
				}
				if($this->result == TRUE || $this->exe == TRUE)
				{ 
					$this->queries++;
					if($this->debugLevel == 2)
							{
                             				$end = $this->getTime();
							$this->syntaxes .= round($end-$start, 4).' sec. '.$this->syntax.'<br>';
							}
				}


					if(isset($this->result))
						return $this->result; //returning resource

							else if(isset($this->exe))
						return TRUE; //returning bool :)


		}
		}

	

        /*
        Fetch results from last query, you can choose mode
        1- MYSQL_BOTH
        2- MYSQL_ASSOC
        3- MYSQL_NUM
        */
	public function fetch($result = NULL, $mode = NULL)
		{
				if($this->result && $this->connection)
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
				
				if(isset($result)) //if you want to choose other than last result
							//but you have to make query like this: $q = $DB->query(...)
					{
						$this->result = $result;

						}

			$this->fetched = @mysql_fetch_array($this->result, $this->mode);

				if(is_array($this->fetched))
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
      Create new database with default charset
      */
	public function createDB($name, $charset)
		{
			if($this->connection)
				{
				$this->creator = $this->query('CREATE DATABASE '.$name.' DEFAULT CHARACTER SET '.$charset.'');

			if($this->creator)
				{
				echo 'Database '.$name.' has been created';
				}


		        	}
        
        	}
            
    /*
    Lock tables (READ) table will be locked from WRITE acces, READ access allowed
    */   

    public function LockTableRead() 
        {
            if($this->connection)
            {
            foreach (func_get_args() as $tablename) 
            {
               $this->lockedRead = $this->query('LOCK TABLES '.$tablename.' READ',0);

                    }
			if($this->lockedRead)
                     return TRUE;
			   
            }
        }
        
     /*
    Lock tables (WRITE) table will be locked from READ and WRITE access
    */   

    public function LockTableWrite() 
        {
            if($this->connection)
            {
            foreach (func_get_args() as $tablename) 
            {
                $this->lockedWrite = $this->query('LOCK TABLES '.$tablename.' WRITE',0);
               
                    }
			if($this->lockedWrite)
                     return TRUE;   
            }
        }
        
    /*
    Unlock tables locked before
    */   

    public function Unlock() 
        {
            if($this->connection)
            {
            $this->unlock = $this->query('UNLOCK TABLES',0);
            }
                if($this->unlock)
                    {
                     return TRUE;   
                    }
        }

           
            
    /*
    Optimize database
    */
    
    public function optimizeDB()
    
        {
            if($this->connection)
            {
          $this->query('SHOW TABLES'); 

          		while($table = $this->fetch(NULL,2))
         			 {
				 
          			foreach ($table as $db)
            			{ 
		
       		$this->query('OPTIMIZE TABLE '.$db.'', 0);
        			echo $db.' - Optimized<br>'; 
            
            			} 
    
          			}
            }
            
        }

            
    /*
    Clear (truncate) table from records
    */
    public function clearTable($table)
        {
            if($this->connection)
                {
                 $this->clear = $this->query('TRUNCATE TABLE '.$table.'',0);
                    if($this->clear)
                        {
                            echo 'Table '.$table.' has been cleared';
                        }
                	return TRUE;
                }   

        }

	
	/*
	Import dump using exec function, u have to be logged to mysql admin user
	*/
	public function importDumpexec($location)
			{
				if($this->connection)
					{
						if(file_exists($location) && function_exists('exec'))
						{
							//check if superuser!

						$cmd = '/usr/bin/mysql -h '.$this->db_host.' -u '.$this->db_user.' -p'.$this->db_password.' '.$this->db_database.' < '.$location;
						$this->dump = exec($cmd, $result);
	
						if($this->dump == 0)
							{
								echo 'Import resulted an error...';
								return FALSE;
							}

						}
						else 
							{
							echo 'Import success!';
							return TRUE;

							}

					}

			}

	/*
	Force disconnect from mysql, use only with reconnect function or let class close connection by itself
	*/
	public function disconnect()
		{
			$this->disconnect = @mysql_close($this->connection);

					unset($this->connection);
					unset($this->db_host);
					unset($this->db_login);
					unset($this->db_password);
					unset($this->db_database);
					unset($this->db_port);

				if($this->disconnect)
					{
						if($this->disconnect && $this->debugLevel == 2)
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
      List mysql variables such as client encoding and version
      */  

    public function dbVars()
        {
           if($this->connection)
                {
                  $this->vars .= 'Client Encoding: '.mysql_client_encoding($this->connection).'</br>';
                        $this->vars .= 'Server Version: '.mysql_get_server_info().'</br>';

                    		return $this->vars;
                } 

        }
      /*
      Show mysql statistics like queries per second, long queries, uptime and so one
      */ 
 
    public function dbStatistics()
        {
         if($this->connection)
            {
            $this->statistics = mysql_stat($this->connection);
                return $this->statistics;

            }   
 
        }

	/*
	Returns number of rows executed by query
	*/

	public function numRows($res = NULL)
		{
			if($this->connection)
			{
				if(isset($res))
				{
				$this->result = $res;
				$this->rows = @mysql_num_rows($this->result);
				}
					else
						{
							if(isset($this->result))
							$this->rows = @mysql_num_rows($this->result);

						}

				if($this->rows)
				return $this->rows;
				else
				{
				$this->throwError($this->exit);
                            	return FALSE;
				}
			}
		}

	/*
	Show all queries syntaxes during script work
	*/

    public function showSyntaxes()
		{
			if($this->queries > 0)
			{
                if($this->debugLevel == 2)
                {
                
				return $this->syntaxes;
			
			}
				else
				echo 'Debug mode must be ENABLED (2) to use this function';
        }
	//no queries

		}
        
     /*
     Counts time needed to finish query
     */   
        
    public function getTime()
        {
            
         static $a;
    if($a == 0) $a = microtime(true);
    else return (string)(microtime(true)-$a);  
            
            
        }

     /*
      Show last errors
      */
   
	public function showError()
    
   	 {
        if($this->errors > 0)
            {
        echo 'Last SimpleDB error:</br>';
    	return $this->error;
            }
            else
                return 'No errors found';

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
	return 'Debug level is now: '.$this->debugLevel.'';

	}


    /*
      Close connections and unset all variables
      */

	public function __destruct() 
				{
                    
                            		if($this->connection)
                                                 {
											$this->disconnect();
                                                 }
					                       
						//free memory
					unset($this->connection);
					unset($this->database);
					unset($this->fetched);
                    unset($this->error);
					unset($this->db_host);
					unset($this->db_port);
					unset($this->db_user);
					unset($this->db_password);
					unset($this->db_database);

					

				}
			
	}




	
