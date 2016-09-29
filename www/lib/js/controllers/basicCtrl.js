angular.module("main").controller("basicCtrl", function($scope, $window, paths, getDataService){
	
	//include file paths constant
	$scope.paths = paths;
	
	//login status
	$scope.user = {};
	
	//messages to the user
	$scope.msg = {};
	
/* ===================================================================================== */	
	
	$scope.checkSession = function(){
		
		//checks if the user is logged in
		var checkLoginData = {
				"action": "loginCheck",
				"content": ""
		};
				
		var checkLogin = getDataService.getData(checkLoginData);
		checkLogin.then(function(result){
			$scope.user.isLoggedIn = result.result;
		});
		
		//checks if the user is logged in as admin
		var checkAdminData = {
				"action": "adminCheck",
				"content": ""
		};
		
		var checkAdmin = getDataService.getData(checkAdminData);
		checkAdmin.then(function(result){
			$scope.user.isAdmin = result.result;
		});	
	}
	
	$scope.checkSession();
	
/* ===================================================================================== */

// user logout (called from nav menu)
	
	$scope.logoutUser = function(){
		
		var data = { "action": "logoutUser",
				"content": 	""};
		
		var logout = getDataService.getData(data)
		logout.then(function(result){
			$scope.msg.messages = result.messages;
			$window.location.reload();
		},function(result){
			console.log('failed');
			console.log(result);
		});
		
		
	};

	
});
