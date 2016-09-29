angular.module("main").factory('videoDataService', function($http, paths){
	
	 return {
		
		getVideo: function(toSend){
		
			return $http({
				method: "post",
				url: paths.videoPath,
				data: "data=" + toSend,
				headers: {"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"} 
			}).then(function(result){
				return result.data;
			})
			
		}
	}
})
