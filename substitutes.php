<?php
/*
Created by Mark Leci - 2019-08-30

*/


/*
Created by Mark Leci - 2019-01-03

*/
/**
 * Created by PhpStorm.
 * User: markleci
 * Date: 2019-01-03
 * Time: 4:53 PM
 */

$pageTitle = 'Cooking - Substitutes & Pairings';
require 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

if($_GET['id']) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    $pdo->query($sqlGetIngredientById, ['binds' => [$id], 'fetch' => 'one']);

    $dbRecord = $pdo->result;
    $dbId = $dbRecord['id'];
    $dbIngredient = $dbRecord['name'];


}


if(isset($_POST['submit'])) {

    //troubleshoot($_POST);

    $ingredient = filter_var($_POST['ingredient'], FILTER_SANITIZE_STRING);

    $ingredient = ucwords($ingredient);

    $pdo->query($sqlGetIngredientByName, ['binds' => [$ingredient], 'fetch' => 'one']);

    if($pdo->result) {
        //existing row
        $ingredientId = $pdo->result['id'];
    }
    else {
        $pdo->query($sqlInsertIngredient, ['binds' => [$ingredient], 'type' => 'insert']);

        $ingredientId = $pdo->rowNum;

        $bootstrap::error_block('New ingredient added', 'success');

    }

    if($_POST['pairing']) {
        //collect the pairings and sort by id
        $pairingId = filter_var($_POST['pairing'], FILTER_SANITIZE_NUMBER_INT);

        $pairings = [$ingredientId, $pairingId];

        sort($pairings);

        $pdo->query($sqlCheckPairing, ['binds' => $pairings, 'fetch' => 'one']);

        if($pdo->result) {

        }
        else {
            $pdo->query($sqlInsertPairing, ['binds' => $pairings, 'type' => 'insert']);

            $bootstrap::error_block('New pairing added', 'success');

        }

    }

    if($_POST['substitute']) {
        //collect the pairings and sort by id
        $subId = filter_var($_POST['substitute'], FILTER_SANITIZE_NUMBER_INT);;


        $substitutes = [$ingredientId, $subId];

        sort($substitutes);

        $pdo->query($sqlCheckSubstitutes, ['binds' => $substitutes, 'fetch' => 'one']);

        if($pdo->result) {

        }
        else {
            $pdo->query($sqlInsertSubstitute, ['binds' => $substitutes, 'type' => 'insert']);

            $bootstrap::error_block('New substitute added', 'success');

        }


    }


}


$bootstrap::linebreak(2);

$pdo->query($sqlSelectIngredient);
$ingredientList = array();

foreach($pdo->result as $key => $ingredient) {
    $ingredientList[$ingredient['id']] = $ingredient['name'];


}

$form->open();
$pdo->datalist('ingredient', 'Ingredient:', $dbIngredient, $sqlSelectIngredient, 'name');
$form->select('pairing', 'Pairing:', $ingredientList);
$form->select('substitute', 'Substitute:', $ingredientList);
$bootstrap::clearfix();
$bootstrap::linebreak(2);
$form->submit();
$bootstrap::clearfix();
echo "<br/><a role='button' href='index.php' class='btn btn-primary' name='clear'>Clear</a>";
$form->close();


$pdo->query($sqlIngredientReport);
$reportData = $pdo->result;

$rowClass = 'text-left clickable-row';

$bootstrap::table(['Ingredient', 'Pairings', 'Substitutes']);

foreach($reportData as $row) {

    $rowId = $row['id'];
    $url = $_SERVER['PHP_SELF'] . "?id=" . $rowId;

    $pdo->query($sqlGetPairingById, ['binds' => [$rowId, $rowId], 'fetch' => 'one']);

    if($pdo->result) {
        $pairingText = $pdo->result['pairings'];

    }
    else {
        $pairingText = '';
    }

    $pdo->query($sqlGetSubstituteById, ['binds' => [$rowId, $rowId], 'fetch' => 'one']);

    if($pdo->result) {
        $substituteText = $pdo->result['substitutes'];

    }
    else {
        $substituteText = '';
    }

    if($pairingText != '' || $substituteText != '') {
        $bootstrap::table_row([$row['name'], $pairingText, $substituteText], ['class' => $rowClass, 'data-href' => $url]);
    }


}


$bootstrap::table_close();


include 'footer.php';


?>