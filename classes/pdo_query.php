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
	public $requestString;

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

public function request($requestString)
{

		$recipeId = get_id($requestString, 'request_');

		//check if the request already exists and insert it if not
		$sqlGetRequestById = "SELECT id FROM avie_recipe_request WHERE active = 1 AND recipe_id = ?";
		$sqlInsertRequest = "INSERT INTO avie_recipe_request (recipe_id) VALUES (?)";

		$this->query($sqlGetRequestById, ['binds'=>[$recipeId],'fetch'=>'one']);

		if(!$this->result)
		{

			$sqlGetRecipeByPublicId = "SELECT id, updated_at, title FROM avie_recipe WHERE public_id = ? AND isdeleted = 0";

			$this->query($sqlInsertRequest, ['binds'=>[$recipeId],'type'=>'insert']);
			$this->query($sqlGetRecipeByPublicId, ['binds'=>[$recipeId], 'fetch'=>'one']);

			$recipeName = $this->result['title'];

			$requestMessage = "Request submitted for recipe $recipeName";

			Bootstrap4::error_block($requestMessage,'success');

			send_email("New Request from Avie's Recipe site",$requestMessage );

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

	/**
	 * @param        $name
	 * @param        $label
	 * @param        $value
	 * @param        $query
	 * @param int    $column
	 * @param int    $labelWidth
	 * @param int    $fieldSize
	 * @param string $comment
	 * @param string $autofocus
	 * @param string $required
	 */
	public function datalist($name, $label, $value, $query, $column=1, $labelWidth=4, $fieldSize=5
		, $comment='', $autofocus='', $required='')

	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;

		if ($autofocus<>'')
			$autofocus = ' autofocus';
		else
			$autofocus = '';

		if ($value<>'')
			$formValue = "value = '$value'";
		else
			$formValue = '';

		if ($required<>'')
			$required = ' required';
		else
			$required = '';

		$this->query($query);
		$result = $this->result;

		echo "<div class='form-group align-items-center'>";
		echo "<div class='row'>";
		echo "<label for='$name' class='col-$size-$labelWidth control-label'>$label </label>";
		echo "<datalist id='$name'>";
		echo "<select name='$name'>";
		foreach ($result as $row)
		{
			$optionValue = $row[$column];
			$optionId = $row['id'];
			if ($optionValue == $value)
			{
				//echo "<option selected='selected'>$optionValue</option>";
				echo "<option>$optionValue</option>";
			}
			else
			{
				echo "<option>$optionValue</option>";
			}
			//echo "<option>$optionValue</option>";

		}
		Bootstrap4::tag_close('select');
		Bootstrap4::tag_close('datalist');
		echo "<div class='col-$size-$fieldSize'>";
		echo "<input class='form-control' list='$name' id='$name' name='$name' type='text' $formValue $required $autofocus/>";
		Bootstrap4::tag_close('div');


		if ($comment<>'')
		{
			echo "<div class='col-$size-$fieldSize col-$size-offset-$labelWidth'>";
			echo "<p class='help-block'>$comment</p></div>";
		}

		//close form group
		Bootstrap4::tag_close('div',2);


	}






}


?>
