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

    //troubleshoot($_POST);

    $spice = filter_var($_POST['spice'], FILTER_SANITIZE_STRING);
    $spice = ucwords($spice);
    $container = filter_var($_POST['jar'], FILTER_SANITIZE_NUMBER_INT
    );
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_INT
    );
    $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_INT
    );
    $formId = filter_var($_POST['spicejar_id'], FILTER_SANITIZE_NUMBER_INT
    );
    $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);


    if($category)
    {

        //check if category exists
        $pdo->idColumn = 'id';
        $pdo->searchColumn = 'name';
        $pdo->searchTable = 'category';
        $pdo->searchValue = $category;
        $pdo->insertQuery = $sqlInsertCategory;
        $pdo->insertBinds = [$category];

        $pdo->find_id();
        $categoryId = $pdo->recordId;

        //troubleshoot($categoryId);

    }

    if(!$formId)
    {

        //check if the spice already exists and insert it if not
        $pdo->idColumn = 'id';
        $pdo->searchColumn = 'name';
        $pdo->searchTable = 'spice';
        $pdo->searchValue = $spice;
        $pdo->insertQuery = $sqlInsertSpice;
        $pdo->insertBinds = [$spice];

        $pdo->find_id();
        $itemId = $pdo->recordId;

        //check if the spice jar exists and insert it if not
        $pdo->query($sqlGetSpiceJar, ['binds'=>[$itemId, $container], 'fetch'=>'one']);
        $spiceJar = $pdo->result;

        if(!$spiceJar)
        {
            $pdo->query($sqlInsertSpiceJar, ['binds'=>[$itemId, $container, $categoryId, $amount, $size, $quantity]
                , 'type'=>'insert']);

        }

    }
    else
    {
        //update existing record

        $pdo->query($sqlGetSpiceJarById, ['binds'=>[$formId],'fetch'=>'one']);
        $dbRow = $pdo->result;

        $dbSpice = $dbRow['spice'];
        $dbContainer = $dbRow['container'];
        $dbCategory = $dbRow['category_id'];
        $dbAmount = $dbRow['amount'];
        $dbSize = $dbRow['size'];
        $dbQuantity = $dbRow['quantity'];

        if($dbAmount <> $amount || $dbSize <> $size || $dbSpice <> $spice || $categoryId <> $dbCategory
            || $dbQuantity <> $quantity)
        {

            
            $sqlUpdateSpiceJar = "UPDATE spice_jar SET size=?, percentage=?, category_id=?, quantity=? WHERE id = ?";

            $pdo->query($sqlUpdateSpiceJar, ['binds'=>[$size, $amount,$categoryId, $quantity, $formId], 'type'=>'update']);

            //@@todo update name if needed

        }

    }






}

if(isset($_GET['id'])) {

    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT
    );

    unset($dbRow);
    $pdo->query($sqlGetSpiceJarById, ['binds'=>[$id], 'fetch'=>'one']);
    $dbRow = $pdo->result;

    $dbSpice = $dbRow['spice'];
    $container = $dbRow['container'];
    $dbCategory = $dbRow['category_id'];
    $dbAmount = $dbRow['amount'];
    $dbSize = $dbRow['size'];
    $category = $dbRow['category'];
    $dbQuantity = $dbRow['quantity'];


}

$pdo->query($sqlGetJars, ['fetch'=>'all']);
$jarsList = $pdo->result;

$pdo->query($sqlGetCategories, ['fetch'=>'all']);
$catsList = $pdo->result;

//set default form values
if($dbAmount == '')
{
    $dbAmount = 100;
}
if($dbQuantity == '' || $dbQuantity == 0)
{
    $dbQuantity = 1;

}

$form->open();
$form->input('spice','Item:',['type'=>'text','required'=>'required','autofocus'=>'autofocus'
    ,'value'=>$dbSpice,'autocomplete'=>'autocomplete']);
//$form->input('container','Container:',['type'=>'text','required'=>'required','autofocus'=>'autofocus']);
$form->datalist_query('category','Category:',$catsList, 'name','id',$category);
$form->selectquery('jar','Container:',$jarsList, 'name','id', $container);
$form->input('size','Size:',['type'=>'number','required'=>'required','min'=>0,'value'=>$dbSize]);
$form->input('quantity','Quantity:',['type'=>'number','min'=>0,'required'=>'required'
    ,'value'=>$dbQuantity]);
//$form->datalistquery('jar2','C2',$jarsList, 'name','id',$jar);
$form->input('amount','% Full:',['type'=>'number','min'=>0,'max'=>100,'value'=>$dbAmount]);
$form->hidden('spicejar_id', $id);


Bootstrap4::linebreak(2);
Bootstrap4::table(['Item','Category','Container','Qty','Size (g)','% Full','Total Amount (g)','Updated']);

$rowClass = 'clickable-row';

$pdo->query($sqlGetSpices, ['fetch'=>'all']);
$spicesList = $pdo->result;
$spiceName = '';
$spiceTotal = 0;
$spiceKey = '';



foreach($spicesList as $key=>&$spiceRow)
    {

        //troubleshoot($spiceRow);

        $spiceRow['total'] = round($spiceRow['size'] * ($spiceRow['amount']/100) * $spiceRow['quantity']);

        if($spiceRow['spice'] == $spiceName)
        {
            $spicesList[$spiceKey]['total'] += $spiceRow['total'];
            $spiceRow['total'] += $spiceTotal;

        }

        $spiceName = $spiceRow['spice'];
        $spiceTotal = $spiceRow['total'];
        $spiceKey = $key;
    }



//usort($spicesList, "sizesort");


foreach($spicesList as $spiceRowItem)
{

    $url = $_SERVER['PHP_SELF'] . "?id=" . $spiceRowItem['record_id'];

    if($spiceRowItem['amount'] < $cutoff)
    {
        $spiceRowItem['amount'] .= "|".$highlightRed;
    }

    //get last update
    $pdo->query($sqlGetLastPantryUpdate, ['binds'=>[$spiceRowItem['id']],'fetch'=>'one']);
    $update = $pdo->result['ts'];
    $update = get_date('Y-m-d',$update);

    Bootstrap4::table_row([$spiceRowItem['spice'],$spiceRowItem['category'],$spiceRowItem['container']
        ,$spiceRowItem['quantity'],$spiceRowItem['size']
        ,$spiceRowItem['amount']
      ,$spiceRowItem['total'],$update], ['class' => $rowClass, 'data-href' => $url]);

    unset($update, $url);

}


Bootstrap4::table_close();

$form->submit();
echo "<a role='button' href='pantry.php' class='btn btn-primary' name='clear'>Clear</a>";
$form->close();

include 'footer.php';


?>