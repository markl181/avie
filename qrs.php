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
$sqlInsertIngredient = "INSERT INTO avie_ingredient (name) VALUES (?)";

$sqlSelectRecipeTag = "SELECT * FROM avie_recipe_tag WHERE recipe_id = ? AND tag_id = ?";
$sqlInsertRecipeTag = "INSERT INTO avie_recipe_tag (recipe_id, tag_id) VALUES (?,?)";
$sqlSelectRecipeIngredient = "SELECT * FROM avie_recipe_ingredient WHERE recipe_id = ? AND ingredient_id = ?";
$sqlInsertRecipeIngredient = "INSERT INTO avie_recipe_ingredient (recipe_id, ingredient_id) VALUES (?,?)";

$sqlFilterRecipeByIngredient = "SELECT DISTINCT ar.* from avie_recipe ar
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_ingredient ai ON ai.id = ari.ingredient_id
WHERE ai.name LIKE ?
ORDER BY ar.course, ar.title
";

$sqlGetRecipeByPublicId = "SELECT id, updated_at FROM avie_recipe WHERE public_id = ?";
$sqlGetRecipes = "SELECT * from avie_recipe ORDER BY course, title";
$sqlInsertRecipe = "INSERT INTO avie_recipe (public_id, title, course, main_ingredient, url, website, prep_time
, cook_time, servings, yield, rating, public_url, photo, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

?>