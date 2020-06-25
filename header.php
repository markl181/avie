<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="cache-control" content="no-cache" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="Shortcut Icon" href="css/favicon.ico">
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.corner.js"></script>
    <script type="text/javascript" src="js/fusioncharts.js"></script>
    <script type="text/javascript" src="js/themes/fusioncharts.theme.ocean.js"></script>


    <!--Bootstrap 4-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <script type="text/javascript" src="./js/jquery_addons.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <script src="js/avie.js"></script>
    <link href="css/food.css" rel="stylesheet" type='text/css'/>
<link href="css/import/custom.css.map" rel="stylesheet" type="text/css"/>
<?php
echo "<title>$pageTitle</title>";
echo "</head>";
echo "<body class='plain'>";

$ipList = ['127.0.0.1', '75.156.174.108', '::1'];

require 'connect.php';


error_reporting(E_ERROR);

require_once 'functions.php';
require_once 'qrs.php';

//open bootstrap css container
use \tools\Bootstrap4;
class_alias('\tools\Bootstrap4','Bootstrap4');
$bootstrap = new Bootstrap4('y');
//open frequently used classes
use \tools\simple_pdo;
$pdo = new simple_pdo($host, $dbname, $user, $pass);
use \tools\form4;
$form = new form4;

//set the recipe count variable
$pdo->query($sqlGetRecipeCount,['fetch'=>'one']);
$recipeCt = $pdo->result['ct'];
Bootstrap4::$recipeCount = $recipeCt;


$today = get_date('Y-m-d');

$redline = 5;
$greenline = 7;
$newCutoff = 30;
$updatedCutoff = 15;
$requestCutoff = 30;

Bootstrap4::heading($pageTitle,1);
Bootstrap4::linebreak(2);

include 'menu.php';

?>