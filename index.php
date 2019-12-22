<?php
$pageTitle = 'Avie - Home';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

$today = get_date('Y-m-d');

/*
//show daily table
$pdo->query($sqlGetDailyFoods, ['binds'=>[$today],'fetch'=>'all']);
$dailyFoods = $pdo->result;


if($dailyFoods) {

    Bootstrap4::linebreak(2);
    Bootstrap4::heading('Food Diary',2);
    Bootstrap4::table(['Food','Meal','Meal Grps','Food Group','Quantity','Remaining']);


    $pdo->query($sqlGetDailyGroup, ['binds'=>[$today]]);
    $dailyGroupTotals = $pdo->result;
    $dailyGroupTotals = array_flatten($dailyGroupTotals, 'food_group_id', 'quantity');

    $pdo->query($sqlGetMealGroupCount, ['binds'=>[$today]]);
    $dailyMealGroupTotals = $pdo->result;
    $dailyMealGroupTotals = array_flatten($dailyMealGroupTotals, 'meal_id','count');


    foreach($dailyFoods as $dailyFood) {

        if($dailyFood['goal'] - $dailyFood['quantity'] < 0) {
            $dailyFood['remaining'] = 0;

        }
        else {
            $dailyFood['remaining'] = $dailyFood['goal'] - $dailyGroupTotals[$dailyFood['food_group_id']];


        }

        $dailyFood['group_count'] = $dailyMealGroupTotals[$dailyFood['meal_id']];

        Bootstrap4::table_row([$dailyFood['food'], $dailyFood['meal'], $dailyFood['group_count'], $dailyFood['food_group']
            , $dailyFood['quantity'], $dailyFood['remaining']]);

    }
    Bootstrap4::table_close();

}
*/

include 'footer.php';

?>
