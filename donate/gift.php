<?php

	// fill in DB stuffs as necessary
	
	$dbhost = '';
	$dbuser = '';
	$dbpass = '';
	$dbname = '';
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error connecting to mysql');
	mysql_select_db($dbname);

	$forumName = $_POST['username'];
	$tx = $_POST['tx'];
	
	$query = "UPDATE donations SET donator='".$forumName."' WHERE transactionID='".$tx."'";
	$result = mysql_query($query) or die(mysql_error());
?>