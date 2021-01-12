<?php
/*
Created by Mark Leci - 2020-12-30

*/
session_start();
$pageTitle = 'Avie - Apples!';


include 'header.php';

$emptyStar = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
  <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.523-3.356c.329-.314.158-.888-.283-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767l-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288l1.847-3.658 1.846 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.564.564 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>
</svg>';
$filledStar = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
    </svg>';

if(isset($_SESSION['id'])) {

    //troubleshoot($_SESSION);

    $sqlGetAppleById = "SELECT name, rating FROM avie_apple WHERE id = ?";

    $id = filter_var($_SESSION['id'], FILTER_SANITIZE_NUMBER_INT
    );

    $pdo->query($sqlGetAppleById, ['binds'=>[$id], 'fetch'=>'one']);
    $dbRow = $pdo->result;

    $dbApple = $dbRow['name'];
    $dbRating = $dbRow['rating'];


}

if(isset($_POST['submit']))
{


   $sqlInsertApple = "INSERT INTO avie_apple (name, rating) VALUES (?,?)";
   $sqlUpdateApple = "UPDATE avie_apple SET name = ?, rating = ? WHERE id = ?";

   $appleName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $appleRating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);

    $pdo->idColumn = 'id';
    $pdo->searchColumn = 'name';
    $pdo->searchTable = 'avie_apple';
    $pdo->searchValue = $appleName;
    $pdo->insertQuery = $sqlInsertApple;
    $pdo->updateQuery = $sqlUpdateApple;
    $pdo->insertBinds = [$appleName, $appleRating];
    $pdo->updateBinds = [$appleName, $appleRating, $id];
    $pdo->find_id(true);
    $appleId = $pdo->recordId;


    unset($dbRating, $dbApple);
    session_destroy();

}





$form->open();

$form->input('name','Name:',['type'=>'text','required'=>'required','autocomplete'=>'autocomplete'
    , 'value'=>$dbApple]);
$form->select('rating','Rating:',[''=>'',1=>1,2=>2,3=>3,4=>4,5=>5],$dbRating);

$sqlGetApples = "SELECT id, name, rating, timestamp from avie_apple ORDER by name";



$pdo->query($sqlGetApples);
$appleList = $pdo->result;

$appleCount = count($appleList);

Bootstrap4::heading("$appleCount apples tried",3);

Bootstrap4::linebreak(2);
Bootstrap4::table(['Apple','Date','Rating']);

$rowClass = 'text-left clickable-row';

foreach($appleList as $apple)
{

    $apple['ratingOut'] = '';

    $apple['timestamp'] = get_date('Y-m-d', $apple['timestamp']);

    if($apple['rating'] >=1)
    {
        for($i=1;$i<=$apple['rating'];$i++)
        {
            $apple['ratingOut'] .= $filledStar;
        }

        for($j=$apple['rating'];$j<5;$j++)
        {
            $apple['ratingOut'] .= $emptyStar;

        }
    }

    $url =  "process.php?ref=apples&id=" . $apple['id'];


    Bootstrap4::table_row([$apple['name'], $apple['timestamp'],$apple['ratingOut']], ['class' => $rowClass, 'data-href' => $url]);


}
 

Bootstrap4::table_close();

$form->submit('submit');
$form->close();


include 'footer.php';
?>