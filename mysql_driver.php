<?php

/*
SimpleDB - Mysql driver class
Version: 0.1.9
Author: Pawel 'Pavlus' Janisio
License: GPL v3
SVN: http://code.google.com/p/simplemysqlclass/source/browse/#svn/
*/




class DB_MySQL

	{
        
 //       
// Some of variables you will have to fill before using
 //

// Datbase host - most common: localhost - mysql server you want to connect
// string

private $db_host = 'localhost';

// Connection port - default: 3306
// int

private $db_port = 3306;

// Username which is allowed to connect to mysql server - default: root
// string

private $db_user = 'root';

// User password, in clean installation of mysql user password is empty
// string
 
private $db_password = '';

// Database you want to connect
// string

private $db_database = 'mysql';

/*
Debug mode = 0 silent mode, no errors reporting!
Debug mode = 1 normal work, error reporting like in php.ini + errors from class[DEFUALT]
Debug mode = 2 all php errors and warnings will be displayed + query time and syntaxes 
int
*/
	
private $debug = 1;

// Do not change variables below, there is no need to do it


protected $connection = NULL;
protected $database = NULL;
protected $error = NULL;
protected $disconnect = NULL; 
protected $syntaxes = NULL;
private $fetched = array();
private $lockedRead = NULL;
private $lockedWrite = NULL;
private $rows = 0;
private $result = NULL;
private $vars = NULL;
public $statistics = NULL;
public $queries = 0;
public $exe = NULL;

	public function __construct ()
			{
                
                 switch ($this->debug)
                            {
                              case 0:
                              $this->debug = error_reporting(0);
				$this->debugLevel = 0;
                              break;
                              
                              case 1:
                              $this->debug = error_reporting(E_ALL ^ E_NOTICE);
				$this->debugLevel = 1;
                              break;
                              
                              case 2:
                              $this->debug = error_reporting(E_ALL);
				$this->debugLevel = 2;
                              break;
                              
                                default:
                                $this->debug = error_reporting(E_ALL ^ E_NOTICE);
					$this->debugLevel = 1;   
                                
                            }
			
		$this->connection = @mysql_connect($this->db_host.':'.$this->db_port, $this->db_user, $this->db_password);
                
                    if($this->connection)
                    {                              
			$this->database = @mysql_select_db ($this->db_database);
                    }

				if(!$this->connection)
					{
					$this->throwError();
						return FALSE;
					} 
                            			else 
                            				return TRUE;
                            
					if(!$this->database)
						{
							$this->throwError(); 
							return FALSE;
						}
							else
								return TRUE;

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
				$this->throwError();
                            	return FALSE;
				}

			}
				else if($this->resource == 0)
				{
				$this->exe = @mysql_query($this->syntax);
				

				if(!$this->result)
				{
				$this->throwError();
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
						return $this->result;

							else if(isset($this->exe))
						return TRUE;


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
					$this->throwError();
                            		return FALSE;
					}

					}
		}

      /*
      Function throwing error syntax
      */
	public function throwError() 
		{
		if(mysql_error() != NULL)
			echo $this->error ='MySQL Error #'.mysql_errno().' Syntax: '.mysql_error();

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
                                    	return TRUE;
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
	public function forceDisconnect()
		{
			$this->disconnect = @mysql_close($this->connection);

					unset($this->connection);
					unset($this->db_host);
					unset($this->db_login);
					unset($this->db_password);
					unset($this->db_database);
					unset($this->db_port);

				if($this->disconnect)
				return TRUE;
					else 
					{
				$this->throwError();
                            	return FALSE;
					}



		}

	/*
	Connects again to mysql, usable when you want to change user or database
	*/

	public function reconnect($host, $port, $user, $password, $database)

		{

			$this->db_host = $host;
			$this->db_port = $port;
			$this->db_user = $user;
			$this->db_password = $password;
			$this->db_database = $database;

				$this->connection = @mysql_connect($this->db_host.':'.$this->db_port, $this->db_user, $this->db_password);
                
                    if($this->connection)
                    {                              
			$this->database = @mysql_select_db ($this->db_database);
                    }

				if(!$this->connection)
					{
					$this->throwError();
						return FALSE;
					} 
                            			else 
                            				return TRUE;
                            
					if(!$this->database)
						{
							$this->throwError(); 
							return FALSE;
						}
							else
								return TRUE;


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
				$this->throwError();
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
        if(!empty($this->error))
            {
        echo 'Last SimpleDB error:</br>';
    
            return $this->error;
            }
            else
                return 'No errors found';

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
					$this->disconnect = @mysql_close($this->connection);
                                                 }
					if(!$this->disconnect)
						$this->throwError();
					                       

					unset($this->connection);
					unset($this->database);
					unset($this->fetched);
                    			unset($this->error);
                    			unset($this->debug);
                   			unset($this->statistics);
                    			unset($this->vars);
					unset($this->db_host);
					unset($this->db_port);
					unset($this->db_user);
					unset($this->db_password);
					unset($this->db_database);
					unset($this->lockedRead);
					unset($this->lockedWrite);

					//error_reporting(E_ALL ^ E_NOTICE);
				}
			
	}




	
