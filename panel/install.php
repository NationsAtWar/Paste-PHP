<?php

define('HTTPROOT', 			'http://'.$_SERVER['SERVER_NAME']);
define('LOCALROOT', 		str_replace("/index.php","",$_SERVER['SCRIPT_FILENAME']).'/');
define('SUBDIR',			str_replace("/index.php","",$_SERVER['SCRIPT_NAME']).'/');

define('PANEL_DBUSERNAME', 'pasteweb');
define('PANEL_DBPASSWORD', '');
define('PANEL_DBHOSTNAME', 'localhost');
define('PANEL_DBDATABASE', 'pasteweb');

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
				
					<h2>Panel Database Installer</h2>
					<form id="install_form" name="app" action="install.php" method="post">
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
