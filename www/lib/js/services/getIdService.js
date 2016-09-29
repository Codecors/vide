angular.module("main").factory("getIdService", function(){
	
	return{
	
		getID: function(path){
			
			var splitPath = path.split('/');
			var id = splitPath[splitPath.length - 1];
					
			return id;			
			
		}
	}	
});