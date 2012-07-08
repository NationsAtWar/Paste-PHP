<?php
define('HTTPROOT', 			'http://'.$_SERVER['SERVER_NAME']);
define('LOCALROOT', 		str_replace("/index.php","",$_SERVER['SCRIPT_FILENAME']).'/');
define('SUBDIR',			str_replace("/index.php","",$_SERVER['SCRIPT_NAME']).'/');

define('BOT_USERNAME', 'NationsBot');
define('BOT_PASSWORD', '');



function getApplication($array) {
	$applicationtext = "";
	$username = "";
	include('app_questions.php');
	
	for($i = 0; $i<count($questions); $i++) {
		$r = $i + 1;
		if(stristr(strtolower($questions[$i]['label']), "name")) {
			$username = $array['q'.$r];
		}
		$applicationtext .= "\n\n[b][u]".$questions[$i]['label']."[/u][/b]\n\n";
		if($array['q'.$r] == "" || $array['q'.$r] == false) {
			$applicationtext .= 'Applicant did not answer this question';
		} else {
			$applicationtext .= $array['q'.$r];
		}
	}

	$app = array(
			'text' => htmlentities($applicationtext),
			'subject' => htmlentities("Application: ".$username)
	);

	$poll = array(
			'poll_title'      => htmlentities('Allow Entry?'),
			'poll_max_options'   => 1,
			'poll_vote_change'	=> true,
			'poll_options'	=> array(0 => 'Yes', 1 => 'No')
	);

	return array( 'app' => $app, 'poll' => $poll, 'username' => $username);

}

function curlApplication($array) {
	$thepost = getApplication($array);

	$url = "http://forum.nationsatwar.org/";
	$post_fields = 'username='.BOT_USERNAME.'&password='.BOT_PASSWORD.'&redirect=&login=Log+in';
	$lurl = $url."ucp.php";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$lurl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR,"cookie.txt");
	$result = curl_exec($ch);
	curl_close ($ch);

	preg_match('/index\.php\?sid=([A-Za-z0-9]+)/', $result, $matches);

	if(!$matches || count($matches) < 1) {
		return "error";
	}

	$purl = "http://forum.nationsatwar.org/rpc.php";

	$post_fields = array (
			'mode'	=> 'application',
			'data'	=> json_encode($thepost)
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$purl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HEADER, false );
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch,CURLOPT_COOKIEFILE,"cookie.txt");
	$result = curl_exec($ch);
	curl_close ($ch);

	preg_match('/submitted/', $result, $matches);
	if($matches && count($matches) > 0) {
		//if($this->activateToken($thepost['username'])) {
		return "success";
		//} else {
		//	return "down";
		//}
	}
	return "error";
}

if($_POST) {
	/*'result' => $this->returnValue,
			'debug' => $buf
		);
		return json_encode($array);*/
	$return = array('result' => 'error');
	if($_POST['rpc'] && $_POST['q1']) {
		 $return['result'] = curlApplication($_POST);
	}
	echo json_encode($return);
	exit;
} 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Nations At War: Apply</title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTTPROOT.SUBDIR ?>favicon.ico?" />
	<link rel="stylesheet" type="text/css" media="screen" href="http://www.nationsatwar.org/wp-content/themes/2010-Cataclysm/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo HTTPROOT.SUBDIR ?>main.css" />
	<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold&subset=latin' />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo HTTPROOT.SUBDIR.'content.apply.js' ?>"></script>
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
				
					<h2>Application Form</h2>
					<p>You can use <a href="http://forum.nationsatwar.org/faq.php?mode=bbcode#f1r0">BBCode</a> in this form</p>
					<form id="application_form" name="app" action="#" method="post">
					<?php
					$i = 0;
					include('app_questions.php');
					?>
					<?php foreach($questions as $question) : ?>
					<?php $i++; ?>
					<p>
						<label for="q<?php echo $i; ?>">
							<?php echo $question['label']; ?>
						</label>
						<?php if(strcasecmp($question['type'], 'text') == 0) : ?>
							<input type="<?php echo $question['type']; ?>" name="q<?php echo $i; ?>" />
						<?php endif;?>
						<?php if(strcasecmp($question['type'], 'textarea') == 0) : ?>
							<textarea name="q<?php echo $i; ?>"></textarea>
						<?php endif;?>	
					</p>
					<?php endforeach; ?>
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