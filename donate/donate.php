<?php

	// perform wizardry, put username here. Or 'Guest', if none found
	// currently just uses 'Guest'
	$username = "Guest";

	echo "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"cmd\" value=\"_donations\">";
	echo "<input type=\"hidden\" name=\"business\" value=\"\">"; // value is account receiving payment - Website Payments Standard type
	echo "<input type=\"hidden\" name=\"lc\" value=\"GB\">";
	echo "<input type=\"hidden\" name=\"item_name\" value=\"Nations at War\">";
	echo "<input type=\"hidden\" name=\"item_number\" value=\"".$username."\">"; // value is username - grab however, or put Guest if empty
	echo "<input type=\"hidden\" name=\"currency_code\" value=\"GBP\">";
	echo "<input type=\"hidden\" name=\"bn\" value=\"PP-DonationsBF:btn_donateCC_LG.gif:NonHosted\">";
	echo "<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal — The safer, easier way to pay online.\">";
	echo "<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_GB/i/scr/pixel.gif\" width=\"1\" height=\"1\">";
	echo "</form>";

?>