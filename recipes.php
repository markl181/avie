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

$pdo->query($sqlGetRecipes);
$recipeList = $pdo->result;


Bootstrap4::heading('Food List',2);
Bootstrap4::table(['Title','Course','Rating','Time (m)','Photo']);

foreach($recipeList as $recipe)
{
    $recipe['time'] = $recipe['prep_time'] + $recipe['cook_time'];
    $recipe['title'] = "<a target='_blank' href='".$recipe['public_url']."'>".$recipe['title']."</a>";
    $recipe['photo'] = "<img height='50px' src='".$recipe['photo']."'/>";

    Bootstrap4::table_row([
        $recipe['title'], $recipe['course'], $recipe['rating'], $recipe['time']
        , $recipe['photo']

    ]);


}

Bootstrap4::table_close();


include 'footer.php';


?>