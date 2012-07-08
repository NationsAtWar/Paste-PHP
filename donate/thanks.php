<?php

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-synch';

	$tx_token = $_GET['tx'];
	$auth_token = ""; // this needs to be changed to the auth_token of the Paypal account you're using
	$req .= "&tx=$tx_token&at=$auth_token";

	// post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
	// If possible, securely post back to paypal using HTTPS
	// Your PHP server will need to be SSL enabled
	// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

	if (!$fp) 
	{
		// HTTP ERROR
	} 
	else 
	{
		fputs ($fp, $header . $req);
		// read the body data
		$res = '';
		$headerdone = false;
		while (!feof($fp)) 
		{
			$line = fgets ($fp, 1024);
			if (strcmp($line, "\r\n") == 0) 
			{
				// read the header
				$headerdone = true;
			}
			else if ($headerdone)
			{
				// header has been read. now read the contents
				$res .= $line;
			}
		}

		// parse the data
		$lines = explode("\n", $res);
		$keyarray = array();
		if (strcmp ($lines[0], "SUCCESS") == 0) 
		{
			for ($i=1; $i<count($lines);$i++)
			{
			list($key,$val) = explode("=", $lines[$i]);
			$keyarray[urldecode($key)] = urldecode($val);
			}
			
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			$username = $keyarray['item_name'];
			$amount = $keyarray['payment_gross'];

			echo ("<p><h3>Thank you for your purchase!</h3></p>");
			echo ("<b>Payment Details</b><br>\n");
			echo ("<li>Username: $username</li>\n");
			echo ("<li>Amount: $amount</li>\n");
			echo ("");
		}
		else if (strcmp ($lines[0], "FAIL") == 0) 
		{
			// log for manual investigation
		}

	}

	fclose ($fp);
	
	// fill in DB stuffs as necessary
	
	$dbhost = '';
	$dbuser = '';
	$dbpass = '';
	$dbname = '';
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error connecting to mysql');
	mysql_select_db($dbname);
	
	// just an example, since I don't know how the table will actually be
	// I also don't know the format the transaction ID takes, so for now it's a string
	$query = "INSERT INTO donations(transactionID,donator,amount) VALUES('".$tx."','".$username."','".$amount."')"; 
	
	$result = mysql_query($query) or die(mysql_error());
	
	echo "Gifting! Give the credit from your donation to someone else! Or make sure it's credited to you if the above username is incorrect.<br/>";
	echo "<form name=\"gift\" action=\"gift.php\" method=\"post\">";
	echo "NaW Forum Name: <input type=\"text\" name=\"username\"/><br/>";
	echo "<input type =\"hidden\" name=\"tx\" value=\"".$tx_token."\">";
	echo "<input type=\"submit\" value=\"Gift!\" />";
	echo "</form>";

?>

Your transaction has been completed, and a receipt for your purchase has been emailed to you.<br> You may log into your account at <a href='https://www.paypal.com'>www.paypal.com</a> to view details of this transaction.<br>