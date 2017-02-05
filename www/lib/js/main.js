var main = angular.module("main", ['ngRoute']);

main.config(function($routeProvider){
	
	$routeProvider
	.when("/", {
		templateUrl: "views/home.html"
	})
	.when("/videos", {
		templateUrl: "views/videos.html",
		controller: "videoListCtrl"
	})
	.when("/video/:param", {
		templateUrl: "views/video.html",
		controller: "videoCtrl"
	})
	.when("/edit-video/:param", {
		templateUrl: "views/edit_video.html",
		controller: "videoCtrl"
	})
		.when("/video-thumbnail/:param", {
		templateUrl: "views/thumbnail.html",
		controller: "videoCtrl"
	})
	.when("/kontakt", {
		templateUrl: "views/kontakt.html",
		controller: "contactCtrl"
	})
	.when("/einstellungen", {
		templateUrl: "views/settings.html",
		controller: "settingsCtrl"
	})
	.when("/datenschutz", {
		templateUrl: "views/datenschutz.html"
	})
	.when("/impressum", {
		templateUrl: "views/impressum.html"
	})
	.when("/upload", {
		templateUrl: "views/upload.html"
	})
	.when("/login", {
		templateUrl: "views/login.html",
		controller: "userCtrl"
	})
	.when("/register", {
		templateUrl: "views/register.html",
		controller: "userCtrl"
	})
	.when("/testing", {
		templateUrl: "views/test.html"
	});

})
.constant("paths", {
	// path for redirecting to home page (for reload after login)
	"reloadPath" : "/video-cms/www",
	// path for user registration/login related ajax
	"loginPath" : "gateway/user.php",
	// path for ajax reuqests for video/user data
	"getDataPath" : "gateway/send_ajax.php/",
	// path for video file upload
	"videoUploadPath" : "gateway/video_upload.php/",
	// path for thumbnail file upload
	"thumbnailUploadPath" : "gateway/thumbnail_upload.php/",
	// directory path for video files
	"videoDir" : "gateway/videos.php/",
	// directory path for thumbnail files
	"thumbsDir" : "lib/thumbnails/"
});

