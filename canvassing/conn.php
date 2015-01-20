<?php
	$host   ="localhost";
	$user   ="root";
	$pass   = "amurao120282";
	$dbname = "SMBi_DEV";
	include('../adodb/adodb.inc.php');
	global $conn, $dbc;
	$conn  	= ADONewConnection('mysql');
	$dbc 	= $conn->PConnect($host,$user,$pass,$dbname);
	//$conn->debug = true;
?>