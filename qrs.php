<?php
/*
Created by Mark Leci - 2019-05-11

*/
/**
 * Created by PhpStorm.
 * User: markleci
 * Date: 2019-05-11
 * Time: 4:29 PM
 */

/*
 * ALTER TABLE `x`
ADD `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 */


$sqlGetMeals = "SELECT id, ord, name FROM meal ORDER BY ord";
$sqlGetFoodGroups = "SELECT id, name, ord, notes FROM food_group ORDER BY ord";
$sqlGetFoods = "SELECT avie_food.id, avie_food.name name, avie_level.name level FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
ORDER BY avie_food.name";
$sqlGetLevels = "SELECT id, name FROM avie_level ORDER BY id";

$sqlInsertFood = "INSERT INTO avie_food (name, level_id) VALUES (?, ?)";
$sqlInsertTag = "INSERT INTO avie_tag (name) VALUES (?)";

$sqlGetRecipeByPublicId = "SELECT id, updated_at FROM avie_recipe WHERE public_id = ?";
$sqlGetRecipes = "SELECT * from avie_recipe ORDER BY title";
$sqlInsertRecipe = "INSERT INTO avie_recipe (public_id, title, course, main_ingredient, url, website, prep_time
, cook_time, servings, yield, rating, public_url, photo, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

?>