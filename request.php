<?php
/*
Created by Mark Leci - 2020-01-05

*/
$pageTitle = 'Avie - Recipe Requests';

include 'header.php';



$sqlGetRequests = "SELECT * FROM avie_recipe_request arr 
INNER JOIN avie_recipe ar ON ar.public_id = arr.recipe_id
WHERE active = 1";
$pdo->query($sqlGetRequests);
$requestList = $pdo->result;


Bootstrap4::table(['Recipe','Course','Date','Done']);

foreach($requestList as $request)
{

    $request['date'] = "<input type='date' value='".$request['date']." name='date_"
        .$request['id']."' />";
    $request['box'] = "<input type='checkbox' value='1' name='done_"
        .$request['id']."' />";

    Bootstrap4::table_row([$request['title'],$request['course'],$request['date'], $request['box']]);


}


Bootstrap4::table_close();




include 'footer.php';



?>