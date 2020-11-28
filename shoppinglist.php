<?php
/*
Created by Mark Leci - 2020-09-07

*/

$pageTitle = 'Shopping List';
require 'header.php';

Bootstrap4::menu($menu, basename(__FILE__));

//get all the ingredients where the % full is < 10 for the smallest container
//and the total amount is < 50

$percentCutoff = 20;
$totalAmtCutoff = 50;

Bootstrap4::linebreak(2);
Bootstrap4::table(['Item','Category','Container','Qty','Size (g)','% Full','Total Amount (g)','Updated']);

$rowClass = 'clickable-row';

$pdo->query($sqlGetSpices, ['fetch'=>'all']);
$spicesList = $pdo->result;
$spiceName = '';
$spiceTotal = 0;
$spiceTotalPer = 0;
$spiceKey = '';
$count = 0;

foreach($spicesList as $key=>&$spiceRow)
{

    //troubleshoot($spiceRow);

    $spiceRow['total'] = round($spiceRow['size'] * ($spiceRow['amount']/100) * $spiceRow['quantity']);
    $spiceRow['totalper'] = $spiceRow['amount'];
    $spiceRow['count'] = $count;

    if($spiceRow['spice'] == $spiceName)
    {
        //increment
        $spicesList[$spiceKey]['total'] += $spiceRow['total'];
        $spiceRow['total'] += $spiceTotal;

        $spicesList[$spiceKey]['totalper'] += $spiceRow['totalper'];
        $spiceRow['totalper'] += $spiceTotalPer;

        $spicesList[$spiceKey]['count'] = $spiceRow['count'];
        $spiceRow['count']++;
    }

    $spiceName = $spiceRow['spice'];
    $spiceTotal = $spiceRow['total'];
    $spiceTotalPer = $spiceRow['totalper'];
    $count =  $spiceRow['count'];

    $spiceKey = $key;



}


foreach($spicesList as $spiceRowItem)
{

    //get last update
    $pdo->query($sqlGetLastPantryUpdate, ['binds'=>[$spiceRowItem['id']],'fetch'=>'one']);
    $update = $pdo->result['ts'];
    $update = get_date('Y-m-d',$update);

    $percent = new NumberFormatter('en_US', NumberFormatter::PERCENT);
    $spiceRowItem['amount'] = $percent->format($spiceRowItem['amount']/100);

    if(($spiceRowItem['total'] < $totalAmtCutoff && $spiceRowItem['amount'] < $percentCutoff)
        ||$spiceRowItem['total'] == 0||$spiceRowItem['quantity'] <= 1 && $spiceRowItem['category'] == 'Cans and Jars')
    {

        Bootstrap4::table_row([$spiceRowItem['spice'],$spiceRowItem['category'],$spiceRowItem['container']
            ,$spiceRowItem['quantity'],$spiceRowItem['size']
            ,$spiceRowItem['amount']
            ,$spiceRowItem['total'],$update]);

    }

    unset($update);

}


Bootstrap4::table_close();


include 'footer.php';

?>