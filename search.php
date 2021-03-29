<?php
/*
Created by Mark Leci - 2019-12-26

*/
$pageTitle = 'Avie - Search';

if(isset($_POST['submit']))
{

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $searchUrl = 'recipes.php?include=';
    $includeFoodIds = [];

    $includeBlack = filter_var($_POST['includeblack'],FILTER_SANITIZE_NUMBER_INT);
    $redLimit = filter_var($_POST['redlimit'],FILTER_SANITIZE_NUMBER_INT);



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

    if($includeBlack == 1)
    {
        $searchUrl .= '&includeblack=1';

    }
    if($redLimit > 0)
    {

        $searchUrl .= '&redlimit='.$redLimit;

    }


    header("Location: $searchUrl");

}


include 'header.php';

/*
 * Show some checkboxes and redirect to the search page based on them
 *
 *
 */

Bootstrap4::menu($menu, basename(__FILE__),4);


$today = get_date('Y-m-d');


$pdo->query($sqlGetFoodsForSearch);
$foodListAr = $pdo->result;

foreach($foodListAr as $food)
{

    $foodList[] = $food['name'];


}

$pdo->query($sqlGetLevels, ['fetch' => 'all']);
$levelsList = $pdo->result;

$form->open();
//$form->input('date','Date',['type'=>'date', 'value'=>$today]);


Bootstrap4::error_block("Select the foods to search for! Black Labelled Ingredients are Filtered Out"
    ,'info');

Bootstrap4::linebreak(2);
Bootstrap4::heading('Food List',2);

$form->checkbox('includeblack','Include Recipes with Black Labelled Ingredients');
$form->input('redlimit','Limit to x red ingredients',['type'=>'number','min'=>0,'max'=>99]);
Bootstrap4::linebreak(2);
$form->submit('submit','Search');
Bootstrap4::table(['Food','Level','Include']);

foreach($foodListAr as $food)
{

    $food['name'] = "<a href='recipes.php?ingredient=".$food['name']."' target='_blank'>".$food['name']."</a>";
    $food['include'] = "<input type='checkbox' value='1' name='checked_".$food['id']."' />";

$food['name'] = food_colour($food['name'],$food['level']);

    Bootstrap4::table_row([$food['name'], $food['level'],$food['include']]);


}

Bootstrap4::table_close();

$form->submit('submit','Search');
$form->close();


include 'footer.php';

?>