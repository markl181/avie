<?php
/*
Created by Mark Leci - 2019-08-30

*/



$pageTitle = 'Cooking - Seasonal Ingredients';
require 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

if($_GET['id']) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    $pdo->query($sqlGetIngredientById, ['binds' => [$id], 'fetch' => 'one']);

    $dbRecord = $pdo->result;
    $dbId = $dbRecord['id'];
    $dbIngredient = $dbRecord['name'];


}


if(isset($_POST['submit'])) {

    //troubleshoot($_POST);

    $ingredient = filter_var($_POST['ingredient'], FILTER_SANITIZE_STRING);

    $ingredient = ucwords($ingredient);

    $pdo->query($sqlGetIngredientByName, ['binds' => [$ingredient], 'fetch' => 'one']);

    if($pdo->result) {
        //existing row
        $ingredientId = $pdo->result['id'];
    }
    else {
        $pdo->query($sqlInsertIngredient, ['binds' => [$ingredient], 'type' => 'insert']);

        $ingredientId = $pdo->rowNum;

        $bootstrap::error_block('New ingredient added', 'success');

    }

    if($_POST['end']) {
        //collect the pairings and sort by id
        $endId = filter_var($_POST['end'], FILTER_SANITIZE_NUMBER_INT);

    }

    if($_POST['start']) {

        $startId = filter_var($_POST['start'], FILTER_SANITIZE_NUMBER_INT);
        //see if a record already exists for this ingredient
        //??Are there any ingredients with non-contiguous seasons? If so we'll need to allow for multiple season
        //records per ingredient which we don't otherwise want to do


        $pdo->query($sqlCheckSeasons, ['binds'=>[$ingredientId]]);

        if($pdo->result)
        {
            //ingredient season exists

            if($pdo->result['season_start_id'] <> $startId || $pdo->result['season_end_id'] <> $endId)
            {

                //update record



            }


        }
        else
        {
            //create a new record
            $pdo->query($sqlInsertSeason, ['binds'=>[$ingredientId, $startId, $endId], 'type'=>'insert']);

            $bootstrap::error_block('New seasonal ingredient period added', 'success');

        }



    }




}


$bootstrap::linebreak(2);

$pdo->query($sqlSelectSeason);
$seasonList = array();

foreach($pdo->result as $key => $ingredient) {
    $seasonList[$ingredient['id']] = $ingredient['name'];


}

$form->open();
$pdo->datalist('ingredient', 'Ingredient:', $dbIngredient, $sqlSelectIngredient, 'name');
$form->select('start', 'Season Start:', $seasonList);
$form->select('end', 'Season End:', $seasonList);
$bootstrap::clearfix();
$bootstrap::linebreak(2);
$form->submit();
$bootstrap::clearfix();
echo "<br/><a role='button' href='seasonal.php' class='btn btn-primary' name='clear'>Clear</a>";
$form->close();

//get the current month and show what's in season
$pdo->query($sqlGetAllSeasons);

$seasonData = $pdo->result;



try {
    $curDate = new MyDateTime();
    $nextMonth = $curDate->addMonth();
    $thisMonth = $curDate->format('n');
    $nextMonth = $curDate->format('n');
}
catch(Exception $e) {
}

display($thisMonth);

//need to add last month and next month with a calculation

$inSeason = [];

foreach($seasonData as $seasonDatum) {

    if(check_month($thisMonth, $seasonDatum['month1'], $seasonDatum['month2']))
    {

        $inSeason[] = $seasonDatum['name'];

    }

}

$bootstrap::heading('In Season Now',2);

$bootstrap::table(['Last Month', 'This Month', 'Next Month']);

foreach($inSeason as $inSeasonRow)
{


    $bootstrap::table_row(['',$inSeasonRow, '']);

}

$bootstrap::table_close();


$pdo->query($sqlSeasonReport);
$reportData = $pdo->result;

$rowClass = 'text-left clickable-row';

$bootstrap::heading('All Seasons',2);

$bootstrap::table(['Ingredient', 'Start', 'End']);

foreach($reportData as $row) {

    $rowId = $row['id'];
    $url = $_SERVER['PHP_SELF'] . "?id=" . $rowId;


        $bootstrap::table_row([$row['name'], $row['start'], $row['end']], ['class' => $rowClass, 'data-href' => $url]);



}


$bootstrap::table_close();


include 'footer.php';


?>