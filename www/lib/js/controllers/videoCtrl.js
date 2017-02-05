angular.module("main").controller("videoCtrl", function($scope, $location, $window, $http, paths, getIdService, getDataService){
	
	//get video id when single video page is loaded
	$scope.video_id = getIdService.getID($location.path());
	
	// rating
	$scope.ratings = [];
	
/* ===================================================================================== */	
	
	//load video data when single video page is loaded
	$scope.videoData = [];


	$scope.getVideoData = function(){
		
//		var i;
//		
//		for(i=0;i<6;i++){
//			
//			$scope.ratings[i].ratingClass = "glyphicon glyphicon-star-empty";
//		}
				
		var toSend = {"action" : "getVideo",
					"content" : {
						"video_id" : $scope.video_id
					}
			};
		
		var call = getDataService.getData(toSend);
		call.then(function(result){
			
			if(result.result == false){
				
				$window.location.href = '#/videos';
			}
			else{
				$scope.videoData = JSON.parse(result.data);
				$scope.thumbSrc = paths.thumbsDir + $scope.videoData['thumbnail_file'];
				$scope.isAdmin = result.isAdmin;
				$scope.msg.messages = result.messages;
				
//				var rated = $scope.videoData['video_rating'];
//				var i;
//				for(i=0; i < rating; i++){
//					$scope.ratings[i].ratingClass = "glyphicon glyphicon-star";
//					console.log(i);
//				}
			}
		});
	}
	
	$scope.getVideoData();
	
/* ===================================================================================== */

	//rating class
//	$scope.rating.ratingClass = function(rated){
//		
//		var set_rating = "ratingClass_" + rated;
//		return set_rating;
//		
//	}
	
	//change video rating
//	$scope.rateVideo = function(rated){
//		
//		$scope.videoData['video_rating'] = rated;
//		
//		for(i = 1; i < parseInt(rated); i++){
//			
//			$scope.ratingClass[i] = "glyphicon glyphicon-star-empty";
//			
//		}
//		
//		var toSend = {
//				"action" : "changeRating",
//				"content" : {
//					"video_id" : this.videoID,
//					"newRating" : this.videoData['video_rating']
//				}
//		};
//		
//		var call = getDataService.getData(toSend);
//		call.then(function(result){
//			console.log(result);
//			$scope.msg.messages = result.messages;
//		});
//		
//	} 

/* ===================================================================================== */	
	
	//set video to public/private
	$scope.setPublic = function(){
		
		var toSend = {
				"action" : "setPublic",
				"content" : {
					"video_id" : this.videoData['video_id'],
					"video_public" : this.videoData['video_public']
				}
		};
		
		var call = getDataService.getData(toSend);
		call.then(function(result){
			console.log(result);
			$scope.msg.messages = result.messages;
		});
		
	} 
	

/* ===================================================================================== */		
//	send edited video data to db
	
	$scope.sendEditedData = function(){
		$scope.videoData['video_id'] = $scope.video_id;
		var send = {"action" : "editVideo",
					"content" : $scope.videoData
					};
		
		var call = getDataService.getData(send);
		call.then(function(result){
			$scope.msg.messages = result.messages;
		});
	}
	
/* ===================================================================================== */
	
//	delete the video
	
	$scope.deleteVideo = function(){
		
		var confirmDelete = confirm("Video wirklich endgültig löschen?");
		
		if(confirmDelete){
			
			var toSend = {"action" : "deleteVideo",
						"content" : {
							"video_id" : $scope.video_id,
							"video_file" : $scope.videoData['video_file'],
							"thumbnail_file" : $scope.videoData['thumbnail_file']
						}
					};
						
			var call = getDataService.getData(toSend);
			call.then(function(result){
				if(result.result){
					$window.location.href="#/videos";
				}
				$scope.msg.messages = result.messages;
			});
		}
	}

/* ===================================================================================== */
	
//	delete the thumbnail image
	
	$scope.deleteThumbnail = function(){
		
		var confirmThumbDelete = confirm("Bild wirklich endgültig löschen?");
		
		if(confirmThumbDelete){
						
			var toSend = {"action" : "deleteThumbnail",
						"content" : {
							"video_id" : $scope.video_id,
							"thumbnail_file" : $scope.videoData['thumbnail_file']
						}
					};
			
			var call = getDataService.getData(toSend);
			call.then(function(result){
				if(result.result){
					$window.location.reload();
				}
				$scope.msg.messages = result.messages;
			});
		}
	}
	
	
	
	
});