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
$sqlGetFoods = "SELECT avie_food.id id, avie_food.name name, avie_level.name level FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
ORDER BY level_id, avie_food.name";

$sqlGetFoodsForUpdate = "SELECT avie_food.id id, avie_food.name name, avie_level.name level 
FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
WHERE avie_food.TIMESTAMP > 
(SELECT max(TIMESTAMP) 
FROM avie_update 
WHERE TYPE = 2
)
ORDER BY 
level_id,        
avie_food.name";

$sqlGetFoodsForSearch = "SELECT avie_food.id id, avie_food.name name, avie_level.name level FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
WHERE avie_level.name <> 'Black'
ORDER BY avie_food.name";

$sqlGetFoodById = "SELECT avie_food.id, avie_food.name name, avie_level.id level FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
WHERE avie_food.id = ?
ORDER BY avie_food.name";
$sqlGetLevels = "SELECT al.id, al.name, count(af.id) foods FROM avie_level al
LEFT OUTER JOIN avie_food af ON af.level_id = al.id
GROUP BY al.id, al.name
 ORDER BY id";

$sqlGetCourses = "SELECT DISTINCT course FROM avie_recipe ORDER BY 1";

$sqlInsertFood = "INSERT INTO avie_food (name, level_id) VALUES (?, ?)";
$sqlInsertTag = "INSERT INTO avie_tag (name) VALUES (?)";
$sqlInsertIngredient = "INSERT INTO avie_ingredient (name) VALUES (?)";

$sqlSelectRecipeTag = "SELECT * FROM avie_recipe_tag WHERE recipe_id = ? AND tag_id = ?";
$sqlInsertRecipeTag = "INSERT INTO avie_recipe_tag (recipe_id, tag_id) VALUES (?,?)";
$sqlSelectRecipeIngredient = "SELECT * FROM avie_recipe_ingredient WHERE recipe_id = ? AND ingredient_id = ?";
$sqlInsertRecipeIngredient = "INSERT INTO avie_recipe_ingredient (recipe_id, ingredient_id) VALUES (?,?)";

$sqlFilterRecipeByIngredient = "SELECT DISTINCT ar.* 
, arr.id request_id
, ifnull(blackct,0) blackct
, ifnull(redct,0) redct
, ifnull(greenct,0) greenct
, GROUP_CONCAT(att.name)
from avie_recipe ar
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_recipe_request arr ON arr.recipe_id = ar.public_id AND arr.active = 1
LEFT OUTER JOIN avie_recipe_tag art ON art.recipe_id = ar.public_id
LEFT OUTER JOIN avie_tag att ON att.id = art.tag_id       
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) blackct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Black'
GROUP BY recipe_id
) blackj ON blackj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) redct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Red'
GROUP BY recipe_id
) redj ON redj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) greenct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Green'
GROUP BY recipe_id
) greenj ON greenj.recipe_id = ar.public_id 
WHERE afi.food_id = ?
ORDER BY ar.course, ar.title
";

$sqlGetRecipeByPublicId = "SELECT id, updated_at, title FROM avie_recipe WHERE public_id = ?";
$sqlGetRecipes = "SELECT * from avie_recipe ORDER BY course, title";
$sqlGetRecipeCount = "SELECT count(1) AS ct from avie_recipe";
$sqlGetRecipeCourseCount = "SELECT count(1) AS ct from avie_recipe WHERE course = ?";
$sqlGetRecipeTags = "SELECT 
 GROUP_CONCAT(att.name) tags
from avie_recipe_tag art 
INNER JOIN avie_tag att ON att.id = art.tag_id   
WHERE art.recipe_id = ?    
 ";
$sqlGetRecipesWithDetails = "SELECT * from avie_recipe ar 
LEFT OUTER JOIN avie_recipe_tag art ON art.recipe_id = ar.public_id
LEFT OUTER JOIN avie_tag att ON att.id = art.tag_id
LEFT OUTER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
ORDER BY course, title";

$sqlGetRecipesByInclude = "
SELECT DISTINCT ar.*
, ifnull(blackct,0) blackct
, ifnull(redct,0) redct
, ifnull(greenct,0) greenct
, arr.id request_id
FROM avie_recipe ar 
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_recipe_tag art ON art.recipe_id = ar.public_id
LEFT OUTER JOIN avie_tag att ON att.id = art.tag_id
LEFT OUTER JOIN avie_recipe_request arr ON arr.recipe_id = ar.public_id AND arr.active = 1
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) blackct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Black'
GROUP BY recipe_id
) blackj ON blackj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) redct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Red'
GROUP BY recipe_id
) redj ON redj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) greenct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Green'
GROUP BY recipe_id
) greenj ON greenj.recipe_id = ar.public_id 
WHERE 1=1";

$sqlGetRecipesWithRating = "
SELECT DISTINCT ar.*
, ifnull(blackct,0) blackct
, ifnull(redct,0) redct
, ifnull(greenct,0) greenct
, arr.id request_id
FROM avie_recipe ar 
LEFT OUTER JOIN avie_recipe_tag art ON art.recipe_id = ar.public_id
LEFT OUTER JOIN avie_tag att ON att.id = art.tag_id
LEFT OUTER JOIN avie_recipe_request arr ON arr.recipe_id = ar.public_id AND arr.active = 1
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) blackct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Black'
GROUP BY recipe_id
) blackj ON blackj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) redct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Red'
GROUP BY recipe_id
) redj ON redj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) greenct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Green'
GROUP BY recipe_id
) greenj ON greenj.recipe_id = ar.public_id 
WHERE rating = 5 ORDER BY rating DESC, course, title
LIMIT 100
";

$sqlGetAllRecipes = "
SELECT DISTINCT ar.*
, ifnull(blackct,0) blackct
, ifnull(redct,0) redct
, ifnull(greenct,0) greenct
, arr.id request_id
FROM avie_recipe ar 
LEFT OUTER JOIN avie_recipe_tag art ON art.recipe_id = ar.public_id
LEFT OUTER JOIN avie_tag att ON att.id = art.tag_id
LEFT OUTER JOIN avie_recipe_request arr ON arr.recipe_id = ar.public_id AND arr.active = 1
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) blackct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Black'
GROUP BY recipe_id
) blackj ON blackj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) redct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Red'
GROUP BY recipe_id
) redj ON redj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) greenct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Green'
GROUP BY recipe_id
) greenj ON greenj.recipe_id = ar.public_id 
WHERE ifnull(blackct,0) = 0  ORDER BY course, title
";

$sqlInsertRecipe = "INSERT INTO avie_recipe (public_id, title, course, main_ingredient, url, website, prep_time
, cook_time, servings, yield, rating, public_url, photo, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$sqlGetRecipeIngredient = "SELECT DISTINCT ai.id, ai.name from avie_recipe ar
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_ingredient ai ON ai.id = ari.ingredient_id
WHERE ai.name LIKE ? AND ai.timestamp >= ?
ORDER BY ar.course, ar.title
";

$sqlGetRecipeIngredientNoTime = "SELECT DISTINCT ai.id, ai.name from avie_recipe ar
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_ingredient ai ON ai.id = ari.ingredient_id
WHERE ai.name LIKE ?
ORDER BY ar.course, ar.title
";


$sqlGetRecipeIngredientTime = "SELECT DISTINCT ai.id, ai.name from avie_recipe ar
INNER JOIN avie_recipe_ingredient ari ON ari.recipe_id = ar.public_id
INNER JOIN avie_ingredient ai ON ai.id = ari.ingredient_id
WHERE ai.name LIKE ?
AND ari.timestamp > 
(
SELECT
MAX(TIMESTAMP)
FROM avie_update
WHERE TYPE = 2
)
ORDER BY ar.course, ar.title
";




$sqlGetFoodIngredient = "SELECT id FROM avie_food_ingredient WHERE food_id = ? AND ingredient_id = ?";
$sqlInsertFoodIngredient = "INSERT INTO avie_food_ingredient (food_id, ingredient_id) VALUES (?,?)";

$sqlGetUnmatchedIngredients = "SELECT
name, COUNT(1) ct
FROM avie_ingredient ai
INNER JOIN avie_recipe_ingredient ari ON ari.ingredient_id = ai.id
WHERE ai.id NOT IN
(SELECT ingredient_id FROM avie_food_ingredient)
GROUP BY name
ORDER BY 2 DESC,1";

$sqlFinishOneInclude = " AND
afi.food_id = ?
ORDER BY ar.course, ar.title";

$sqlInsertUpdate = "INSERT INTO avie_update (type) VALUES (?)";

$sqlGetRequests = "SELECT arr.id, arr.active, arr.date, ar.title, ar.course 
, redct, greenct
, public_url
FROM avie_recipe_request arr 
INNER JOIN avie_recipe ar ON ar.public_id = arr.recipe_id
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) redct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Red'
GROUP BY recipe_id
) redj ON redj.recipe_id = ar.public_id 
LEFT OUTER JOIN
(
SELECT recipe_id, COUNT(DISTINCT food_id) greenct
FROM 
avie_recipe_ingredient ari
LEFT OUTER JOIN avie_food_ingredient afi ON afi.ingredient_id = ari.ingredient_id
LEFT OUTER JOIN avie_food af ON af.id = afi.food_id
LEFT OUTER JOIN avie_level al ON al.id = af.level_id
WHERE al.name = 'Green'
GROUP BY recipe_id
) greenj ON greenj.recipe_id = ar.public_id 

WHERE active = 1";

$sqlUpdateRequestDate = "UPDATE avie_recipe_request SET date = ? WHERE id = ?";
$sqlUpdateRequestStatus = "UPDATE avie_recipe_request SET active = 0 WHERE id = ?";

?>