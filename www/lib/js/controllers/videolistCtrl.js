angular.module("main").controller("videoListCtrl", function($scope, $window, $http, $location, paths, getDataService){
	
	//load video data when page is loaded
	$scope.videoList = [];
		
	$scope.getVideoList = function(){
		
		var toSend = {"action" : "getVideoList",
					"content" : ""			
					};
		
		var call = getDataService.getData(toSend);
		call.then(function(result){
			
			if(result.data.length > 0){
				$scope.videoList = JSON.parse(result.data);
			}
			$scope.msg.messages = result.messages;
		});
	}
	
	$scope.getVideoList();
	
/* ===================================================================================== */
	
	$scope.getThumbSrc = function(file){
		
		var thumbSrc = paths.thumbsDir + file;
		return thumbSrc;
	}
	
});