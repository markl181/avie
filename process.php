<?php
/*
Created by Mark Leci - 2021-01-10

*/
session_start();

$_SESSION['id'] = $_GET['id'];
$_SESSION['food'] = $_GET['food'];
$ref = filter_var($_GET['ref'], FILTER_SANITIZE_STRING);
header("location:$ref.php");
exit;

?>