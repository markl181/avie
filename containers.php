<?php
/*
Created by Mark Leci - 2019-12-29

*/

$pageTitle = 'Cooking - Containers';
require 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

if(isset($_POST['submit']))
{

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $container = filter_var($_POST['container'], FILTER_SANITIZE_STRING);
    $container = ucwords($container);

    //check if the jar already exists and insert it if not
    $pdo->idColumn = 'id';
    $pdo->searchColumn = 'name';
    $pdo->searchTable = 'jar';
    $pdo->searchValue = $container;
    $pdo->insertQuery = $sqlInsertJar;
    $pdo->insertBinds = [$container];

    $pdo->find_id();
    $containerId = $pdo->recordId;

  }

$pdo->query($sqlGetJars, ['fetch'=>'all']);
$jarsList = $pdo->result;

$form->open();
$form->input('container','Container:',['type'=>'text','required'=>'required','autofocus'=>'autofocus']);
$form->submit();
$form->close();

Bootstrap4::linebreak(2);
Bootstrap4::table(['Container']);

foreach($jarsList as $jar)
{

    Bootstrap4::table_row([$jar['name']]);


}


Bootstrap4::table_close();



include 'footer.php';

?>