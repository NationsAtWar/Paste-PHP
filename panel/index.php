<?php

define('HTTPROOT', 			'http://'.$_SERVER['SERVER_NAME']);
define('LOCALROOT', 		str_replace("/index.php","",$_SERVER['SCRIPT_FILENAME']).'/');
define('SUBDIR',			str_replace("/index.php","",$_SERVER['SCRIPT_NAME']).'/');

define('PANEL_DBUSERNAME', 'paste_web');
define('PANEL_DBPASSWORD', '');
define('PANEL_DBHOSTNAME', 'localhost');
define('PANEL_DBDATABASE', 'paste_web');

define('MC_DBUSERNAME', 'paste_mc');
define('MC_DBPASSWORD', '');
define('MC_DBHOSTNAME', 'localhost');
define('MC_DBDATABASE', 'paste_mc');

/**
* @ignore
*/
define('IN_PHPBB', true);
// Specify the path to your phpBB3 installation directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../../forum/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// The common.php file is required.
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if ($user->data['user_id'] == ANONYMOUS){
	login_box('', 'LOGIN');
}

//Checks the token against what's in the Paste DB
function compareToken($token, $username) {
		$verified = 0;
		
		$datab = mysql_connect(PANEL_DBHOSTNAME,PANEL_DBUSERNAME,PANEL_DBPASSWORD);
		mysql_select_db(PANEL_DBDATABASE, $datab);
		$querystring = 'SELECT * FROM  `requests` WHERE `Type`="SETTOKEN" AND `Status`="SENT" ORDER BY `Time` DESC;';
		$result = mysql_query($querystring);
		
		if(!$result) {
			mysql_close();
			return $verified;
		}
		if(mysql_num_rows($result) < 1) {
			mysql_close();
			return $verified;
		}
		
		$alts = array();
		$i = 0;
		while($row = mysql_fetch_assoc($result)) {
			$data = json_decode($row['Data'], TRUE);
			//if the username matches
			if(strcmp(strtolower($data['username']), strtolower($username)) == 0) {
				//see if it's the first and matches, if not, the token has been superseded.
				if($i == 0) {
					if(strcmp($data['token'], $token) == 0) {
						$verified = 1;
						$querystring = sprintf('UPDATE `requests` SET `Status` = "CONFIRMED" WHERE  `Id` = %d;',$row['Id']);
						mysql_query($querystring);
						//continue the loop to collect all the alternate requests
					} else {
						//break the loop entirely to return false
						break;
					}
				}
				//increment for others in case it's verified, we can flip them all to COMPLETED.
				if($verified && $i != 0) {
					$alts[] = $row['Id'];
				}
				$i++;
			}
		}
		if($verified) {
			/*$stmt = $datab->getPrepared();
			if($stmt->prepare('UPDATE `requests` SET `Status` = "COMPLETED" WHERE  `Id` = "?"')) {
				$stmt->bindParam(1, $id);
				foreach($alts as $num) {
					$id = $num;
					$stmt->execute();
				}
				$stmt->close();
			}*/
			foreach($alts as $num) {
						$querystring = sprintf('UPDATE `requests` SET `Status` = "REJECTED" WHERE  `Id` = %d;',$num);
						mysql_query($querystring);
			}
		}
		mysql_close();
		return $verified;
}

//Sends the confirmation back to the game server.
function activateToken($username) {
	$data = json_encode(array('status' => 'CONFIRMED', 'username' => $username));
	$querystring = "INSERT INTO `requests` (`Time`, `Type`, `Status`, `User`, `Data`) VALUES (NOW(),'CONFIRMTOKEN','%s','WEBSERVER','".$data."');";
	
	$datab = mysql_connect(MC_DBHOSTNAME,MC_DBUSERNAME,MC_DBPASSWORD);
	mysql_select_db(MC_DBDATABASE, $datab);
	$result = mysql_query(sprintf($querystring, "SENT"));
	if($result) {
		return true;
	} else {
		//cache it here...
	}
	return false;
}

//Sets the custom profile field in phpbb
function token($username) {
	global $db, $user;

	$sql = sprintf('INSERT INTO `phpbb_profile_fields_data` (`user_id`, `pf_ingamename`) VALUES (%s, "%s") ON DUPLICATE KEY UPDATE `pf_ingamename`="%s"',$user->data['user_id'], $db->sql_escape($username), $db->sql_escape($username) );
	$result = $db->sql_query($sql);
	
	return true;
}

//Checks the custom profile field in phpbb
function isTokened() {
	global $db, $user;

	$sql = 'SELECT `user_id`
        FROM `phpbb_profile_fields_data`
        WHERE `user_id`='.$user->data['user_id'].' AND `pf_ingamename` IS NOT NULL AND `pf_ingamename`!=""';
	$result = $db->sql_query($sql);
	
	if($db->sql_fetchrow($result)) {
		return true;
	}

	return false;
}

if($_POST) {
	$return = array('result' => 'error');
	if($_POST['rpc'] && $_POST['username'] && $_POST['token']) {
		if(compareToken($_POST['token'], $_POST['username'])) {
			token($_POST['username']);
			activateToken($_POST['username']);
			$return['result'] = "success";
		}
	}
	echo json_encode($return);
	exit;
} 


// Completing the script and displaying the page.
//page_footer();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Nations At War: Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTTPROOT.SUBDIR ?>favicon.ico?" />
	<link rel="stylesheet" type="text/css" media="screen" href="http://www.nationsatwar.org/wp-content/themes/2010-Cataclysm/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo HTTPROOT.SUBDIR ?>main.css" />
	<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold&subset=latin' />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo HTTPROOT.SUBDIR.'content.panel.js' ?>"></script>
</head>
<body>
	<div id="buttons">
		<a href="http://www.nationsatwar.org"> <img
			src="http://www.nationsatwar.org/wp-content/themes/2010-Cataclysm/images/nationsheader.png" />
		</a>
	</div>
	<div id="wrapper" class="hfeed">
		<div id="header">
			<div id="masthead">
				<div id="access" role="navigation">
					<div class="skip-link screen-reader-text">
						<a href="#content" title="Skip to content">Skip to content</a>
					</div>
					<div class="menu-header">
						<ul id="menu-main-header-menu" class="menu">
							<li id="menu-item-161"
								class="menu-item menu-item-type-post_type menu-item-object-page menu-item-161"><a
								href="http://www.nationsatwar.org/rules/">RULES</a></li>
							<li id="menu-item-177"
								class="menu-item menu-item-type-custom menu-item-object-custom menu-item-177"><a
								href="http://wiki.nationsatwar.org">WIKI</a></li>
							<li id="menu-item-179"
								class="menu-item menu-item-type-custom menu-item-object-custom menu-item-179"><a
								href="http://forum.nationsatwar.org/">FORUM</a></li>
							<li id="menu-item-158"
								class="menu-item menu-item-type-post_type menu-item-object-page menu-item-158"><a
								href="http://www.nationsatwar.org/downloads/">DOWNLOADS</a></li>
							<li id="menu-item-156"
								class="menu-item menu-item-type-post_type menu-item-object-page menu-item-156"><a
								href="http://www.nationsatwar.org/donations/">DONATIONS</a></li>
							<li id="menu-item-200"
								class="menu-item menu-item-type-post_type menu-item-object-page menu-item-200"><a
								href="http://www.nationsatwar.org/apply/">APPLY</a></li>
						</ul>
					</div>
				</div>
				<!-- #access -->
			</div>
			<!-- #masthead -->
		</div>
		<!-- #header -->

		<div id="main">
			<div id="container">
				<div id="content" role="main">
				
					<h2>Panel</h2>
					<?php if(!isTokened()) : ?>
					<form id="token_form" name="app" method="post">
					<p>
						<label for="token">
							Token:
						</label>
							<input type="text" name="token" />
					</p>
					<p>
						<label for="username">
							Minecraft Username:
						</label>
							<input type="text" name="username" />
					</p>
					<input class="submitapp" type="submit" value="Submit" />
					</form>
					<?php else: ?>
					<p>Your token worked!</p>
					<?php endif; ?>
					<p id="result">&nbsp;</p>
				</div>
			</div>
			<!-- #content -->
		</div>
		<!-- #container -->
		<div id="footer" role="contentinfo">
			<div id="colophon">
				<div id="site-info">
					<a href="http://www.nationsatwar.org/" title="NATIONS AT WAR"
						rel="home"> NATIONS AT WAR </a>
				</div>
				<!-- #site-info -->
			</div>
			<!-- #colophon -->
		</div>
		<!-- #footer -->

	</div>
	<!-- #wrapper -->
</body>
</html>
