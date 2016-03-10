

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.projects = ko.observableArray();
	//self.Staff = ko.observableArray();
	//self.Staff = {};
	//self.Projects = {};





	
	self.init = function() {
		self.generateArrays();

		//document.getElementsByClassName("loadText")[0].style.visibility="hidden";
	};

	self.generateArrays = function(){
		for(var index in data['tallData']){
			//console.log(data[index]);
			self.projects.push(new RecordViewModel(data['tallData'][index]));
			
		}
		// for(var index in data['Staff']){
		// 	//self.Staff.push(new RecordViewModel(data['Staff'][index]));
		// 	self.Staff[data['Staff'][index]['Employee']] = data['Staff'][index];
			
		// }
		// for(var index in data['Projects']){
		// 	self.Projects[data['Projects'][index]['Project_Number']] = data['Projects'][index];
			
		// }
	}






}

var RecordViewModel = function(data){
	var self = this;
	self.initialData = data;

	for(var field in data){
		if(field != 'children'){
			var val = data[field];
			self[field] = ko.observable(val);
			//self[field] = val;
		} else {
			self.children = ko.observableArray();
			for(var index in data['children']){
				//console.log(data[index]);
				self.children.push(new RecordViewModel(data['children'][index]));
				
			}
		}
		
	}

}

