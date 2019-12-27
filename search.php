<?php
/*
Created by Mark Leci - 2019-12-26

*/
$pageTitle = 'Avie - Recipe Search';

if(isset($_POST['submit']))
{

    error_reporting(E_ALL);

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $searchUrl = 'recipes.php?include=';
    $includeFoodIds = [];

    foreach($_POST as $key=>$item)
    {

        if($item == 1)
        {
            //box is checked
            $key = filter_var($key, FILTER_SANITIZE_STRING);

            if(strpos($key,'checked_')==0)
            {
                $foodId = substr($key,strlen('checked_'));

                if(is_numeric($foodId))
                {
                    $includeFoodIds[]=$foodId;
                }

            }

        }



    }

    if(count($includeFoodIds) > 0)
    {

        $includelist = implode("+",$includeFoodIds);

        $searchUrl .= $includelist;

    }

    //echo $_SERVER['SERVER_NAME'].$searchUrl;

    header("Location: $searchUrl");

}


include 'header.php';

error_reporting(1);

/*
 * Show some checkboxes and redirect to the search page based on them
 *
 *
 */

Bootstrap4::menu($menu, basename(__FILE__));


$today = get_date('Y-m-d');


$pdo->query($sqlGetFoods);
$foodListAr = $pdo->result;

foreach($foodListAr as $food)
{

    $foodList[] = $food['name'];


}

$pdo->query($sqlGetLevels, ['fetch' => 'all']);
$levelsList = $pdo->result;

$form->open();
//$form->input('date','Date',['type'=>'date', 'value'=>$today]);


Bootstrap4::error_block("Select the foods to search for!"
    ,'info');

Bootstrap4::linebreak(2);
Bootstrap4::heading('Food List',2);
Bootstrap4::table(['Food','Level','Include']);

foreach($foodListAr as $food)
{

    $food['name'] = "<a href='recipes.php?ingredient=".$food['name']."' target='_blank'>".$food['name']."</a>";
    $food['include'] = "<input type='checkbox' value='1' name='checked_".$food['id']."' />";

    Bootstrap4::table_row([$food['name'], $food['level'],$food['include']]);


}

Bootstrap4::table_close();

$form->submit();
$form->close();


include 'footer.php';

?>