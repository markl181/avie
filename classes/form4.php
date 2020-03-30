<?php
/**
 * Mark Leci - 2015
 * form class.
 * Create a form that interacts with the bootstrap css framework
 */

namespace tools;


use bootstrap;

/**
 * Class form4
 *
 * @package tools
 */
class form4
{

    /**
     * @var
     */
    public $charFail;
    /**
     * @var
     */
    public $numFail;
    /**
     * @var
     */
    public $boolFail;
    /**
     * @var
     */
    public $pwFail;

    private $attributesArray=[];
    private $class;

    /**
	 * open function.
	 *
	 * @access public
	 * @param string $method (default: 'post')
	 * @param string $class (default: '')
	 * @param string $action (default: '')
	 * @return void
	 */
	function open($method='post', $class='', $action='')
	{
		if ($action=='')
			{echo "<form class='$class' method='$method'>";}
		else
			{echo "<form class='$class' method='$method' action='$action'>";}

	}


	/**
	 * gutter function.
	 * adapt bootstrap columns by adding an extra div automatically if necessary
	 *
	 * @access protected
	 * @param mixed $labelWidth
	 * @param mixed $fieldSize
	 * @return void
	 */
	protected function gutter($labelWidth, $fieldSize)
	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;

		$gutter = 12-$labelWidth-$fieldSize;

		//add an extra gutter div if needed
		if ($gutter >= 1)
		{
			echo "<div class='col-$size-$gutter'></div>";

		}

	}

	private function attributes()
    {

        foreach ($this->attributesArray as $attr=>$val)
        {
            if ($attr == 'class')
            {
                $this->class .= " $val";

            }
            else
            {

                //$val = addcslashes($val);
                echo " $attr='".$val."'";
            }
        }


    }

	/**
	 * timestamp function.
	 * displays last record update date/time
	 *
	 * @access public
	 * @param mixed $timestamp
	 * @param mixed $userstamp
	 * @return void
	 */
	public function timestamp($timestamp, $userstamp)
	{
		//get global defaults from bootstrap
		$size = Bootstrap4::$size;
		$fieldSize = Bootstrap4::$defaultFieldSize;
		$offset = Bootstrap4::$defaultOffset;

		Bootstrap4::msg_block("$timestamp by $userstamp", 'info', 'Last update: ');

	}



	/**
	 * checkbox function. Generate an html bootstrap checkbox
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $label
	 * @param int $checked (default: 0)
	 * @param string $closeGroup (default: 'y')
	 * @return void
	 */
	public function checkbox($name, $label, $checked=0)
	{

		echo "<div class='form-check'>";



		if ($checked == 0)
		{

			echo "<input class='form-check-input' id='$name' name='$name' type='checkbox' value='1'/>";
		}
		else
		{

			echo "<input class='form-check-input' id='$name' name='$name' type='checkbox' checked='checked' value='1'/>";
		}

        echo "<label for='$name' class='form-check-label'>";
		echo " $label";
		Bootstrap4::tag_close('label');
		Bootstrap4::tag_close('div');


	}


	/**
	 * static_element function. Add a non-fillable element to a form
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $label
	 * @param mixed $value
	 * @param int $labelWidth (default: 4)
	 * @param int $fieldSize (default: 1)
	 * @return void
	 */
	public function static_element($name, $label, $value, $labelWidth=4, $fieldSize=5)
	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;

		echo "<div class='form-group'>";
		echo "<label for='$name' class='col-$size-$labelWidth control-label'>$label </label>";

		echo "<div class='col-$size-$fieldSize'>";
		echo "<p id='$name' class='form-control-static'>$value</p>";
		Bootstrap4::tag_close('div');

		//add gutter div if needed
		$this->gutter($labelWidth, $fieldSize);

		//close form group
		Bootstrap4::tag_close('div');


	}


	/**
	 * text function. Can be used to create any input field except select or datalist (including radio)
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $label
	 * @param mixed $attributesArray
	 * @param int $labelWidth (default: 4)
	 * @param int $fieldSize (default: 5)
	 * @return void
	 */
	public function input($name, $label, $attributesArray, $labelWidth=4, $fieldSize=5)
	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;
		$this->class = 'form-control text-center';
		$this->attributesArray = $attributesArray;

		echo "<div class='form-group align-items-center'>";

		//print label
        echo "<div class='row'>";
		echo "<label class='col-sm-$labelWidth control-label' for='$name'>";

		echo " $label </label>";
		//print field
		echo "<div class='col-sm-$fieldSize text-center'>";
		echo "<input ";

        $this->attributes();

		echo " class = '$this->class'";
		echo " id='$name' name='$name'/>";
		Bootstrap4::tag_close('div');
        Bootstrap4::tag_close('div');

		//close form group
		Bootstrap4::tag_close('div');
	}


    /**
     * @param        $name
     * @param        $label
     * @param string $required
     * @param string $value
     * @param int    $rows
     * @param int    $labelWidth
     * @param int    $fieldSize
     */
    public function textarea($name, $label, $required='', $value='', $rows=3, $labelWidth=4, $fieldSize=5)
	{

		//get global size from bootstrap
		$size = Bootstrap4::$size;

		if ($required<>'')
			{$required = ' required';}
		else
			{$required = '';}

		echo "<div class='form-group'>";
		//print label
		echo "<label class='col-$size-$labelWidth control-label' for='$name'>$label </label>";
		echo "<div class='col-$size-$fieldSize'>";
		echo "<textarea class='form-control' id='$name' name='$name' rows='$rows' $required>$value</textarea>";
		Bootstrap4::tag_close('div');

		//add gutter div if needed
		$this->gutter($labelWidth, $fieldSize);

		//close form group
		Bootstrap4::tag_close('div');

	}


	/**
	 * select function.
	 *
	 * @access public
	 * @param string $name
	 * @param string $label
	 * @param array $values
	 * @param int $labelWidth (default: 4)
	 * @param int $fieldSize (default: 5)
	 * @param string $script (default: '')
	 * @param string $closeGroup (default: 'y')
	 * @param string $selected (default: '')
	 * @return void
	 */
	public function select($name, $label, $values, $selected='')
	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;
		$labelWidth = 4;
		$fieldSize = 5;

		echo "<div class='form-group align-items-center'>";
		echo "<div class='row'>";
        echo "<label class='control-label col-sm-$labelWidth' for='$name'>";


            echo " $label </label>";

echo "<select class='form-control col-sm-$fieldSize' id='$name' name='$name'>";

		if ($selected=='')
			{echo "<option label='' value='' selected></option>";}
		else
        {
            echo "<option label='' value=''></option>";
        }

		foreach ($values as $value=>$text)
		{
			if ($value == $selected && $selected <> '')
				{echo "<option selected='selected' value='$value'>$text</option>";}
			else
				{echo "<option value='$value'>$text</option>";}

		}
		Bootstrap4::tag_close('select');

		Bootstrap4::tag_close('div');

		Bootstrap4::tag_close('div');


	}


    /**
     * select function.
     *
     * @access public
     * @param mixed  $name
     * @param mixed  $label
     * @param mixed  $values
     * @param string $queryField (default: '')
     * @param string $idField
     * @param int    $labelWidth (default: 4)
     * @param int    $fieldSize  (default: 5)
     * @param string $script     (default: '')
     * @param string $closeGroup (default: 'y')
     * @param string $selected   (default: '')
     * @param string $tooltip    (default: '')
     * @return void
     */
    public function selectquery($name, $label, $values, $queryField = '', $idField = '', $selected = ''
        , $attributesArray = '')
    {
        $labelWidth = 4;
        $fieldSize = 5;
        $this->class = 'form-control';

        $valuesArray = [];


        echo "<div class='form-group align-items-center'>";
        echo "<div class='row'>";
        echo "<br/>";
        echo "<label for='$name' class='col-sm-$labelWidth control-label'>";

        echo " $label </label>";
        echo "<div class='col-sm-$fieldSize'>";
        echo "<select ";

        if($attributesArray <> '')
        {
            $this->attributesArray = $attributesArray;
            $this->attributes();

        }

        echo " class = '$this->class'";
        echo " id='$name' name='$name'>";


        if($selected == '') {
            echo "<option label='' value='' selected></option>";
        }

                if($queryField != '') {
            //key will be field name

            foreach($values as $item) {
                //reformat array into usable format
                $valuesArray[$item[$idField]] = $item[$queryField];


            }

        } else {

            $valuesArray = $values;

        }



        foreach($valuesArray as $value => $text) {
            if($value == $selected) {
                echo "<option selected='selected' value='$value'>$text</option>";

            } else {
                echo "<option value='$value'>$text</option>";
            }

        }
        Bootstrap4::tag_close('select');
        Bootstrap4::tag_close('div');
        Bootstrap4::tag_close('div');

        //add gutter div if needed
        $this->gutter($labelWidth, $fieldSize);

        //close form group
        Bootstrap4::tag_close('div');


    }



    /**
     * @param     $name
     * @param     $label
     * @param     $preset
     * @param     $values
     * @param int $labelWidth
     * @param int $fieldSize
     */
    public function datalist_text($name, $label, $preset, $values, $labelWidth=4, $fieldSize=5)
	{
		//get global size from bootstrap

		echo "<div class='form-group align-items-center'>";
		echo "<div class='row'>";
		echo "<label for='$name' class='col-sm-$labelWidth control-label'>$label </label>";
		echo "<datalist id='$name'>";
		echo "<select name='$name'>";
		foreach ($values as $optionValue)
		{
		
				echo "<option>$optionValue</option>";
			
		}
		Bootstrap4::tag_close('select');
		Bootstrap4::tag_close('datalist');
		//echo "<div class='col-sm-$fieldSize'>";
		echo "<input class='form-control col-sm-$fieldSize' value='$preset' list='$name' id='$name' name='$name' type='text'/>";
		//Bootstrap4::tag_close('div');
        Bootstrap4::tag_close('div');
		//close form group
		Bootstrap4::tag_close('div');


	}


	/**
	 * hidden function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $value
	 * @return void
	 */
	public function hidden($name, $value)
	{

		echo "<input type='hidden' name='$name' value='$value'/>";

	}


	/**
	 * submit function.
	 *
	 * @access public
	 * @param string $name (default: '')
	 * @param string $value (default: 'submit')
	 * @param int $offset (default: 4)
	 * @param string $delete (default: 'N')
	 * @return void
	 */
	public function submit($name='submit', $value='Submit', $action = '', $offset=4, $delete='N')
	{
		//get global size from bootstrap
		$size = Bootstrap4::$size;
		$fieldSize = 4;
		$labelWidth = 0;

		if($action != '')
        {
            $action = "action='$action'";

        }

		echo "<div class='col-$size'>";

		if ($delete == 'N')
		{

			echo "<input type='submit' class='btn btn-primary' value='$value' name='$name' $action />";
		}
		else
		{
			echo "<div class='btn-group'>";
			echo "<button type='submit' class='btn btn-primary btn-lg' name='$name' $action>$value</button> ";
			echo "<button type='submit' class='btn btn-primary btn-lg' name='delete'>Delete</button>";
			Bootstrap4::tag_close('div');
		}
		Bootstrap4::tag_close('div');
		$this->gutter($labelWidth, $fieldSize);

	}


	/**
	 * validate function.
	 * validates a string is not too long and does not contain banned characters
	 *
	 * @access public
	 * @param mixed $string
	 * @param mixed $fieldName
	 * @param string $strlen (default: '')
	 * @return void
	 */
	public function validate($string, $fieldName, $strlen='')
	{
		$barredCharacters = ['*'];
		//, '%'];
		$size = Bootstrap4::$size;
		$errString = [];
		$this->charFail = false;

		if
		($strlen<>'')
		{
			if
			(strlen($string)>$strlen)
			{
				Bootstrap4::error_block("The maximum length for $fieldName is $strlen");
			}

		}

		foreach ($barredCharacters as $charTest)
		{
			if (strpos($string, $charTest)!==FALSE)
			{

				$errString[] = $charTest;
				$this->charFail = true;
			}


		}

		foreach ($errString as &$errorInstance)
		{
			//add single quotes
			$errorInstance = "'".$errorInstance."'";

		}

		//piece together array
		$errorList = implode(',', $errString);

		if (count($errString)>1)
		{

			$errorString = $errorList." are not allowed in the $fieldName field";
			Bootstrap4::error_block($errorString);
		}
		else if (count($errString)==1)
			{
				$errorString = $errorList. " is not allowed in the $fieldName field";
				Bootstrap4::error_block($errorString);
			}


	}


	/**
	 * validate_boolean function. Validates a boolean form value and adjusts it to Y/N
	 *
	 * @access public
	 * @param mixed $string
	 * @param mixed $fieldName
	 * @return void
	 */
	public function validate_boolean($string, $fieldName)
	{
		$characterList = ['y', 'yes', 'n', 'no'];

		foreach
		($characterList as $charTest)
		{
			if ($charTest == strtolower($string))
			{
				$this->boolFail = false;
				return strtoupper(substr($string, 0, 1));

			}


		}
		//if none of them match, return an error
		$this->boolFail = true;
		Bootstrap4::error_block("Please enter Y/N/Yes/No only");
	}


	/**
	 * validate_number function.
	 *
	 * @access public
	 * @param mixed $number
	 * @param mixed $fieldName
	 * @param mixed $max
	 * @param mixed $min
	 * @return void
	 */
	public function validate_number($number, $fieldName, $min, $max)
	{
		//get global size
		$size = Bootstrap4::$size;

		if ($number < $min)
		{
			Bootstrap4::error_block("$fieldName must have a minimum value of $min");
			$this->numFail = true;
		}
		else if ($number > $max)
			{
				Bootstrap4::error_block("$fieldName must have a maximum value of $max");
				$this->numFail = true;
			}
		else
		{
			$this->numFail = false;

		}

	}
	

	/**
	 * close function.
	 *
	 * @access public
	 * @return void
	 */
	public function close()
	{
		Bootstrap4::tag_close('form');

	}


}


?>