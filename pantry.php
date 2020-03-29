<?php
/*
Created by Mark Leci - 2019-12-24

*/

$pageTitle = 'Cooking - Pantry';
require 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

/*
 * Estimate how much is left by adding up all the amounts and percentages
 * Display in order of which things are almost out along with a colour code
 *
 * Add a row to the spice jar when updated and inactivate the previous
 * row, so we can track usage over time
 *
 */

//error_reporting(E_ALL);
$cutoff = 30;



if(isset($_POST['submit']))
{

    //$date = preg_replace("([^0-9-])", "", $_POST['date']);
    $spice = filter_var($_POST['spice'], FILTER_SANITIZE_STRING);
    $spice = ucwords($spice);
    $jar = filter_var($_POST['jar'], FILTER_SANITIZE_NUMBER_INT
    );
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_INT
    );
    $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_INT
    );

    //check if the spice already exists and insert it if not
    $pdo->idColumn = 'id';
    $pdo->searchColumn = 'name';
    $pdo->searchTable = 'spice';
    $pdo->searchValue = $spice;
    $pdo->insertQuery = $sqlInsertSpice;
    $pdo->insertBinds = [$spice];

    $pdo->find_id();
    $spiceId = $pdo->recordId;

    //check if the spice jar exists and insert it if not
    $pdo->query($sqlGetSpiceJar, ['binds'=>[$spiceId, $jar], 'fetch'=>'one']);
    $spiceJar = $pdo->result;

    if(!$spiceJar)
    {
    $pdo->query($sqlInsertSpiceJar, ['binds'=>[$spiceId, $jar, $amount, $size], 'type'=>'insert']);

    }




}

$pdo->query($sqlGetJars, ['fetch'=>'all']);
$jarsList = $pdo->result;

$form->open();
$form->input('spice','Spice:',['type'=>'text','required'=>'required','autofocus'=>'autofocus']);
//$form->input('container','Container:',['type'=>'text','required'=>'required','autofocus'=>'autofocus']);

$form->selectquery('jar','Container:',$jarsList, 'name','id', $jar);
$form->input('size','Size:',['type'=>'number','required'=>'required','autofocus'=>'autofocus']);
//$form->datalistquery('jar2','C2',$jarsList, 'name','id',$jar);
$form->input('amount','% Full:',['type'=>'number','min'=>0,'max'=>100]);

$form->submit();
$form->close();

Bootstrap4::linebreak(2);
Bootstrap4::table(['Spice','Container','Size','% Full','Total Amount','Updated']);

$pdo->query($sqlGetSpices, ['fetch'=>'all']);
$spicesList = $pdo->result;
$spiceName = '';
$spiceTotal = 0;
$spiceKey = '';

foreach($spicesList as $key=>&$spiceRow)
    {

        $spiceRow['total'] = round($spiceRow['size'] * ($spiceRow['amount']/100));

        if($spiceRow['spice'] == $spiceName)
        {
            $spicesList[$spiceKey]['total'] += $spiceRow['total'];
            $spiceRow['total'] += $spiceTotal;

        }

        $spiceName = $spiceRow['spice'];
        $spiceTotal = $spiceRow['total'];
        $spiceKey = $key;
    }

usort($spicesList, "sizesort");

foreach($spicesList as $spiceRow)
{


    if($spiceRow['amount'] < $cutoff)
    {
        $spiceRow['amount'] = $spiceRow['amount']."|".$highlightRed;
    }

    //get last update
    $sqlGetLastPantryUpdate = "SELECT MAX(timestamp) as ts FROM spice_jar WHERE spice_id = ? ";
    $pdo->query($sqlGetLastPantryUpdate, ['binds'=>[$spiceRow['id']],'fetch'=>'one']);
    $update = $pdo->result['ts'];
    $update = get_date('Y-m-d',$update);


    Bootstrap4::table_row([$spiceRow['spice'],$spiceRow['container'],$spiceRow['size'],$spiceRow['amount']
      ,$spiceRow['total'],$update]);

    unset($update);

}



Bootstrap4::table_close();


echo date_default_timezone_get();

include 'footer.php';


?>