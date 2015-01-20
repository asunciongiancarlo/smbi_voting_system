<?php
	$host   ="localhost";
	$user   ="root";
	$pass   = "amurao120282";
	$dbname = "SMBi_DEV";
	include('adodb/toexport.inc.php');
	include('adodb/adodb.inc.php');
	global $conn, $dbc;
	$conn  	= ADONewConnection('mysql');
	$dbc 	= $conn->PConnect($host,$user,$pass,$dbname);
	$conn->debug = true;
	
	$host   ="localhost";
	$user   ="root";
	$pass   = "amurao120282";
	$dbname = "SMBi_Archive";
	global $conn2, $dbc2;
	$conn2  = ADONewConnection('mysql');
	$dbc2   = $conn2->Connect($host,$user,$pass,$dbname);
	$conn2->debug = true;
	
	define('HTTP_PATH','http://smbi.dev.c3-interactive.com.ph');
?>