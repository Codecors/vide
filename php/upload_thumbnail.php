<?php
session_start();
require_once 'load.php';

$user = new User();
$is_logged_in = $user->is_logged_in();
$is_admin = $user->is_admin();

if(!empty($_POST) && !empty($_FILES)){
	
	$id = $_POST['video_id'];
	$input = new video();
	
	$file_upload = $input->upload_thumbnail($_FILES, $id, $is_admin);
	
	if(!$file_upload){
		echo 'Fehler beim Hochladen der Datei';
	}else{
		
		echo "<script>window.location.assign('http://localhost/video-cms/www/#/video/" . $file_upload['video_id'] . "')</script>";
	}
	
}else{
	echo "Leeres Formular";
}