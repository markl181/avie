<?php
/*
Created by Mark Leci - 2019-12-22

*/

$pageTitle = 'Avie - Parse';

include 'header_log.php';

//Bootstrap4::menu($menu, basename(__FILE__));

//error_reporting(0);

/*
 * Reorder code once all updates are done so we only check the deltas
 *
 *
 * 0 - title
 * 1 - course
 * 3 - main ingredient
 * 6 - url
 * 7 - website
 * 8 - prep time
 * 9 - cook time
 * 10 - total time
 * 11 - servings
 * 12 - yield
 * 13 - ingredients
 * 15 - tags
 * 16 - rating
 * 17 - public url
 * 18 - photo
 * 31 - updated at
 *
 */

$start = microtime(true);

error_reporting(1);

$fileName = 'plantoeat-recipes.csv';
$rowLimit = 500000;

$file = fopen($fileName, 'r');

$i = 0;

while (($data = fgetcsv($file, 0, ",")) !==FALSE && $i <= $rowLimit)
{
    // Read the data
    if($i == 0)
    {
        display($data);
    }


    if($i > 0)
    {
        //exclude header row
        set_time_limit(30);
        $title = $data[0];
        $course = $data[1];
        $mainIngredient = $data[3];
        $url = $data[6];
        $website = $data[7];
        $prepTime = $data[8];
        $cookTime = $data[9];
        $servings = $data[11];
        $yield = $data[12];
        $ingredients = $data[13];
        $tags = $data[15];
        $rating = $data[16];
        $publicUrl = $data[17];
        $photo = $data[18];
        $updatedAt = $data[31];

        //initial processing
        $tags = explode(", ",$tags);
        $ingredients = explode("\n",$ingredients);
        $publicId = str_ireplace('https://www.plantoeat.com/recipes/','',$publicUrl);

        $pdo->logFind = true;

        foreach($tags as $tag) {
            if($tag <> '') {
                //check if the record already exists and insert it if not
                $pdo->idColumn = 'id';
                $pdo->searchColumn = 'name';
                $pdo->searchTable = 'avie_tag';
                $pdo->searchValue = $tag;
                $pdo->insertQuery = $sqlInsertTag;
                $pdo->insertBinds = [$tag];

                $pdo->find_id();
                $tagId = $pdo->recordId;

                //check if the tag exists for the recipe
                $pdo->query($sqlSelectRecipeTag, ['binds'=>[$publicId, $tagId], 'fetch'=>'one']);
                $recipeTag = $pdo->result;

                if(!$recipeTag)
                {

                    $pdo->query($sqlInsertRecipeTag, ['binds'=>[$publicId, $tagId], 'type'=>'insert']);
                    echo "Recipe Tag Created for recipe $publicId<br/>";
                }

                //@@todo do we want to remove any tags that were removed?

            }
        }

            if($title <> '' && is_numeric($publicId))
            {
                //check recipe based on public url
                $pdo->query($sqlGetRecipeByPublicId, ['binds'=>[$publicId], 'fetch'=>'one']);
                $recipe = $pdo->result;

                if($recipe)
                {
                    //recipe exists, check the update date
                    $dbUpdatedAt = $recipe['updated_at'];

                    if($dbUpdatedAt <> $updatedAt)
                    {

                        $update = true;

                    }


                }
                else
                {
                    //need to add recipe
                    $pdo->query($sqlInsertRecipe, ['binds'=>[$publicId, $title, $course, $mainIngredient, $url
                        , $website, $prepTime, $cookTime, $servings, $yield, $rating, $publicUrl, $photo, $updatedAt]
                        , 'type'=>'insert']);

                    echo "Recipe $publicId Created<br/>";

                    $update = true;

                }

                if($update = true)
                {
                    //process update



                    unset ($update);
                }

                foreach ($ingredients as $ingredient)
                {
                    if($ingredient <> '') {

                        //$ingredient = str_ireplace("1/2 cup","",$ingredient);
                        $ingredient = parse_ingredient($ingredient);

                        //check if the record already exists and insert it if not
                        $pdo->idColumn = 'id';
                        $pdo->searchColumn = 'name';
                        $pdo->searchTable = 'avie_ingredient';
                        $pdo->searchValue = $ingredient;
                        $pdo->insertQuery = $sqlInsertIngredient;
                        $pdo->insertBinds = [$ingredient];

                        $pdo->find_id();
                        $ingredientId = $pdo->recordId;

                        //check if the ingredient exists for the recipe
                        $pdo->query($sqlSelectRecipeIngredient, ['binds'=>[$publicId, $ingredientId], 'fetch'=>'one']);
                        $recipeIngredient = $pdo->result;

                        if(!$recipeIngredient)
                        {

                            $pdo->query($sqlInsertRecipeIngredient, ['binds'=>[$publicId, $ingredientId], 'type'=>'insert']);
                            echo "Recipe Ingredient Created for recipe $publicId<br/>";
                        }

                        //@@todo do we want to remove any ingredients that were removed?



                    }

                }


            }


        }




    $i++;

}

fclose($file);

exec_time($start, __LINE__);

$pdo->query($sqlInsertUpdate, ['binds'=>['1'], ['type'=>'insert']]);


//include 'footer.php';




?>