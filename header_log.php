<?php
//echo "<title>$pageTitle</title>";
//echo "</head>";
//echo "<body class='plain'>";

$ipList = ['127.0.0.1', '75.156.174.108', '::1'];

require 'connect.php';

error_reporting(0);

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

$today = get_date('Y-m-d');

?>