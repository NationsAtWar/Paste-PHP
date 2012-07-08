<?php

function postApp($app, $poll) {
	$subject = utf8_normalize_nfc($app['subject']);
	
	// No clue how this will format in a post; I couldn't find anything on linebreaks, so I went with the standard newline.
	$app = utf8_normalize_nfc($app['text']);
	
	$uid = $bitfield = $options = '';
	
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
	/*
	 * 								'poll_title'      => htmlentities('Allow Entry?'),
								'poll_max_options'   => 1,
								'poll_vote_change'	=> true,
								'poll_options'	=> array(0 => 'Yes', 1 => 'No')
	 */
	$poll = array(
							'poll_title'      => utf8_normalize_nfc($poll['poll_title']),
							'poll_start'	=> 0,
							'poll_max_options'   => $poll['poll_max_options'],
							'poll_length'		=> 0,
							'poll_vote_change'	=> $poll['poll_vote_change'],
							'poll_options'	=> $poll['poll_options']
	);
	
	if(submit_post('post', $subject, '', POST_NORMAL, $poll, $data)) {
		echo "submitted\n";
		return true;
	}
}

define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/var/www/html/forum/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// The common.php file is required.
require_once($phpbb_root_path . 'common.' . $phpEx);
require_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

if(!$_POST) {
	echo "Not a valid request.";
	return;
}

// Start session management
$user->session_begin();
$login = $auth->login($username, $password, false);
$auth->acl($user->data);
$user->setup();

if($user->data['user_id'] == ANONYMOUS) {
	echo "Not Logged in.";
	exit;
}

$mode = $_POST['mode'];
echo $mode."\n";
switch($mode) {
	case "application":
		$application = json_decode($_POST['data'], true);
		postApp($application['app'],$application['poll']);
		break;
	default:
		echo "Not a valid request.";
}

?>