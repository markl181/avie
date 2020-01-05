<?php
/*
Created by Mark Leci - 2019-12-22

*/

$pageTitle = 'Avie - Recipes';

include 'header.php';

/*
 * Make a recipe clickable
 *
 * Show the filter details ie ingredient name
 *
 */

Bootstrap4::menu($menu, basename(__FILE__));

error_reporting(0);

$ingredient = $_GET['ingredient'];
$includes = $_GET['include'];
$includeBlack = $_GET['includeblack'];
$redLimit = $_GET['redlimit'];
$greenLimit = $_GET['greenlimit'];
$course = $_GET['course'];
$rating = $_GET['rating'];

$includeBlack = filter_var($includeBlack, FILTER_SANITIZE_NUMBER_INT);
$redLimit = filter_var($redLimit, FILTER_SANITIZE_NUMBER_INT);
$greenLimit = filter_var($greenLimit, FILTER_SANITIZE_NUMBER_INT);
$course = filter_var($course, FILTER_SANITIZE_STRING);
$rating = filter_var($rating, FILTER_SANITIZE_NUMBER_INT);

if($ingredient)
{
    $ingredient = filter_var($ingredient, FILTER_SANITIZE_NUMBER_INT);

    $pdo->query($sqlFilterRecipeByIngredient, ['binds'=>[$ingredient],'fetch'=>'all']);

    $filters[] = 3;

}
else if($includes) {
    $includes = filter_var($includes, FILTER_SANITIZE_STRING);
    $includes = explode(" ", $includes);
    $includeCount = count($includes);

    if($includeCount == 1) {

        $sqlSearch = $sqlGetRecipesByInclude . $sqlFinishOneInclude;
        $pdo->query($sqlSearch, ['binds' => [$includes[0]]]);

    }
    else {
        //multiple includes
        $firstInclude = " AND afi.food_id = ?";

        $sqlSearch = $sqlGetRecipesByInclude . $firstInclude;

        $addsql = " AND ari.recipe_id IN 
(SELECT DISTINCT ari.recipe_id
FROM avie_food_ingredient afi
INNER JOIN avie_recipe_ingredient ari ON ari.ingredient_id = afi.ingredient_id
WHERE afi.food_id = ?)";

        for($i = 1; $i < $includeCount; $i++) {

            $sqlSearch .= $addsql;


        }

        $sqlSearch .= " ORDER BY ar.course, ar.title";
        //display($sqlSearch);
        $pdo->query($sqlSearch, ['binds' => $includes]);

    }

    $filters[] = 2;

}
else
{
    $pdo->query($sqlGetRecipesWithRating);

    $filters[] = 1;
}

$recipeList = $pdo->result;

    $previousId = '';

    foreach($recipeList as $key=>&$recipe)
    {

        if($previousId == $recipe['public_id'])
        {
            unset($recipeList[$key]);

        }
        if($rating <> '' && $recipe['rating'] < $rating)
        {
            unset($recipeList[$key]);

        }
        if($course <> '' && $recipe['course'] <> $course)
        {
            unset($recipeList[$key]);
        }
        if($includeBlack != 1 && $recipe['blackct'] >0)
        {
            unset($recipeList[$key]);
        }
        if($redLimit <> '' && $redLimit >=0 && $recipe['redct'] >= $redLimit)
        {
            unset($recipeList[$key]);

        }
        if($greenLimit >0 && $recipe['greenct'] < $greenLimit)
        {

            unset($recipeList[$key]);

        }

        //how to handle the includes now - just a straght query with IN clause?

        $previousId = $recipe['public_id'];
    }

$recipeCount = count($recipeList);

//display($recipeList);


        if($recipeCount == 0)
        {
            Bootstrap4::heading("No recipes found!",2);
            Bootstrap4::heading("If you just added a food, please wait up to 1 hour and search again",3);

        }
        else
        {
            Bootstrap4::heading("Recipe List - Filtered ($recipeCount recipes)",2);
        }

        if($filters)
        {
            $text = '';

            foreach($filters as $filter)
            {
                if($filter == 1)
                {
                    $text.= 'only rated recipes';

                }
                if($filter == 2)
                {
                    $text.= 'only specific ingredients';

                }
                if($filter == 3)
                {
                    $text.= 'only specific ingredient';

                }

            }

            Bootstrap4::error_block("Filtered - $text",'info');

        }


//Form
$form->open('get');
$form->select('rating','Rating: ',[3=>'Rating >=3', 4=>'Rating >=4'
    , 5=>'Rating 5'], $rating);
$form->select('course','Course: ',[''=>'','Appetizer'=>'Appetizer','Breakfast'=>'Breakfast'
    ,'Dessert'=>'Dessert','Main Course'=>'Main Course','Salad'=>'Salad'
    ,'Sides'=>'Sides','Soup'=>'Soup'], $course);
$form->input('redlimit','Max Red:',['type'=>'number','min'=>0,'max'=>10, 'value'=>$redLimit]);
$form->input('greenlimit','Min Green:',['type'=>'number','min'=>0,'max'=>10, 'value'=>$greenLimit]);
$form->submit();
$form->close();


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