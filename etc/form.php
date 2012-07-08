<?php
	/**
	*
	* @author Original Author Username author_email@domain.tld - http://mywebsite.tld
	* @author Another Author Username another_email@domain.tld - http://domain.tld
	*
	* @package {PACKAGENAME}
	* @version $Id$
	* @copyright (c) 2007 Your Group Name
	* @license http://opensource.org/licenses/gpl-license.php GNU Public License
	*
	*/

	/**
	* @ignore
	*/
	define('IN_PHPBB', true);
	// Specify the path to your phpBB3 installation directory.
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/var/www/html/forum/';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	// The common.php file is required.
	include($phpbb_root_path . 'common.' . $phpEx);
	include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
	include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

	// since we are grabbing the user avatar, the function is inside the functions_display.php file since RC7
	/*include($phpbb_root_path . 'includes/functions_display.' . $phpEx);*/

	// Start session management
	$user->session_begin();
	$auth->acl($user->data);

	// specify styles and/or localisation
	// in this example, we specify that we will be using the file: my_language_file.php
	//$user->setup('mods/my_language_file');

	/*
	* All of your coding will be here, setting up vars, database selects, inserts, etc...
	*/

	if(empty($_POST['q1']))//||empty($_POST['q2'])||empty($_POST['q3'])||empty($_POST['q4'])||empty($_POST['q5'])||empty($_POST['q6'])||empty($_POST['q7'])||empty($_POST['q8'])||empty($_POST['q9'])||empty($_POST['q10'])||empty($_POST['q11']))
	{
		// Yes, this will look really, really ugly as-is, but it's just a sample form to use with submit_post().
	
		echo "<form name=\"app\" action=\"form.php\" method=\"post\"><br/>";
		echo "Minecraft Ingame Name: <input type=\"text\" name=\"q1\"/><br/>";
		echo "Age: <input type=\"text\" name=\"q2\"/><br/>";
		echo "Country: <input type=\"text\" name=\"q3\"/><br/>";
		echo "How long have you been playing Minecraft? <input type=\"text\" name=\"q4\"/><br/>";
		echo "Have you played on any other servers? If so, provide their names. <input type=\"text\" name=\"q5\"/><br/>";
		echo "How did you discover Nations at War? If from a friend, provide their N@W username. <input type=\"text\" name=\"q6\"/><br/>";
		echo "Why do you want to be a part of Nations at War? <input type=\"text\" name=\"q7\"/><br/>";
		echo "Have you read and agree to abide by the rules? <input type=\"text\" name=\"q8\"/><br/>";
		echo "Are you familiar with the changed gameplay mechanics the server uses? Describe them a little. <input type=\"text\" name=\"q9\"/><br/>";
		echo "Are you comfortable with using Spoutcraft and our custom launcher based on it? If not, are you willing to take instruction and learn? <input type=\"text\" name=\"q10\"/><br/>";
		echo "If invited onto the server, what would you bring to the community? <input type=\"text\" name=\"q11\"/><br/>";
		echo "<input type=\"submit\" value=\"Submit\" />";
		echo "</form>";
	}
	else
	{
		$q1 = request_var('q1','Applicant did not answer this question.', true);
		$q2 = request_var('q2','Applicant did not answer this question.', true);
		$q3 = request_var('q3','Applicant did not answer this question.', true);
		$q4 = request_var('q4','Applicant did not answer this question.', true);
		$q5 = request_var('q5','Applicant did not answer this question.', true);
		$q6 = request_var('q6','Applicant did not answer this question.', true);
		$q7 = request_var('q7','Applicant did not answer this question.', true);
		$q8 = request_var('q8','Applicant did not answer this question.', true);
		$q9 = request_var('q9','Applicant did not answer this question.', true);
		$q10 = request_var('q10','Applicant did not answer this question.', true);
		$q11 = request_var('q11','Applicant did not answer this question.', true);
		
		$subject = utf8_normalize_nfc("Application: ".$_POST['q1']);
		
		// No clue how this will format in a post; I couldn't find anything on linebreaks, so I went with the standard newline.
		$app = utf8_normalize_nfc("[b][u]Minecraft Ingame Name[/u][/b]\n\n".$q1."\n\n[b][u]Age[/u][/b]\n\n".$q2."\n\n[b][u]Country[/u][/b]\n\n".$q3."\n\n[b][u]How long have you been playing Minecraft?[/u][/b]\n\n".$q4."\n\n[b][u]Have you played on any other servers? If so, provide their names.[/u][/b]\n\n".$q5."\n\n[b][u]How did you discover Nations at War? If from a friend, provide their N@W username.[/u][/b]\n\n".$q6."\n\n[b][u]Why do you want to be a part of Nations at War?[/u][/b]\n\n".$q7."\n\n[b][u]Have you read and agree to abide by the rules?[/u][/b]\n\n".$q8."\n\n[b][u]Are you familiar with the changed gameplay mechanics the server uses? Describe them a little.[/u][/b]\n\n".$q9."\n\n[b][u]Are you comfortable with using Spoutcraft and our custom launcher based on it? If not, are you willing to take instruction and learn?[/u][/b]\n\n".$q10."\n\n[b][u]If invited onto the server, what would you bring to the community?[/u][/b]\n\n".$q11);
		
		$poll = $uid = $bitfield = $options = '';
		
		generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
		generate_text_for_storage($app, $uid, $bitfield, $options, true, true, true);
		
		// forum_id needs setting
		$data = array(
		// General Posting Settings
				'forum_id'            => 36,    // The forum ID in which the post will be placed. Should be an integer. I don't actually know what forum
				'topic_id'            => 0,    // Posts as a new topic.
				'icon_id'            => false,    // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
		
		// Defining Post Options
				'enable_bbcode'    => true,    // Enable BBcode in this post. (bool)
				'enable_smilies'    => true,    // Enabe smilies in this post. (bool)
				'enable_urls'        => true,    // Enable self-parsing URL links in this post. (bool)
				'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)
		
		// Message Body
				'message'            => $app,        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
				'message_md5'    => md5($app),// The md5 hash of your message
		
		// Values from generate_text_for_storage()
				'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
				'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.
		
		// Other Options
				'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
				'topic_title'        => $subject,    // Subject/Title of the topic. (string)
		
		// Email Notification Settings
				'notify_set'        => false,        // (bool)
				'notify'            => false,        // (bool)
				'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
				'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)
		
		// Indexing
				'enable_indexing'    => true,        // Allow indexing the post? (bool)
		
		// 3.0.6
				'force_approved_state'    => true, // Allow the post to be submitted without going into unapproved queue
		);
		
		/*
		 Theoretically this should work, but I can't find information on what it wants beyond one help topic on PHPBB's forums, which isn't exactly clear.
		Commented out as I'm less sure whether it'll work or not.
		*/
		$poll = array(
				'poll_title'      => utf8_normalize_nfc('Allow Entry?'),
				'poll_start'	=> 0,
				'poll_max_options'   => 1,
				'poll_length'		=> 15,
				'poll_vote_change'	=> true,
				'poll_options'	=> array(0 => 'Yes', 1 => 'No')
		);
		
		submit_post('post', $subject, '', POST_NORMAL, $poll, $data);
		}

	// Page title, this language variable should be defined in the language file you setup at the top of this page.
	//page_header($user->lang['MY_TITLE']);

	// Set the filename of the template you want to use for this file.
	// This is the name of our template file located in /styles/<style>/templates/.
	/*$template->set_filenames(array(
		'body' => 'index_body.html',
	));*/

	// Completing the script and displaying the page.
	//page_footer();

?>