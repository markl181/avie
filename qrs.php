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
$sqlGetFoods = "SELECT avie_food.id id, avie_food.name name, avie_level.name level 
, maxuse, oligos, fructose, polyols, lactose 
FROM avie_food 
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

$sqlGetFoodById = "SELECT avie_food.id, avie_food.name name, avie_level.id level, maxuse, oligos, fructose, polyols, lactose 
FROM avie_food 
LEFT OUTER JOIN avie_level ON avie_food.level_id = avie_level.id
WHERE avie_food.id = ?
ORDER BY avie_food.name";
$sqlGetLevels = "SELECT al.id, al.name, count(af.id) foods FROM avie_level al
LEFT OUTER JOIN avie_food af ON af.level_id = al.id
GROUP BY al.id, al.name
 ORDER BY id";

$sqlGetCourses = "SELECT DISTINCT course FROM avie_recipe WHERE isdeleted = 0 ORDER BY 1";

$sqlInsertFood = "INSERT INTO avie_food (name, level_id) VALUES (?, ?)";
$sqlInsertTag = "INSERT INTO avie_tag (name) VALUES (?)";
$sqlInsertIngredient = "INSERT INTO avie_ingredient (name) VALUES (?)";

$sqlSelectRecipeTag = "SELECT * FROM avie_recipe_tag WHERE recipe_id = ? AND tag_id = ? AND isdeleted = 0";

$sqlSelectAllRecipeTags = "SELECT name FROM avie_recipe_tag art INNER JOIN avie_tag att ON art.tag_id = att.id 
WHERE recipe_id = ? AND isdeleted = 0";

$sqlSelectAllRecipeIngredients = "SELECT
NAME
FROM avie_recipe_ingredient ari 
INNER JOIN avie_ingredient ai ON ai.id = ari.ingredient_id
WHERE recipe_id = ? 
AND ari.isdeleted = 0";

$sqlInsertRecipeTag = "INSERT INTO avie_recipe_tag (recipe_id, tag_id) VALUES (?,?)";
$sqlSelectRecipeIngredient = "SELECT * FROM avie_recipe_ingredient WHERE recipe_id = ? AND ingredient_id = ?";
$sqlInsertRecipeIngredient = "INSERT INTO avie_recipe_ingredient (recipe_id, ingredient_id) VALUES (?,?)";

$sqlFilterRecipeByIngredient = "SELECT DISTINCT ar.* 
, arr.id request_id
, ifnull(blackct,0) blackct
, ifnull(redct,0) redct
, ifnull(greenct,0) greenct
, GROUP_CONCAT(att.name)
, ar.dateadded
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
AND ar.isdeleted = 0
AND art.isdeleted = 0
ORDER BY ar.course, ar.title
";

$sqlGetRecipeByPublicId = "SELECT id, updated_at, title FROM avie_recipe WHERE public_id = ? AND isdeleted = 0";
$sqlGetRecipeByRequestId = "SELECT ar.id, updated_at, title FROM avie_recipe ar
INNER JOIN avie_recipe_request arr ON arr.recipe_id = ar.public_id
 WHERE arr.id = ?";
$sqlGetRecipes = "SELECT * from avie_recipe WHERE isdeleted = 0 ORDER BY course, title ";
$sqlGetRecipeCount = "SELECT count(1) AS ct from avie_recipe";
$sqlGetRecipeCourseCount = "SELECT count(1) AS ct from avie_recipe WHERE course = ? AND isdeleted = 0";
$sqlGetRecipeTags = "SELECT 
 GROUP_CONCAT(att.name) tags
from avie_recipe_tag art 
INNER JOIN avie_tag att ON att.id = art.tag_id   
WHERE art.recipe_id = ?  
AND art.isdeleted = 0
 ";
$sqlGetTagByName = "SELECT id FROM avie_tag WHERE name like ?";
$sqlRemoveTag = "UPDATE avie_recipe_tag SET isdeleted = 1 WHERE tag_id = ? AND recipe_id = ?";
$sqlRemoveIngredient = "UPDATE avie_recipe_ingredient SET isdeleted = 1 WHERE ingredient_id = ? AND recipe_id = ?";

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
WHERE ar.isdeleted = 0
";

$sqlGetRecipesDefault = "
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
WHERE 
(rating = 5 OR rating = 0 OR att.name = 'Avie-Approved') 
  AND
(course = '' OR course = 'Desserts and Treats' OR course = 'Main Course')    
AND ar.isdeleted = 0
AND 
(
datediff(now(),ar.dateadded) < ?
OR
datediff(now(),ar.updated_at) < ?
)
";

$sqlGetActiveRecipes = "SELECT id, public_id FROM avie_recipe WHERE isdeleted = 0";
$sqlInactivateRecipe = "UPDATE avie_recipe SET isdeleted = 1 WHERE id = ?";

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
WHERE ifnull(blackct,0) = 0  
AND ar.isdeleted = 0
ORDER BY course, title
";

$sqlInsertRecipe = "INSERT INTO avie_recipe (public_id, title, course, main_ingredient, url, website, prep_time
, cook_time, servings, yield, rating, public_url, photo, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$sqlUpdateRecipe = "UPDATE avie_recipe SET title=?, course=?, main_ingredient=?, url=?, website=?, prep_time=?
, cook_time=?, servings=?, yield=?, rating=?, public_url=?, photo=?, updated_at=?
WHERE public_id = ?
";


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

$sqlGetRecentlyPlanned = "SELECT recipe_id FROM avie_recipe_request
WHERE datediff(now(),DATE) < ?";


$sqlInsertUpdate = "INSERT INTO avie_update (type) VALUES (?)";

$sqlGetRequests = "SELECT arr.id, arr.active, arr.date, ar.title, ar.course 
, redct, greenct
, public_url
, priority
, xrequested
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
LEFT OUTER JOIN 
(
SELECT recipe_id, count(distinct arr.id) xrequested
FROM avie_recipe_request arr 
INNER JOIN avie_recipe ar ON ar.public_id = arr.recipe_id
WHERE 
((arr.date <> '0000-00-00' AND active = 0)
OR
arr.active = 1)
GROUP BY recipe_id
) rcount ON rcount.recipe_id = ar.public_id
WHERE active = 1
  AND ar.isdeleted = 0

ORDER BY 5, 4
";

$sqlUpdateRequestDate = "UPDATE avie_recipe_request SET date = ? WHERE id = ?";
$sqlUpdateRequestStatus = "UPDATE avie_recipe_request SET active = 0 WHERE id = ?";
$sqlUpdateRequestPriority = "UPDATE avie_recipe_request SET priority = ? WHERE id = ?";
$sqlUpdateRequestPriorityBulk = "UPDATE avie_recipe_request SET priority = 0 WHERE active = 1";


/*Pantry*/

$sqlInsertSeasonalIngredient = "INSERT INTO ingredients (name) VALUES (?)";
$sqlSelectIngredient = "SELECT id, name FROM ingredients ORDER BY name";
$sqlSelectSeason = "SELECT id, name FROM season ORDER BY month";
$sqlIngredientReport = "SELECT i.id, i.name FROM ingredients i ORDER BY i.name";
$sqlSeasonReport = "SELECT i.id, i.name, s1.name start, s2.name end FROM ingredients i 
INNER JOIN ingredient_season iis ON iis.ingredient_id = i.id
LEFT OUTER JOIN season s1 ON s1.id = iis.season_start_id
LEFT OUTER JOIN season s2 ON s2.id = iis.season_end_id
ORDER BY i.name";
$sqlGetIngredientById = "SELECT i.id, i.name FROM ingredients i WHERE i.id = ?";
$sqlGetIngredientByName = "SELECT i.id, i.name FROM ingredients i WHERE lower(i.name) = lower(?)";
$sqlCheckPairing = "SELECT id FROM ingredient_pair WHERE ingredient_id = ? AND ingredient_id2 = ?";
$sqlCheckSubstitutes = "SELECT id FROM ingredient_substitute WHERE ingredient_id = ? AND ingredient_id2 = ?";
$sqlInsertPairing = "INSERT INTO ingredient_pair (ingredient_id, ingredient_id2) VALUES (?,?)";
$sqlInsertSubstitute = "INSERT INTO ingredient_substitute (ingredient_id, ingredient_id2) VALUES (?,?)";
$sqlGetPairingById = "SELECT 
GROUP_CONCAT(DISTINCT il.name ORDER BY il.name ASC SEPARATOR ', ') pairings
FROM
(
SELECT
i.name
FROM ingredient_pair ip
INNER JOIN ingredients i ON i.id = ip.ingredient_id2
WHERE ingredient_id = ?
UNION
SELECT
i.name
FROM ingredient_pair ip
INNER JOIN ingredients i ON i.id = ip.ingredient_id
WHERE ingredient_id2 = ?) il";
$sqlGetSubstituteById = "SELECT 
GROUP_CONCAT(DISTINCT il.name ORDER BY il.name ASC SEPARATOR ', ') substitutes
FROM
(
SELECT
i.name
FROM ingredient_substitute ip
INNER JOIN ingredients i ON i.id = ip.ingredient_id2
WHERE ingredient_id = ?
UNION
SELECT
i.name
FROM ingredient_substitute ip
INNER JOIN ingredients i ON i.id = ip.ingredient_id
WHERE ingredient_id2 = ?) il";

$sqlInsertSeason = "INSERT INTO ingredient_season (ingredient_id, season_start_id, season_end_id) 
            VALUES (?,?,?)";

$sqlCheckSeasons = "SELECT id, season_start_id, season_end_id FROM ingredient_season WHERE ingredient_id = ?";

$sqlGetAllSeasons = "SELECT i.id, i.name, s1.month month1, s2.month month2 FROM ingredient_season iis
INNER JOIN ingredients i ON i.id = iis.ingredient_id
LEFT OUTER JOIN season s1 ON s1.id = iis.season_start_id
LEFT OUTER JOIN season s2 ON s2.id = iis.season_end_id
ORDER BY i.name
";



$sqlGetJars = "SELECT id, name FROM jar ORDER BY name";
$sqlGetCategories = "SELECT id, name FROM category ORDER BY name";
$sqlGetSpices = "SELECT spice.id, spice_jar.id record_id, spice.name spice, jar.name container
     , spice_jar.percentage amount, spice_jar.size, ct.name category, quantity
FROM spice 
left outer join spice_jar on spice.id = spice_jar.spice_id
left outer join jar on jar.id = spice_jar.jar_id
left outer join category ct on ct.id = spice_jar.category_id
ORDER BY ct.name, spice.name";
$sqlInsertSpice = "INSERT INTO spice (name) VALUES (?)";
$sqlInsertCategory = "INSERT INTO category (name) VALUES (?)";
$sqlInsertJar = "INSERT INTO jar (name) VALUES (?)";
$sqlGetSpiceJar = "SELECT id FROM spice_jar WHERE spice_id = ? AND jar_id = ?";
$sqlInsertSpiceJar = "INSERT INTO spice_jar (spice_id, jar_id, category_id, percentage, size, quantity) 
VALUES (?,?,?,?,?,?)";
$sqlInsertSpiceJarHistory = "INSERT INTO spice_jar_history (spice_jar_id, percentage) VALUES (?,?)";

$sqlGetLastPantryUpdate = "SELECT MAX(timestamp) as ts FROM spice_jar WHERE spice_id = ? ";
$sqlGetSpiceJarById = "SELECT spice.id, spice_jar.id record_id, spice.name spice, jar.id container
     , spice_jar.percentage amount, spice_jar.size, category_id, ct.name category, quantity FROM spice 
LEFT OUTER JOIN spice_jar ON spice.id = spice_jar.spice_id
LEFT OUTER JOIN jar ON jar.id = spice_jar.jar_id
left outer join category ct on ct.id = spice_jar.category_id
WHERE spice_jar.id = ?";
$sqlUpdateSpiceJar = "UPDATE spice_jar SET size=?, percentage=?, category_id=?, quantity=? WHERE id = ?";
$sqlUpdateSpice = "UPDATE spice SET name = ? Where id = ?";



?>