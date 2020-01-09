<?php
/*
Created by Mark Leci - 2019-12-30

*/

$pageTitle = 'Avie - Food ideas';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__),4);


$pdo->query($sqlGetUnmatchedIngredients);
$ingredientList = $pdo->result;

$ingredientCount = count($ingredientList);

Bootstrap4::heading("Since last update",4);
Bootstrap4::heading("$ingredientCount unlinked ingredients that might give you an idea of foods to add",5);

$pdo->query($sqlGetRecipesByInclude);
$recipeList = $pdo->result;
$emptyCt = 0;
$noRedCt = 0;
$noGreenCt = 0;

foreach($recipeList as $recipe)
{

    if($recipe['redct'] == '' && $recipe['greenct'] == '')
    {
        $emptyCt ++;

    }
    if ($recipe['redct'] == '')
    {
        $noRedCt ++;

    }
    if($recipe['greenct'] == '')
    {
        $noGreenCt ++;
    }

}

Bootstrap4::heading("$emptyCt Recipes with no red or green ingredients",5);
Bootstrap4::heading("$noRedCt Recipes with no red ingredients",5);
Bootstrap4::heading("$noGreenCt Recipes with no green ingredients",5);



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