<?php
/*
Created by Mark Leci - 2020-01-05

*/
$pageTitle = 'Avie - Recipe Requests';

include 'header.php';

Bootstrap4::menu($menu, basename(__FILE__), 4);

Bootstrap4::heading("Go <a href='recipes.php'>here</a> to submit a request",4);


if(isset($_POST['submit']))
{


    //update all the requests to no priority then set the checked ones to priority
    $pdo->query($sqlUpdateRequestPriorityBulk, ['type'=>'update']);

    foreach($_POST as $key=>$value)
    {
        if(strpos($key,'remove_') === 0 && $value == 1)
        {
            $recordId = str_ireplace('remove_','',$key);
            $recordId = filter_var($recordId, FILTER_SANITIZE_NUMBER_INT);

            //update the record to done
            $pdo->query($sqlUpdateRequestStatus, ['binds'=>[$recordId], 'type'=>'update']);

            //echo "removed";
        }
        if(strpos($key,'prioritize_') === 0 )
        {
            if($value <> 1)
            {
                $value = 0;
            }

            $recordId = str_ireplace('prioritize_','',$key);
            $recordId = filter_var($recordId, FILTER_SANITIZE_NUMBER_INT);

            echo $recordId. " set to $value<br/>";

            //update the record to priority
            $pdo->query($sqlUpdateRequestPriority, ['binds'=>[$value, $recordId], 'type'=>'update']);

            if($value == 1)
            {

                $pdo->query($sqlGetRecipeByRequestId, ['binds'=>[$recordId], 'fetch'=>'one']);
                $recipeName = $pdo->result['title'];

                $requestMessage = "A priority request was submitted for recipe $recipeName";

                send_email("New Priority Request from Avie's Recipe site",$requestMessage );
            }


        }
        if(strpos($key,'date_') === 0 && isset($value))
        {
            $recordId = str_ireplace('date_','',$key);
            $recordId = filter_var($recordId, FILTER_SANITIZE_NUMBER_INT);
            $value = filter_var($value, FILTER_SANITIZE_STRING);


            //update the record date
            $pdo->query($sqlUpdateRequestDate, ['binds'=>[$value, $recordId], 'type'=>'update']);


        }



    }


}


$pdo->query($sqlGetRequests);
$requestList = $pdo->result;

$form->open();


Bootstrap4::table(['Recipe','Course','Date','Red','Green','x Requested','Prioritize','Remove']);

foreach($requestList as $request)
{

    $request['date'] = "<input type='date' value='".$request['date']."' name='date_"
        .$request['id']."' />";

    if($request['priority'] == 1)
    {
        $requestChecked = 'checked';
    }

    $request['prioritybx'] = "<input type='checkbox' value='1' name='prioritize_"
        .$request['id']."' $requestChecked/>";
    $request['box'] = "<input type='checkbox' value='1' name='remove_"
        .$request['id']."' />";

    $request['redct'] = colorscale(0, $redline, $request['redct'], $colorindexRed);
    $request['greenct'] = colorscale(0, $greenline, $request['greenct'], $colorindexgreen);

    $request['title'] = $request['title']."||".$request['public_url'];

    Bootstrap4::table_row([$request['title'],$request['course'],$request['date']
        ,$request['redct'],$request['greenct'],$request['xrequested']
        , $request['prioritybx'], $request['box']]);


    unset($requestChecked);
}


Bootstrap4::table_close();

$form->submit();
$form->close();


include 'footer.php';



?>