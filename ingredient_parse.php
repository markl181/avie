<?php
/*
Created by Mark Leci - 2019-12-22

*/

$pageTitle = 'Avie - Parse';

include 'header_log.php';

//Bootstrap4::menu($menu, basename(__FILE__));

//error_reporting(E_ALL);

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

$path = $_SERVER['SCRIPT_FILENAME'];
$info = pathinfo($path);
$path = $info['dirname'];
$archiveFolder = 'archive';

$filesArray = sortFilesByModified($path);

$rowLimit = 500000;

if(count($filesArray) == 0)
{
    echo "No files found<br/>";
    exec_time($start, __LINE__);

}
else
{

    //get active recipes
    $pdo->query($sqlGetActiveRecipes,['fetch'=>'all']);
    $databaseRecipes = $pdo->result;
    $activeRecipes = [];

    foreach($filesArray as $fileItem)
    {
        $file = fopen($fileItem['file'], 'r');

        $i = 0;

        while (($fileData = fgetcsv($file, 0, ",")) !==FALSE && $i <= $rowLimit)
        {
            // Read the data
            if($i == 0)
            {
                //display the headers
                display($fileData);
            }
            else
            {
                //exclude header row
                set_time_limit(30);
                $title = $fileData[0];
                $course = $fileData[1];
                $mainIngredient = $fileData[3];
                $url = $fileData[6];
                $website = $fileData[7];
                $prepTime = $fileData[8];
                $cookTime = $fileData[9];
                $servings = $fileData[11];
                $yield = $fileData[12];
                $ingredients = $fileData[13];
                $tags = $fileData[15];
                $rating = $fileData[16];
                $publicUrl = $fileData[17];
                $photo = $fileData[18];
                $updatedAt = $fileData[31];

                //initial processing
                $tags = explode(", ",$tags);
                $ingredients = explode("\n",$ingredients);
                $publicId = str_ireplace('https://www.plantoeat.com/recipes/','',$publicUrl);

                $pdo->logFind = true;

                if($title <> '' && is_numeric($publicId))
                {

                    $activeRecipes[] = $publicId;

                    //check recipe based on public url
                    $pdo->query($sqlGetRecipeByPublicId, ['binds'=>[$publicId], 'fetch'=>'one']);
                    $recipe = $pdo->result;


                    if($recipe)
                    {
                        //recipe exists, check the update date
                        $dbUpdatedAt = $recipe['updated_at'];
                        $dbUpdatedAtDt = new DateTime($dbUpdatedAt);
                        $updatedAtDt = new DateTime($updatedAt);


                        if($dbUpdatedAtDt < $updatedAtDt)
                        {

                            //process update
                            $pdo->query($sqlUpdateRecipe, ['binds'=>[$title, $course, $mainIngredient, $url
                                , $website, $prepTime, $cookTime, $servings, $yield, $rating === '' ? null : $rating
                                , $publicUrl, $photo
                                , $updatedAt,$publicId],'type'=>'update']);

                            //get database tags as array and compare
                            $pdo->query($sqlSelectAllRecipeTags, ['binds'=>[$publicId], 'fetch'=>'all']);
                            $dbFullTags = $pdo->result;

                            $dbFullTags = array_flatten($dbFullTags, 'name');

                            $missingTags = array_diff($dbFullTags, $tags);

                            if(count($missingTags) > 0)
                            {
                                //remove the tag from the database for the recipe



                                foreach($missingTags as $missingTag)
                                {
                                    echo "removed tag $missingTag from recipe $publicId<br/>";

                                    $pdo->query($sqlGetTagByName, ['binds'=>[$missingTag], 'fetch'=>'all']);

                                    $tagIdList = $pdo->result;

                                    foreach($tagIdList as $tagItem)
                                    {

                                        //remove the record
                                        $pdo->query($sqlRemoveTag, ['binds'=>[$tagItem['id'], $publicId]
                                            , 'type'=>'delete']);

                                    }


                                    unset($tagIdList);
                                }



                                unset($missingTags);
                            }



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
                                        //tag exists in recipe but not in file
                                        $pdo->query($sqlInsertRecipeTag, ['binds'=>[$publicId, $tagId], 'type'=>'insert']);
                                        echo "Recipe Tag Created for recipe $publicId<br/>";
                                    }



                                }
                            }

                            foreach ($ingredients as $ingredient)
                            {
                                if($ingredient <> '') {

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

                                        $pdo->query($sqlInsertRecipeIngredient, ['binds'=>[$publicId, $ingredientId]
                                            , 'type'=>'insert']);
                                        echo "Recipe Ingredient Created for recipe $publicId<br/>";
                                    }

                                    //@@todo do we want to remove any ingredients that were removed?



                                }

                            }


                        }
                        else
                        {
                            //skip the record. Make sure to look here if we are missing things like tag updates,
                            //since we're not sure if that updates the updated_at value
                            continue;

                        }


                    }
                    else
                    {

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

                                    $pdo->query($sqlInsertRecipeIngredient, ['binds'=>[$publicId, $ingredientId]
                                        , 'type'=>'insert']);
                                    echo "Recipe Ingredient Created for recipe $publicId<br/>";
                                }

                                //@@todo do we want to remove any ingredients that were removed?



                            }

                        }

                        //need to add recipe
                        $pdo->query($sqlInsertRecipe, ['binds'=>[$publicId, $title, $course, $mainIngredient, $url
                            , $website, $prepTime, $cookTime, $servings, $yield, $rating === '' ? null : $rating
                            , $publicUrl, $photo, $updatedAt]
                            , 'type'=>'insert']);

                        echo "Recipe $publicId Created<br/>";



                    }





                }


            }



            $i++;

        }

        fclose($file);

        //now archive the file
        if(rename($fileItem['file'],$archiveFolder."/".$fileItem['file']))
        {
            echo $fileItem['file']." archived <br/><br/>";
        }




    }

    //now compare the arrays of recipes

    //add array size protection
    if(count($activeRecipes) > 1000)
    {

        //@@todo change to array_diff with flatten

        foreach($databaseRecipes as $databaseRecipe)
        {

            if(!in_array($databaseRecipe['public_id'],$activeRecipes))
            {

                $pdo->query($sqlInactivateRecipe, ['binds'=>[$databaseRecipe['id']],'type'=>'update']);
                echo $databaseRecipe['public_id']." has been inactivated<br/>";

            }


        }


    }

    exec_time($start, __LINE__);


    $pdo->query($sqlInsertUpdate, ['binds'=>['1'], 'type'=>'insert']);


}


?>