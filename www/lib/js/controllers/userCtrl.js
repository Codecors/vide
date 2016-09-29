angular.module("main").controller("userCtrl", function($scope, $window, paths, getDataService){
	
	// sends login form input to backend
	
	$scope.loginUser = function(){
		
		var data = { "action": "loginUser",
				"content": 	{
					"username": $scope.userName,
					"password": $scope.userPassword
				}
		};
			
		var login = getDataService.getData(data)
		login.then(function(result){
			$scope.msg.messages = result.messages;
			if(result.result){
				$window.location.href = paths.reloadPath;
			}
		});
	};

/* ===================================================================================== */

	// validates register form input and sends new user data to backend
	
	$scope.registerUser = function(){
		
		if(this.userName.length > 20 || this.userName.length < 5){	
			alert("Der Benutzername muss 5-20 Zeichen lang sein");
		}	
		else if(!this.userName.match(/[a-zA-Z0-9_äöüß\-]/i)){
			alert("Der Benutzername enthält unerlaubte Sonderzeichen");
		}
		else if(this.userPassword.length > 20 || this.userPassword.length < 8){
			alert("Das Passwort muss 8-20 Zeichen lang sein");	
		}
		else if(!this.userPassword.match(/[a-zA-Z0-9_äöüß]/i)){
			alert("Das Passwort enthält unerlaubte Sonderzeichen");
		}
		else if(this.userPassword != this.passwordRepeat){
			alert("Die Passwörter stimmen nicht überein");
		}
		else{
	
			var data = {"action": "registerUser",
					"content": 	{
						"username": $scope.userName,
						"password": $scope.userPassword,
						"passwordRepeat": $scope.passwordRepeat
					}
			};
			
			var register = getDataService.getData(data)
			register.then(function(result){
				$scope.msg.messages = result.messages;
			});
		}
	}
	
});