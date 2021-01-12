<?php
/*
Created by Mark Leci - 2019-12-21

*/
//start the session
session_start();
$pageTitle = 'Avie - Foods';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__),4);


$today = get_date('Y-m-d');

$level = '';
$dbFood = '';
$id = '';

$dbFood = $_SESSION['food'];

if(isset($_SESSION['id'])) {

    //the GET part has to be replaced with SESSION and put first
    $id = filter_var($_SESSION['id'], FILTER_SANITIZE_NUMBER_INT
    );

    $pdo->query($sqlGetFoodById, ['binds'=>[$id], 'fetch'=>'one']);
    $dbRow = $pdo->result;

    $dbFood = $dbRow['name'];
    $level = $dbRow['level'];


}

if(isset($_POST['submit']))
{

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $food = filter_var($_POST['food'], FILTER_SANITIZE_STRING);
    $food = ucwords($food);
    $level = filter_var($_POST['level'], FILTER_SANITIZE_NUMBER_INT
        );
    $formId = filter_var($_POST['food_id'], FILTER_SANITIZE_NUMBER_INT
    );

    if(!$formId)
    {
        //check if the food already exists and insert it if not
        $pdo->idColumn = 'id';
        $pdo->searchColumn = 'name';
        $pdo->searchTable = 'avie_food';
        $pdo->searchValue = $food;
        $pdo->insertQuery = $sqlInsertFood;
        $pdo->insertBinds = [$food, $level];

        $pdo->find_id();
        $foodId = $pdo->recordId;

    }
    else
    {
        //update the existing food
        $pdo->query($sqlGetFoodById, ['binds'=>[$formId], 'fetch'=>'one']);
        $dbRow = $pdo->result;

        $dbFood = $dbRow['name'];
        $dbLevel = $dbRow['level'];

        if($dbLevel <> $level || $dbFood <> $food)
        {
            //update the record
            $sqlUpdateFood = "UPDATE avie_food SET name = ?, level_id = ? WHERE id = ?";

            $pdo->query($sqlUpdateFood, ['binds'=>[$food, $level, $formId], 'type'=>'update']);

        }


    }

    //after the update, unset the form values used so the form is cleared, and end the session
    unset($dbFood, $level, $dbLevel);
    session_destroy();

}



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
$form->datalist_text('food','Food:',$dbFood, $foodList);
$form->hidden('food_id', $id);
$form->selectquery('level', 'Level:', $levelsList, 'name'
    , 'id',$level, ['autofocus'=>'autofocus']);
$form->submit();
echo "<a role='button' href='foods.php' class='btn btn-primary'>Clear</a>";
$form->close();
Bootstrap4::linebreak(2);
Bootstrap4::error_block("For best results, use the singular form of a food, e.g. almond not almonds. Click a row 
to edit"
    ,'info');

Bootstrap4::linebreak(2);
Bootstrap4::heading('Food List',2);

//summary goes here
Bootstrap4::table(['Level','# of Foods'],'table text-center table-striped table-sm table-hover');
$total = 0;
foreach($levelsList as $level)
{

    //make each link clickable and jump to that section of the table

    Bootstrap4::table_row([$level['name'], $level['foods']]);
    $total += $level['foods'];

}
Bootstrap4::table_row(['Total',"$total"]);
//@@todo - automate making the total row and having it as bold
Bootstrap4::table_close();

Bootstrap4::table(['Food','Level']);

$rowClass = 'text-left clickable-row';

error_reporting(0);

foreach($foodListAr as $food)
{

    //send the url to process along with the get values
    $url = "process.php?ref=foods&id=" . $food['id'];

    $food['name'] = "<a href='recipes.php?ingredient=".$food['id']."' target='_blank'>".$food['name']."</a>";

    $food['name'] = food_colour($food['name'],$food['level']);

    Bootstrap4::table_row([$food['name'], $food['level']], ['class' => $rowClass, 'data-href' => $url]);

}

Bootstrap4::table_close();


include 'footer.php';

?>