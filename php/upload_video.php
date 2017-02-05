<?php 
session_start();
require_once 'load.php';

$user = new User();
$is_admin = $user->is_admin();

var_dump($_POST);
var_dump($_FILES);

if(!empty($_POST) && !empty($_FILES)){
	
	$input = new Upload($_POST, $_FILES);
	
	$file_upload = $input->upload_video($is_admin);
	
	if(!$file_upload){
		echo 'Fehler beim Hochladen der Datei';
	}else{
		
		echo "<script>window.location.assign('http://localhost/video-cms/www/#/edit-video/" . $file_upload['LAST_INSERT_ID()'] . "')</script>";
	}
	
}else{
	echo "Leeres Formular";
}