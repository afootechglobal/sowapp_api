<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
header("Access-Control-Allow-Origin: *");

// ////////////for local connect 
$_HOST_NAME = 'localhost';  
$_DB_USERNAME ='root';
$_DB_PASSWORD ='';


// $_HOST_NAME = '23.94.30.18';  
// $_DB_USERNAME ='afootec1_ab';
// $_DB_PASSWORD ='ab@AfooTECH';

$conn = mysqli_connect($_HOST_NAME, $_DB_USERNAME, $_DB_PASSWORD)or die("Unable to connect to MySQL");
mysqli_select_db($conn,"afootec1_sowapp");
/////////////////////////////////////////////////////////////////
?>
