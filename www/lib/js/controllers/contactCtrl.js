angular.module("main").controller("contactCtrl", function($scope, getDataService){
	
	//validate form input
	$scope.validateForm = function(){
		
		if(this.contact.fname.length < 2 || this.contact.fname.length > 20){
			alert("Der Vorname muss 2-20 Zeichen lang sein");
			return false;
		}
		else if(!this.contact.fname.match(/[a-zA-Z0-9_äöüß \-]/i)){
			alert("Der Vorname enthält unerlaubte Sonderzeichen");
		}
		else if(this.contact.lname.length < 2 || this.contact.lname.length > 20){
			alert("Der Nachname muss 2-20 Zeichen lang sein");
			return false;
		}
		else if(!this.contact.lname.match(/[a-zA-Z0-9_äöüß \-]/i)){
			alert("Der Nachname enthält unerlaubte Sonderzeichen");
		}
		else if(!this.contact.email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)){
			alert("Bitte geben Sie eine gültige Email-Adresse ein");
			return false;
		}
		else if(this.contact.about.length < 2 || this.contact.about.length > 100){
			alert("Der Betreff muss 2-100 Zeichen lang sein");
			return false;
		}
		else if(!this.contact.about.match(/[a-zA-Z0-9_äöüß \-\.\:\&\,]/i)){
			alert("Der Nachname enthält unerlaubte Sonderzeichen");
			return false;
		}
		else{
			return true;
		}
	}
	
/* ===================================================================================== */
	
	//send email
	$scope.sendEmail = function(){
		console.log("called sendEmail");
		if(this.validateForm()){
			
			var toSend = {
				"action" : "sendEmail",
				"content" : this.contact
			};
			
			var call = getDataService.getData(toSend);
			call.then(function(result){
				console.log(result);
				$scope.msg.messages = result.messages;
			});	
		}
	}
	
});