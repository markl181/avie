<?php
/*
Created by Mark Leci - 2019-12-22

*/

$pageTitle = 'Avie - Parse';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

//error_reporting(0);

/*
 * Can we download a file say daily from https://www.plantoeat.com/recipes/export.csv
 * and store it on the server, then daily parse it for ingredients and store that in the DB
 * even if we have to do this manually it's ok
 *
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

$fileName = 'plantoeat-recipes-358334_12-22-2019.csv';
$rowLimit = 5000;

$file = fopen($fileName, 'r');

$i = 0;

while (($data = fgetcsv($file, 10000, ",")) && $i <= $rowLimit)
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

            if($title <> '')
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
                    $sqlInsertRecipe = "INSERT INTO avie_recipe (public_id, title, course, main_ingredient, url, website, prep_time
, cook_time, servings, yield, rating, public_url, photo, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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



            }


        }




    $i++;

}

fclose($file);



include 'footer.php';



?>