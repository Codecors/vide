<?php 
session_start();
require_once 'load.php';

$user = new User();
$is_logged_in = $user->is_logged_in();
$is_admin = $user->is_admin();

if(!empty($_POST)){
	
 	$data = json_decode($_POST['data'], true);

	$action = $data['action'];
	$content= $data['content'];
	
	switch($action){
		case "loginCheck":
			$result = $user->is_logged_in();
			$data = [];
			$messages = $user->all_messages;
			break;
		case "adminCheck":
			$result = $user->is_admin();
			$data = [];
			$messages = $user->all_messages;
			break;
		case "loginUser":
			$result = $user->login_user($content['username'], $content['password']);
			$data = [];
			$messages = $user->all_messages;
			break;
		case "logoutUser":
			$result = $user->logout_user();
			$data = [];
			$messages = $user->all_messages;
			break;
		case "registerUser":
			$result = $user->register_user($content);
			$data = [];
			$messages = $user->all_messages;
			break;
		case "getVideo":
			$video = new Video();
			$data = $video->get_video_data($content["video_id"], $is_logged_in);
			$result = ($data != false);
			$messages = $video->all_messages;
			break;
		case 'getVideoList':
			$video = new Video();
			$data = $video->get_video_list($is_admin);
			$result = ($data != false);
			$messages = $video->all_messages;
			break;
		case 'editVideo':
			$video = new Video();
			$data = [];
			$result = $video->edit_video($content, $is_admin);
			$messages = $video->all_messages;
			break;
		case 'deleteVideo':
			$video = new Video();
			$result = $video->delete_video($content["video_id"], $content["video_file"], $content["thumbnail_file"], $is_admin);
			$data = [];
			$messages = $video->all_messages;
			break;
		case 'deleteThumbnail':
			$video = new Video();
			$result = $video->delete_thumbnail($content["video_id"], $is_admin);
			$data = [];
			$messages = $video->all_messages;
			break;
	}
	
	$reply = array(
			"result" => $result,
			"data" => $data,
			"messages" => $messages
	);
		
	$reply = json_encode($reply, JSON_UNESCAPED_UNICODE , 3);
	echo $reply;
	
}else{
	echo "post empty";
}