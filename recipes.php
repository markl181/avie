<?php
/*
Created by Mark Leci - 2019-12-22

*/

$pageTitle = 'Avie - Recipes';

include 'header.php';

/*
 * Make a recipe clickable
 * Show a filtered version of this page on load from another page
 *
 */

Bootstrap4::menu($menu, basename(__FILE__));

error_reporting(0);

$ingredient = $_GET['ingredient'];

if($ingredient)
{
    $pattern = '/\(.*$/';
    $ingredient = preg_replace($pattern,'',$ingredient);
    $ingredient = trim($ingredient);

    $ingredient = "%$ingredient%";

    $pdo->query($sqlFilterRecipeByIngredient, ['binds'=>[$ingredient],'fetch'=>'all']);

    $recipeList = $pdo->result;
    $recipeCount = count($recipeList);

    Bootstrap4::heading("Recipe List - Filtered ($recipeCount recipes)",2);

}
else
{
    $pdo->query($sqlGetRecipes);

    $recipeList = $pdo->result;
    $recipeCount = count($recipeList);

    Bootstrap4::heading("Recipe List ($recipeCount recipes)",2);

}




Bootstrap4::table(['Title','Course','Rating','Time (m)','Photo']);

/*
 * Add in course jumps
 *
 */

foreach($recipeList as $recipe)
{
    $recipe['time'] = $recipe['prep_time'] + $recipe['cook_time'];
    $recipe['title'] = "<a target='_blank' href='".$recipe['public_url']."'>".$recipe['title']."</a>";
    $recipe['photo'] = "<img height='50px' src='".$recipe['photo']."'/>";

    $recipe['rating'] = ($recipe['rating'] == 0) ? '' : $recipe['rating'];
    $recipe['time'] = ($recipe['time'] == 0) ? '' : $recipe['time'];

    Bootstrap4::table_row([
        $recipe['title'], $recipe['course'], $recipe['rating'], $recipe['time']
        , $recipe['photo']

    ]);


}

Bootstrap4::table_close();


include 'footer.php';


?>