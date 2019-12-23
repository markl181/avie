<?php
/*
Created by Mark Leci - 2019-12-21

*/
$pageTitle = 'Avie - Foods';

include 'header.php';

/*
 * Make a food clickable?
 *
 */

Bootstrap4::menu($menu, basename(__FILE__));


$today = get_date('Y-m-d');

$level = '';

if(isset($_POST['submit']))
{

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $food = filter_var($_POST['food'], FILTER_SANITIZE_STRING);
    $food = ucwords($food);
    $level = filter_var($_POST['level'], FILTER_SANITIZE_NUMBER_INT
        );

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
$form->datalist_text('food','Food:','', $foodList);

$form->selectquery('level', 'Level:', $levelsList, 'name'
    , 'id',$level, ['autofocus'=>'autofocus']);
$form->submit();
$form->close();

Bootstrap4::linebreak(2);
Bootstrap4::heading('Food List',2);
Bootstrap4::table(['Food','Level','Recipes']);

foreach($foodListAr as $food)
{
    $food['name'] = "<a href='recipes.php?ingredient=".$food['name']."' target='_blank'>".$food['name']."</a>";

    Bootstrap4::table_row([$food['name'], $food['level'],'']);


}

Bootstrap4::table_close();


include 'footer.php';

?>