<?php

/**
 * _pdoQuery class. Mark Leci 2015-2016
 * https://github.com/markl181/simple-pdo
 *
 */


namespace tools;

class simple_pdo
{

	//class options
	/**
     *
     */
    const TIMEZONE = 'America/Denver';
    //const TIMEZONE = 'Europe/London';
    /**
     *
     */
    const SHOWERRORS = true;
    /**
     *
     */
    const TIMEOUT = 30;
    /**
     *
     */
    const BOOTSTRAP = true;
    /**
     * @var bool
     */
    public $nullEmptyString = true;
    /**
     * @var bool
     */
    public $logErrorsToDatabase = false;
    /**
     * @var string
     */
    public $errorString = "An error has occurred and been logged.";
    /**
     * @var string
     */
    private $sqlLogError = "INSERT INTO log (query, error, record_id, ip_address) VALUES (?, ?, ?, ?)";
    /**
     * @var string
     */
    private $defaultFetchMode = 'assoc';//valid values ['assoc','numeric']
    /**
     * @var string
     */
    private $defaultFetchType = 'all'; //valid values ['all', 'one']

	//declare public variables
    /**
     * @var PDO
     */
    public $DBH;
    /**
     * @var
     */
    public $STH;
    /**
     * @var
     */
    public $result;
    /**
     * @var
     */
    public $rowCount;
    /**
     * @var
     */
    public $rowNum;

    public $idColumn;
    public $searchColumn;
    public $searchTable;
	public $searchValue;
	public $recordId;
	public $insertQuery;
	public $insertBinds = [];
	public $logFind = false;

	/**
	 * error_log_query function. Logs a query to a database table
	 * 
	 * @access public
	 * @param mixed $query
	 * @param mixed $exception (default: null)
	 * @param mixed $recordId (default: null)
	 * @return void
	 */
	public function error_log_query($query, $exception=null, $recordId=null)
	{

		$address = $_SERVER['REMOTE_ADDR'];
		$address = filter_var($address, FILTER_SANITIZE_STRING);

		$this->query($this->sqlLogError, ['binds'=>[$query, $exception, $recordId, $address], 'type'=>'insert']);

	}
	
	
	/**
	 * logging function. Produce a standard error, show an error message and/or log an error to a database
	 * 
	 * @access public
	 * @param mixed $error
	 * @param string $connect (default: 'no')
	 * @param mixed $query (default: null)
	 * @param mixed $recordId (default: null)
	 * @param mixed $customErr (default: null)
	 * @return void
	 */
	public function logging($error, $connect = 'no', $query = null, $recordId = null, $customErr = null)
	{
		if(SELF::BOOTSTRAP == true)
		{$size = Bootstrap4::$size;}
		$connect = strtolower($connect);

		if (SELF::SHOWERRORS == true)
		{
			//logging is enabled

			if ($connect == 'no')
				//log a non-connect error message
				{

				if ($customErr == null)
				{
					//log the application error message
					$errorString = "Error message: ".$error->getMessage()."<br/>";
					$errorString .= "Trace: ".$error->getTraceAsString()."<br/>";
					$errorString .= "Error code: ".$error->getCode()."<br/>";
					$errorString .= " Error in file/line: ".$error->getFile()." ".$error->getLine();
				}
				else
				{
					//show a custom error
					$errorString = $error->getMessage() . " ($customErr)";
				}
				if(SELF::BOOTSTRAP == true)
				{Bootstrap4::error_block($errorString);}
				else
				{echo "<br/>$errorString<br/>";}

			}
			else
				//log a connect error message and exit
				{
				if(SELF::BOOTSTRAP == true)	
				{Bootstrap4::error_block("Connection Error: $error");}
				else
				{echo "<br/>Connection Error: $error<br/>";}
				//exit;

			}

		}
		else
		{
			if(SELF::BOOTSTRAP == true)
			{Bootstrap4::error_block($this->errorString);}
			else
			{echo "<br/>".$this->errorString."<br/>";}
			
			
			if($this->logErrorsToDatabase == true)
			{
				$this->error_log_query($query, $error, $recordId);
				
			}
			
		}

	}


    /**
     * simple_pdo constructor.
     */
    function __construct($host, $dbname, $user, $pass)//construct ensures connection is automatically made
		{

		date_default_timezone_set(self::TIMEZONE);
		//create connection
		try {

			$this->DBH = new \PDO("mysql:host=$host;dbname=$dbname", $user, $pass,array(
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

			$this->DBH->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			$this->DBH->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_EMPTY_STRING);//useful attribute to convert '' to null
			$this->DBH->setAttribute(\PDO::ATTR_TIMEOUT, self::TIMEOUT);

		}


		catch(\PDOException $e)
		{
			$this->logging($e, 'yes');

		}


	}


	/**
	 * query function.
	 * 
	 * @access public
	 * @param mixed $query
	 * @param mixed $options (default: null). Accepts options including binds, mode, fetch, customErr and type
	 * @return void
	 */
	public function query($query, $options=null)
	{
		//set binds array
		if (isset($options['binds']))
			{$binds = $options['binds'];}
		else
			{$binds = null;}

		//set fetch mode
		if (isset($options['mode']))
			{$mode = strtolower($options['mode']);}
		else
			{$mode = $this->defaultFetchMode;}//default fetch mode
			
		//set fetch type
		if (isset($options['fetch']))
			{$fetch = strtolower($options['fetch']);}
		else
			{$fetch = $this->defaultFetchType;}//default fetch type
		
		//set custom error
		if (isset($options['customErr']))
			{$customErr = $options['customErr'];}
		else
			{$customErr = null;}
			
		//set query type	
		if (isset($options['type']))
			{$type = strtolower($options['type']);}
		else
			{$type = 'select';}


		try{
			$this->STH = $this->DBH->prepare($query);
			if ($mode == 'numeric')
			{
				$fetchMode = \PDO::FETCH_NUM;
			}
			else
			{
				$fetchMode = \PDO::FETCH_ASSOC;
			}

			if(is_array($binds))
			{
				//bind variables
				$i=1;
				foreach ($binds as $value)
				{
					if($this->nullEmptyString == true)
						{$value === '' ? null : $value;}

					if(is_int($value))
					{
						$value = intval($value);
						$this->STH->bindValue($i, $value, \PDO::PARAM_INT);
					}
					else{

						$this->STH->bindValue($i, $value);

					}


					$i++;
				}
			}
			//execute query
			$this->STH->execute();

			if
			($type == 'insert')
			{
				//record inserted record id
				$this->rowNum = $this->DBH->lastInsertId();
			}
			else if ($type == 'delete'||$type == 'update')
				{
					//no rownum or fetch

				}
			else
			{
				if ($fetch=='all')
				{
					//get all rows and count rows
					$this->result = $this->STH->fetchAll($fetchMode);
					$this->rowCount = $this->STH->rowCount();
				}
				else
				{
					//get first row
					$this->result = $this->STH->fetch($fetchMode);
				}
			}
		}


		catch(PDOException $e)
		{
			$this->logging($e, 'no', $query, null, $customErr);
			
		}

	}



 public function find_id()
 {

     //first check if a record already exists
    $sql = "SELECT $this->idColumn FROM $this->searchTable WHERE $this->searchColumn = ?";

    $this->query($sql, ['binds'=>[$this->searchValue],'fetch'=>'one']);

    if($this->result)
    {
        $this->recordId = $this->result[$this->idColumn];

    }
    else
    {
        //need to insert a record
        $this->query($this->insertQuery, ['binds'=>$this->insertBinds, 'type'=>'insert']);

        $this->recordId = $this->rowNum;
        if($this->logFind === true)
		{
			echo $this->searchTable." record created for ".$this->searchValue."<br/>";

		}

    }



 }






}


?>
