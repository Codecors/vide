angular.module('main').service('getDataService', function($http, paths){
	
	return{
		
		getData: function(toSend){
			
			toSend = JSON.stringify(toSend);
						
			return $http({
				method: "post",
				url: paths.getDataPath,
				data: "data=" + toSend,
				headers: {"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"} 
			}).then(function(result){
				return result.data;
			});
			
		}
	}
});