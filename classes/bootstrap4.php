<?php
/**
 * bootstrap master class. Mark Leci 2015
 * Copy to all new projects
 */


namespace tools;

class Bootstrap4
{

    //declare css defaults
    /**
     * @var string
     */
    public static $size = 'sm';
    /**
     * @var int
     */
    public static $defaultOffset = 3;
    /**
     * @var int
     */
    public static $defaultFieldSize = 6;
    public static $recipeCount;
    private static $attributesOutput;
    private static $classOutput;

    //allow class options in constructor (research that)

    /**
     * __construct function.
     *
     * @access public
     * @param string $centered (default: 'y')
     * @return void
     */
    function __construct($centered = 'y')
    {
        if($centered == 'y') {
            $centered = ' text-center';
        }
        else {
            $centered = '';
        }

        echo "<div class='main container-fluid $centered'>";


    }


    /**
     * @param        $content
     * @param int    $level
     * @param string $class
     */
    public static function heading($content, $level = 1, $class = 'text-center')
    {
        //  echo "<div class='row'>";
        echo "<h$level class='$class'>";
        echo $content;
        echo "</h$level>";
        //echo "</div>";

    }


    /**
     * @param        $content
     * @param int    $level
     * @param string $class
     * @return string
     */
    public static function heading_hidden($content, $level = 1, $class = 'text-center')
    {
        $return = "<h$level class='$class'>";
        $return .= $content;
        $return .= "</h$level>";

        return $return;

    }

    /**
     * clearfix function.
     *
     * @access public
     * @static
     * @param string $size (default: 'none')
     * @return void
     */
    public static function clearfix($size = 'none')
    {

        if($size == 'none') {

            echo "<div class='clearfix'></div>";

        }
        else {
            $class = "visible-$size-block";

            echo "<div class='clearfix $class'></div>";
        }

    }


    /**
     * @param        $span
     * @param string $content
     * @param array  $attributesArray
     */
    public static function div_col($span, $content = '', $attributesArray = array())
    {

        echo "<div ";
        echo "class='col-" . self::$size . "-$span' ";

        //loop through attributes
        foreach($attributesArray as $attr => $val) {
            echo " $attr='$val'";


        }

        if
        ($content == '') {
            echo ">";
        }
        else {
            echo ">$content</div>";
        }

    }

    /**
     * color_span function. Generate a span given a colour
     *
     * @access public
     * @static
     * @param mixed $data
     * @param mixed $color
     * @return string
     */
    public static function color_span($data, $color)
    {

        return "<span style='background: $color'>$data</span>";


    }

    /**
     * linebreak function.
     *
     * @access public
     * @param int $count (default: 1)
     * @return void
     */
    public static function linebreak($count = 1)
    {
        for
        ($i = 1; $i <= $count; $i++) {
            echo "<br/>";

        }


    }


    /**
     * modal function.
     *
     * @access public
     * @param mixed $title
     * @param mixed $body
     * @param mixed $footer
     * @param mixed $id
     * @param mixed $action
     * @return void
     */
    public function modal($title, $body, $footer, $id, $action = '')
    {
        //create popup div
        self::tag_open('div', ['class' => 'modal fade', 'id' => $id, 'role' => 'dialog']);
        self::tag_open('div', ['class' => 'modal-dialog']);
        //modal content
        self::tag_open('div', ['class' => 'modal-content']);
        self::tag_open('div', ['class' => 'modal-header']);
        self::tag_open('button', ['class' => 'close', 'type' => 'button', 'data-dismiss' => 'modal'], '&times;', true);
        //modal heading
        self::tag_open('h4', ['class' => 'modal-title'], $title, true);
        self::tag_close('div');
        self::tag_open('div', ['class' => 'modal-body']);
        //modal body text
        self::tag_open('p', '', $body, true);
        self::tag_close('div');
        self::tag_open('div', ['class' => 'modal-footer']);

        $form = new form;
        $form->open('post', 'form form-inline', $action);
        echo $footer;

        self::tag_open('button', ['class' => 'btn btn-default', 'type' => 'button', 'data-dismiss' => 'modal'], 'Close', true);
        echo "</form>";

        self::tag_close('div');
        self::tag_close('div');
        self::tag_close('div');
        self::tag_close('div');


    }

    /**
     * tag_open function. Create a tag with attribute/value sets
     *
     * @access public
     * @param mixed $tagName
     * @param mixed $attributesArray
     * @param mixed $content
     * @param bool  $close (default: false)
     * @return string
     */
    public static function tag_open_hidden($tagName, $attributesArray = null, $content = '', $close = false)
    {
        //open tag
        $tagString = "<$tagName ";

        //loop through attributes
        if(is_array($attributesArray) && count($attributesArray) > 0) {
            foreach($attributesArray as $attr => $val) {
                $tagString .= " $attr='$val'";


            }
        }
        //close tag
        $tagString .= ">";
        $tagString .= $content;

        if($close == true) {
            $tagString .= "</$tagName>";

        }

        return $tagString;

    }

    /**
     * tag_open function. Create a tag with attribute/value sets
     *
     * @access public
     * @param mixed $tagName
     * @param mixed $attributesArray
     * @param mixed $content
     * @param bool  $close (default: false)
     * @return void
     */
    public static function tag_open($tagName, $attributesArray = null, $content = '', $close = false)
    {
        //open tag
        $tagString = "<$tagName ";

        //loop through attributes
        self::attributes($attributesArray);
        $tagString .= " class='".self::$classOutput."'";
        $tagString .= self::$attributesOutput;

        //close tag
        $tagString .= ">";
        $tagString .= $content;

        if($close == true) {
            $tagString .= "</$tagName>";

        }

        echo $tagString;

    }


    /**
     * tag_close function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public static function tag_close($tag, $count=1)
    {

        for($i=1;$i<=$count;$i++)
        {
            echo "</$tag>";
        }


    }

    /**
     * tag_close function.
     *
     * @access public
     * @param mixed $tag
     * @return string
     */
    public static function tag_close_hidden($tag, $count=1)
    {

        for($i=1;$i<=$count;$i++)
        {
            return "</$tag>";
        }


    }


    /**
     * @param        $text
     * @param string $orientation
     * @param string $pull
     */
    public static function tooltip($text, $orientation = 'left', $pull = '')
    {
        $class = 'glyphicon glyphicon-question-sign';

        if($pull <> '') {
            $class .= " $pull";
        }


        echo "<span data-toggle='tooltip' data-placement='$orientation' title='$text' class='$class'></span>";


    }


    /**
     * error_block function. Create an error block with given text
     *
     * @access public
     * @static
     * @param mixed  $text
     * @param string $type (default: 'danger')
     * @return void
     */
    public static function error_block($text, $type = 'danger')
    {
        $size = self::$size;

        if($type == 'danger') {
            echo "<div class='alert alert-danger col-$size-12' role='alert'>";
            echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
            echo "<span class='sr-only'>Error:</span>";
        }
        else {
            if($type == 'warning') {
                echo "<div class='alert alert-warning col-$size-12' role='alert'>";
                echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
                echo "<span class='sr-only'>Warning:</span>";
            }
            else {
                if($type == 'info' || $type == 'success') {
                    echo "<div class='alert alert-$type col-$size-12' role='alert'>";
                }
            }
        }

        echo $text;
        echo "</div>";


    }


    /**
     * alert_span function.
     *
     * @access public
     * @static
     * @param mixed  $text
     * @param string $type (default: 'danger')
     * @return string
     */
    public static function alert_span($text, $type = 'danger')
    {
        return "<span class='text-$type'>$text</span>";


    }


    /**
     * msg_block function.
     *
     * @access public
     * @static
     * @param mixed  $text
     * @param string $type      (default: 'info')
     * @param string $label     (default: '')
     * @param int    $fieldSize (default: 6)
     * @param int    $offset    (default: 3)
     * @return void
     */
    public static function msg_block($text, $type = 'info', $label = '', $fieldSize = 6, $offset = 3)
    {
        //add parameter for icon?
        //acceptable type: info, warning
        $size = self::$size;
        echo "<div class='col-$size-$fieldSize col-$size-offset-$offset'>";

        if
        ($label == '') {
            echo "<p class='bg-$type'>$text</p>";

        }
        else {
            echo "<p class='bg-$type'><label>$label</label>$text</p>";
        }

        echo "</div>";


    }


    /**
     * table function. Create a table for display inline in the record page
     *
     * @access public
     * @param string $class (default: 'table table-striped table-condensed table-hover')
     * @param mixed  $headers
     * @return void
     */
    public static function table($headers, $class = 'table text-left table-striped table-sm table-hover')
    {
        //get global size from bootstrap
        $size = self::$size;

        echo "<div id='record' class='text-center col-$size'>";

        //build table
        echo "<table class='$class'>";
        echo "<thead>";

        foreach
        ($headers as $header) {
            echo "<th scope='col'>$header</th>";

        }

        self::tag_close('thead');
        echo "<tbody>";


    }

    /**
     * table function. Create a table for display inline in the record page
     *
     * @access public
     * @param string $class (default: 'table table-striped table-condensed table-hover')
     * @param mixed  $headers
     * @return string
     */
    public static function table_hidden($headers, $class = 'table text-center table-striped table-sm table-hover')
    {
        //get global size from bootstrap
        $size = self::$size;

        $return = "<div id='record' class='text-center col-$size'>";

        //build table
        $return .= "<table class='$class'>";
        $return .= "<thead>";

        foreach
        ($headers as $header) {
            $return .= "<th scope='col'>$header</th>";

        }

        $return .= self::tag_close_hidden('thead');
        $return .= "<tbody>";


        return $return;

    }

    private static function attributes($attributesArray)
    {

        self::$attributesOutput = '';

        if(is_array($attributesArray)) {//loop through attributes
            foreach($attributesArray as $attr => $val) {

                if($attr == 'class')
                {
                    self::$classOutput .= " $val";
                }
                else
                {
                    self::$attributesOutput .= " $attr='$val'";
                }



            }
        }


    }


    /**
     * table_row function. Create a table row
     *
     * @access public
     * @param mixed $data
     * @param null  $attributesArray
     * @return void
     */
    public static function table_row($data, $attributesArray = null)
    {
        echo "<tr";

        if(is_array($attributesArray)) {//loop through attributes
            foreach($attributesArray as $attr => $val) {

                echo " $attr='$val'";


            }
        }
        echo " >";

        foreach($data as $cell) {
            if(strpos($cell, '|')) {
                $cellDataAr = explode('|', $cell);
                $cellData = $cellDataAr[0];
                $cellColour = $cellDataAr[1];
                $cellLink = $cellDataAr[2];
                $cellClass = $cellDataAr[3];

                $cellReturn = "<td ";

                if($cellClass <> '')
                {
                    $cellReturn .= "class='$cellClass' ";


                }

                if($cellColour <> '')
                {
                    $cellReturn .= "style='background: $cellColour'";

                }

                if($cellLink <> '') {
                    $cellReturn .= "><a href='$cellLink' target='_blank'>$cellData</a>";

                } else {
                    $cellReturn .= ">$cellData";

                }



                $cellReturn .= "</td>";

            } else {

                $cellReturn= "<td>$cell</td>";
            }


            echo $cellReturn;
            unset($cell, $cellLink, $cellData, $cellColour, $cellClass);
        }

        echo "</tr>";

    }

    /**
     * table_row function. Create a table row
     *
     * @access public
     * @param mixed $data
     * @param null  $attributesArray
     * @return string
     */
    public static function table_row_hidden($data, $attributesArray = null)
    {
        $return = "<tr";

        if(is_array($attributesArray)) {//loop through attributes
            foreach($attributesArray as $attr => $val) {

                $return.= " $attr='$val'";


            }
        }
        $return.= " >";

        foreach($data as $cell) {
            if(strpos($cell, '|')) {
                $cellDataAr = explode('|', $cell);
                $cellData = $cellDataAr[0];
                $cellClass = $cellDataAr[1];
                $cellLink = $cellDataAr[2];
                $toolTip = $cellDataAr[3];

                $cellReturn = "<td ";

                if($toolTip <> '')
                {
                    $cellReturn .= "title='$toolTip' ";


                }

                if($cellClass <> '')
                {
                    $cellReturn .= "style='background: $cellClass'";

                }

                if($cellLink <> '') {
                    $cellReturn .= "><a href='$cellLink' target='_blank'>$cellData</a>";

                } else {
                    $cellReturn .= ">$cellData";



                }



                $cellReturn .= "</td>";

            } else {

                $cellReturn= "<td>$cell</td>";
            }


            $return.= $cellReturn;
            unset($cell, $cellLink, $cellData, $cellClass, $toolTip);
        }

        $return.= "</tr>";

        return $return;


    }


    /**
     * table_row_cb function. Ignores checkboxes in a clickable row
     *
     * @access public
     * @static
     * @param mixed $data
     * @param mixed $attributesArray (default: null)
     * @return void
     */
    public static function table_row_cb($data, $attributesArray = null)
    {
        echo "<tr";

        if(is_array($attributesArray)) {
            //display($attributesArray);

            //loop through attributes
            foreach($attributesArray as $attr => $val) {
                if(stripos($val, 'clickable-row')) {
                    //remove clickable from the string
                    $val = str_ireplace('clickable-row', '', $val);

                }
                if($attr == 'data-href') {
                    $url = $val;

                }
                echo " $attr='$val'";

            }
        }
        echo " >";

        foreach($data as $cell) {
            if(strpos($cell, '|')) {
                $cellDataAr = explode('|', $cell);
                $cellData = $cellDataAr[0];
                $cellClass = $cellDataAr[1];

                echo "<td style='background: $cellClass;'>$cellData</td>";

            }
            else {
                if(stripos($cell, "input type='checkbox'")) {
                    echo "<td>$cell</td>";

                }
                else {
                    echo "<td class='clickable-row' data-href='$url'>$cell</td>";

                }

            }
        }

        echo "</tr>";

    }


    /**
     * table_close function. Auto-close a table
     *
     * @access public
     * @return void
     */
    public static function table_close()
    {
        //end table
        self::tag_close('tbody');
        self::tag_close('table');
        self::tag_close('div');


    }

    /**
     * table_close function. Auto-close a table
     *
     * @access public
     * @return string
     */
    public static function table_close_hidden()
    {
        $return = '';

        //end table
        $return .= self::tag_close_hidden('tbody');
        $return .= self::tag_close_hidden('table');
        $return .= self::tag_close_hidden('div');


        return $return;

    }

    public static function list_group($content)
    {

        self::tag_open('ul', ['class' => 'list-group']);

        foreach($content as $item)
        {

            self::tag_open('li', ['class' => 'list-group-item'],$item,true);


        }


        self::tag_close('ul');


    }


    /**
     * help_pane function.
     *
     * @access public
     * @static
     * @param mixed $id
     * @param mixed $heading
     * @param mixed $intro
     * @param mixed $items
     * @param mixed $itemDetails
     * @return void
     */
    public static function help_pane($id, $heading, $intro, $items, $itemDetails)
    {
        //panel
        self::div_col(12);
        self::tag_open('div', ['class' => 'panel panel-primary', 'id' => $id]);
        self::tag_open('div', ['class' => 'panel-heading'], $heading, true);
        self::tag_open('div', ['class' => 'panel-body']);
        //intro
        self::tag_open('p', ['class' => 'text-center'], $intro, true);
        self::tag_close('div');

        //list details
        self::tag_open('ul', ['class' => 'list-group text-left']);

        $i = 0;

        foreach($items as $bullet) {


            if(isset($itemDetails[$i])) {

                $details = " - " . $itemDetails[$i];

            }
            else {
                $details = "";
            }

            self::tag_open('li', ['class' => 'list-group-item'], "<strong>$bullet</strong>$details", true);

            $i++;
        }

        //close panel
        self::tag_close('ul');
        self::tag_close('div');
        self::tag_close('div');


    }


    /**
     * @param     $items
     * @param     $current
     * @param int $level
     * takes a list of items and generates a simple html menu
     */
    public static function menu($items, $current, $level=3)
    {

        $output = '';
        $i = 1;

        //loop through the list of items and generate a link heading
        foreach ($items as $name=>$link)
        {

            if($link != $current) {

                $output .= "<a href='$link'>$name</a>";
                $output .= " | ";
            }


            $i++;
        }

        //if theres a pipe at the end, trim it out
        if(strrpos($output, '| ')+2 == strlen($output))
        {

            $output = substr_replace($output, '', strrpos($output, '| '));

        }

        //display the whole list as one heading
        self::heading($output, $level);
        Bootstrap4::heading(self::$recipeCount." Recipes",4);

    }


    /**
     * close function.
     *
     * @access public
     * @return void
     */
    public static function close()
    {
        echo "<div class='text-center'>";
        $founded = 2019;
        echo "<a href='index.php'>Home</a><br/>";

        $year = date('Y');

        if($year==$founded)
        {
            echo "Copyright Mark & Avie $founded";

        }
        else
        {
            echo "Copyright Mark & Avie $founded-$year";

        }

        echo "<div class='text-center'>";
        echo "<div class='col-sm-12 text-center'>";
        echo "<br/>";
        echo "<a href='https://github.com/markl181/avie/issues' target='_blank'>Github</a><br/>";
        echo "</div>";
        echo "</div>";

        echo '<p>
    <a href="http://jigsaw.w3.org/css-validator/check/referer">
        <img style="border:0;width:88px;height:31px"
            src="http://jigsaw.w3.org/css-validator/images/vcss"
            alt="Valid CSS!" />
    </a>
</p>';

        echo "</div>";
        echo "</div>";
        echo "</body>";
        echo "</html>";

    }

    /**
     * progress function.
     *
     * @access public
     * @static
     * @param mixed $value
     * @return void
     */
    public static function progress($value)
    {
        //set completion values
        $minCompletion = 15;
        $lowCompletion = 25;
        $midCompletion = 40;
        $highCompletion = 75;

        if($value <= $lowCompletion)
        {
            $classColour = 'danger';
        }
        else if ($value <= $midCompletion)
        {
            $classColour = 'warning';
        }
        else if($value <= $highCompletion)
        {

            $classColour = 'info';

        }
        else
        {
            $classColour = 'success';

        }

        echo "<div class='col-md-6 col-md-offset-3'>";
        echo "<div class='progress'>";
        echo "<div class='progress-bar progress-bar-striped progress-bar-$classColour' role='progressbar' aria-valuenow='$value' aria-valuemin='0' aria-valuemax='100' style='width: $value"."%;'>";
        echo $value.'%';
        echo "</div>";
        echo "</div>";
        echo "</div>";

        self::clearfix();


    }


    /**
     * @param        $name
     * @param string $class
     * @param null   $attributesArray
     */
    public static function button($name, $attributesArray = null)
    {

        self::$classOutput = 'btn btn-primary';
        $attributes['value'] = $name;
        $attributes['role'] = 'button';

        if($attributesArray)
        {
            $attributes = array_merge($attributes, $attributesArray);
        }

        //troubleshoot($attributes);

        self::tag_open('a',$attributes,$name,true);


    }

    public static function button_group($nameArray, $attributesArray = ['class'=>"btn btn-secondary"])
    {
        self::tag_open('div',['role'=>'group', 'aria-label'=>'Basic example']);

        foreach($nameArray as $button)
        {
            if($button <> '')
            {
                self::button($button, $attributesArray);
            }



        }

        self::tag_close('div');


    }


}


?>