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
 * check the box if the recipe was requested already and request is active
 *
 */

Bootstrap4::menu($menu, basename(__FILE__),4);

error_reporting(1);

$ingredient = $_GET['ingredient'];
$includes = $_GET['include'];
$includeBlack = $_GET['includeblack'];
$redLimit = $_GET['redlimit'];
$greenLimit = $_GET['greenlimit'];
$course = $_GET['course'];
$rating = $_GET['rating'];
$keyword = $_GET['keyword'];

$approvedTags = ['Avie-Approved','Freezable'];

$includeBlack = filter_var($includeBlack, FILTER_SANITIZE_NUMBER_INT);
$redLimit = filter_var($redLimit, FILTER_SANITIZE_NUMBER_INT);
$greenLimit = filter_var($greenLimit, FILTER_SANITIZE_NUMBER_INT);
$course = filter_var($course, FILTER_SANITIZE_STRING);
$rating = filter_var($rating, FILTER_SANITIZE_NUMBER_INT);

foreach($_GET as $key=>$item)
{

    if(strpos($key,'request') !== FALSE)
    {

       $recipeId = str_replace('request_','',$key);
       $recipeId = filter_var($recipeId, FILTER_SANITIZE_NUMBER_INT);

        //check if the request already exists and insert it if not
        $sqlGetRequestById = "SELECT id FROM avie_recipe_request WHERE active = 1 AND recipe_id = ?";
        $sqlInsertRequest = "INSERT INTO avie_recipe_request (recipe_id) VALUES (?)";

        $pdo->query($sqlGetRequestById, ['binds'=>[$recipeId],'fetch'=>'one']);

        if(!$pdo->result)
        {
            $pdo->query($sqlInsertRequest, ['binds'=>[$recipeId],'type'=>'insert']);

            Bootstrap4::error_block("Request submitted for recipe $recipeId",'success');
        }



    }


}


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
else if ($keyword||$redLimit||$greenLimit||$rating||$course)
{
    //query all the recipes and just eliminate the filtered ones
    $pdo->query($sqlGetAllRecipes);


}
else
{
    //default form if nothing is set

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
            continue;

        }
        if($rating <> '' && $recipe['rating'] < $rating)
        {
            unset($recipeList[$key]);
            continue;
        }
        if($course <> '' && $recipe['course'] <> $course)
        {
            unset($recipeList[$key]);
            continue;
        }
        if($includeBlack != 1 && $recipe['blackct'] >0)
        {
            unset($recipeList[$key]);
            continue;
        }
        if($redLimit <> '' && $redLimit >=0 && $recipe['redct'] >= $redLimit)
        {
            unset($recipeList[$key]);
            continue;
        }
        if($greenLimit >0 && $recipe['greenct'] < $greenLimit)
        {

            unset($recipeList[$key]);
            continue;

        }
        if($keyword <> '' )
        {

            if(stripos($recipe['title'],$keyword) === FALSE
                &&
                stripos($recipe['main_ingredient'],$keyword) === FALSE)
            {
                //just add in any additonal search fields above
                unset($recipeList[$key]);
                continue;

            }


        }

        //how to handle the includes now - just a straght query with IN clause?

        $previousId = $recipe['public_id'];
    }

$recipeList = array_filter($recipeList);
$recipeCount = count($recipeList);


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
                    $text.= 'only top rated recipes';

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


$pdo->query($sqlGetCourses, ['fetch' => 'all']);
$coursesList = $pdo->result;

//Form
$form->open('get');
$form->input('keyword','Keyword: ',['type'=>'text','value'=>$keyword]);
$form->select('rating','Rating: ',[3=>'Rating >=3', 4=>'Rating >=4'
    , 5=>'Rating 5'], $rating);
$form->selectquery('course', 'Course: ', $coursesList, 'course'
    , 'course',$course);
/*$form->select('course','Course: ',[''=>'','Appetizers'=>'Appetizers','Beverages'=>'Beverages'
    ,'Breakfast'=>'Breakfast'
    ,'Dessert'=>'Dessert','Main Course'=>'Main Course','Salad'=>'Salad'
    ,'Sides'=>'Sides','Soup'=>'Soup'], $course);*/
$form->input('redlimit','Max Red:',['type'=>'number','min'=>0,'max'=>10, 'value'=>$redLimit]);
$form->input('greenlimit','Min Green:',['type'=>'number','min'=>0,'max'=>10, 'value'=>$greenLimit]);



Bootstrap4::error_block('Check a box to request a recipe','info');

Bootstrap4::table(['Title','Course','Rating','Time (m)','Red','Green','Tags','Photo','']);

/*
 * Add in course jumps
 *
 */

//make query for tag search
$inClause = str_repeat('?,', count($approvedTags) - 1) . '?';
$sqlGetRecipeTags = "SELECT a.name FROM avie_recipe_tag art INNER JOIN
    avie_tag a on art.tag_id = a.id
where name in ($inClause)
    and recipe_id = ? 
";


foreach($recipeList as $key=>$recipeItem)
{

    $tagSearch = $approvedTags;
    $tagSearch[] = $recipeItem['public_id'];

    $pdo->query($sqlGetRecipeTags, ['binds'=>$tagSearch]);

    $recipeTags = $pdo->result;

    $recipeTags = array_flatten($recipeTags,'name');

    $recipeItem['tags'] = implode(",",$recipeTags);

    $recipeItem['time'] = $recipeItem['prep_time'] + $recipeItem['cook_time'];
    $recipeItem['time'] = ($recipeItem['time'] == 0) ? '' : $recipeItem['time'];
    $recipeItem['time'] = convertToHoursMins($recipeItem['time']);

    $recipeItem['title'] = $recipeItem['title']."||".$recipeItem['public_url'];
    $recipeItem['photo'] = "<img height='50px' src='".$recipeItem['photo']."'/>";
    $recipeItem['rating'] = ($recipeItem['rating'] == 0) ? '' : $recipeItem['rating'];

    if($recipeItem['request_id'])
    {
        $recipeItem['request'] = ' checked';
    }

    $recipeItem['redct'] = colorscale(0, $redline, $recipeItem['redct'], $colorindexRed);
    $recipeItem['greenct'] = colorscale(0, $greenline, $recipeItem['greenct'], $colorindexgreen);


    $recipeItem['request'] = "<input type='checkbox' value='1'".$recipeItem['request']." name='request_"
        .$recipeItem['public_id']."' />";

    Bootstrap4::table_row([
        $recipeItem['title']
        , $recipeItem['course'], $recipeItem['rating'], $recipeItem['time']
        ,$recipeItem['redct'],$recipeItem['greenct'],$recipeItem['tags']
        , $recipeItem['photo'], $recipeItem['request']

    ]);



}

Bootstrap4::table_close();

$form->submit();
$form->close();

include 'footer.php';


?>