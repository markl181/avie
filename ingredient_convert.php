<?php
/*
Created by Mark Leci - 2019-12-26

*/

$pageTitle = 'Avie - Convert';

include 'header_log.php';

//Bootstrap4::menu($menu, basename(__FILE__));

/*
 * Really only need to check if the ingredients or foods have been modified since last check...
 * If a food changes and matches different ingredients, that's going to cause problems!
 */

$start = microtime(true);

$pdo->query($sqlGetFoodsForUpdate);
$foodList = $pdo->result;

//run list of all updated foods

echo "Running list of updated foods<br/>";

foreach($foodList as $food)
{

    if($food['name'] <> '')

    {

        //set_time_limit(30);
        $pattern = '/\(.*$/';
        $foodSearch = preg_replace($pattern,'',$food['name']);
        $foodSearch = trim($foodSearch);
        $originalFood = $foodSearch;
        $foodSearch = "%$foodSearch%";

        //create a food->ingredient translation
        $pdo->query($sqlGetRecipeIngredientNoTime, ['binds'=>[$foodSearch]]);
        $ingredientList = $pdo->result;
        $insertCount = 0;

        foreach($ingredientList as $ingredient)
        {

            set_time_limit(30);

            $pdo->query($sqlGetFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'fetch'=>'one']);

            if(!$pdo->result)
            {

                $pdo->query($sqlInsertFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'type'=>'insert']);
                $insertCount++;

            }

        }

        echo "$originalFood search completed - $insertCount new ingredients linked<br/>";


    }


}

$pdo->query($sqlGetFoods);
$foodList = $pdo->result;

//update all foods where the ingredient has changed

echo "Running list of updated ingredients<br/>";

    foreach($foodList as $food)
    {

        //set_time_limit(30);
        $pattern = '/\(.*$/';
        $foodSearch = preg_replace($pattern,'',$food['name']);
        $foodSearch = trim($foodSearch);
        $originalFood = $foodSearch;
        $foodSearch = "%$foodSearch%";

        //create a food->ingredient translation
        $pdo->query($sqlGetRecipeIngredientTime, ['binds'=>[$foodSearch]]);
        $ingredientList = $pdo->result;
        $insertCount = 0;



        foreach($ingredientList as $ingredient)
        {

            set_time_limit(30);

            $pdo->query($sqlGetFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'fetch'=>'one']);

            if(!$pdo->result)
            {

                $pdo->query($sqlInsertFoodIngredient, ['binds'=>[$food['id'],$ingredient['id']],'type'=>'insert']);
                $insertCount++;

            }

        }

        if($insertCount > 0)
        {
            echo "$originalFood search completed - $insertCount new ingredients linked<br/>";
        }



    }

exec_time($start, __LINE__);

$pdo->query($sqlInsertUpdate, ['binds'=>[2], ['type'=>'insert']]);



//include 'footer.php';

?>