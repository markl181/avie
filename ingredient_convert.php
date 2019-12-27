<?php
/*
Created by Mark Leci - 2019-12-26

*/

$pageTitle = 'Avie - Convert';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

/*
 * Really only need to check if the ingredients or foods have been modified since last check...
 * If a food changes and matches different ingredients, that's going to cause problems!
 */



$pdo->query($sqlGetFoods);
$foodList = $pdo->result;


$pdo->query($sqlGetIngredientTime, ['fetch'=>'one']);
$ingredientTime = $pdo->result['maxtime'];


foreach($foodList as $food)
{

    //set_time_limit(30);
    $pattern = '/\(.*$/';
    $foodSearch = preg_replace($pattern,'',$food['name']);
    $foodSearch = trim($foodSearch);
    $foodSearch = "%$foodSearch%";

    //create a food->ingredient translation
    $pdo->query($sqlGetRecipeIngredient, ['binds'=>[$foodSearch, $ingredientTime]]);
    $ingredientList = $pdo->result;

    foreach($ingredientList as $ingredient)
    {

        set_time_limit(30);

        $pdo->query($sqlGetFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'fetch'=>'one']);

        if(!$pdo->result)
        {

            $pdo->query($sqlInsertFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'type'=>'insert']);


        }




    }

    echo "$foodSearch search completed<br/>";

}


include 'footer.php';

?>