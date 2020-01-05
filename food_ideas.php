<?php
/*
Created by Mark Leci - 2019-12-30

*/

/*
 * Show a list of ingredients that are not linked to any food by count.
 * Click an ingredient and prefill it into the foods form
 *
 *
 */

$pageTitle = 'Avie - Food ideas';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

Bootstrap4::heading("Some random ingredients that might give you an idea of foods to add",5);



$pdo->query($sqlGetUnmatchedIngredients);
$ingredientList = $pdo->result;

Bootstrap4::table(['Ingredient','Count']);

foreach ($ingredientList as $row)
{

    if($row['name'] <> '')
    {

        $row['name'] = "<a href='foods.php?food=".$row['name']."' target='_blank'>".$row['name']."</a>";


        Bootstrap4::table_row([$row['name'], $row['ct']]);


    }



}


Bootstrap4::table_close();



?>