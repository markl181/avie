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
$includes = $_GET['include'];
$includeBlack = $_GET['includeblack'];
$redLimit = $_GET['redlimit'];

$redLimitClause =
    "
    AND ar.public_id NOT IN
    (
    SELECT DISTINCT ar.public_id
FROM avie_food_ingredient afi
INNER JOIN avie_recipe_ingredient ari ON ari.ingredient_id = afi.ingredient_id
INNER JOIN avie_recipe ar ON ar.public_id = ari.recipe_id
INNER JOIN avie_food af ON af.id = afi.food_id
WHERE af.level_id = 5
GROUP BY ar.public_id, ar.title
HAVING COUNT(DISTINCT afi.food_id) > ?
    
    )
    
    ";

$includeNoBlackClause = "
 AND ar.id NOT IN
(
SELECT ar.id FROM
avie_recipe ar 
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_food_ingredient afi ON ari.ingredient_id = afi.ingredient_id
INNER JOIN avie_food af ON af.id = afi.food_id
WHERE level_id = 4
)

";

$sqlFinishOneInclude = " AND
afi.food_id = ?
ORDER BY ar.course, ar.title";

if($ingredient)
{
    $ingredient = filter_var($ingredient, FILTER_SANITIZE_NUMBER_INT);

    $pdo->query($sqlFilterRecipeByIngredient, ['binds'=>[$ingredient],'fetch'=>'all']);

    $recipeList = $pdo->result;
    $recipeCount = count($recipeList);

    Bootstrap4::heading("Recipe List - Filtered ($recipeCount recipes)",2);

}
else if($includes)
{

    $includes = explode(" ",$includes);

    $includeCount = count($includes);

    if($includeCount == 1)
    {
        if($includeBlack != 1)
        {
            $sqlSearch = $sqlGetRecipesByInclude.$includeNoBlackClause;

            if($redLimit > 0)
            {
                $sqlSearch .= $redLimitClause;
                $sqlSearch .= $sqlFinishOneInclude;
                $pdo->query($sqlSearch, ['binds'=>[$redLimit, $includes[0]]]);
            }
            else
            {
                $sqlSearch .= $sqlFinishOneInclude;
                $pdo->query($sqlSearch, ['binds'=>[$includes[0]]]);

            }


        }
        else
        {
            $sqlSearch = $sqlGetRecipesByInclude;

            if($redLimit > 0)
            {
                $sqlSearch .= $redLimitClause;
                $sqlSearch .= $sqlFinishOneInclude;
                $pdo->query($sqlSearch, ['binds'=>[$redLimit, $includes[0]]]);

            }
            else
            {
                $sqlSearch .= $sqlFinishOneInclude;
                $pdo->query($sqlSearch, ['binds'=>[$includes[0]]]);
            }


        }



    }
    else
    {
        //multiple includes
        $firstInclude = " AND afi.food_id = ?";

        $sqlSearch = $sqlGetRecipesByInclude.$firstInclude;

        if($includeBlack != 1)
        {

            $sqlSearch .= $includeNoBlackClause;


        }

        $addsql = " AND ari.recipe_id IN 
(SELECT DISTINCT ari.recipe_id
FROM avie_food_ingredient afi
INNER JOIN avie_recipe_ingredient ari ON ari.ingredient_id = afi.ingredient_id
WHERE afi.food_id = ?)";



       for($i=1;$i<$includeCount;$i++)
       {

           $sqlSearch.= $addsql;


       }

        //display($sqlSearch);

        if($redLimit > 0)
        {
            $sqlSearch .= $redLimitClause;
            $sqlSearch .= " ORDER BY ar.course, ar.title";

            $includes[] = $redLimit;

            $pdo->query($sqlSearch, ['binds'=>$includes]);

        }
        else
        {
            error_reporting(E_ALL);

            $sqlSearch .= " ORDER BY ar.course, ar.title";
            $pdo->query($sqlSearch, ['binds'=>$includes]);
        }



    }


    $recipeList = $pdo->result;
    $recipeCount = count($recipeList);

    Bootstrap4::heading("Recipe List - Filtered ($recipeCount recipes)",2);


    /*
     * We're providing a list of foods, and we want to return all recipes that have ingredients that have those foods
     * Step 1 is to get a list of all ingredients with EITHER of those foods in
     *
     */


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