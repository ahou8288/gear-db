

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	
	self.fields_list = '';

	self.init = function() {
		self.fields_list = ko.observableArray();
		self.generateArrays();
	};


	self.generateArrays = function(){
			self.fields_list=new RecordViewModel(data['fields_list']);
	}
}

var RecordViewModel = function(data){
	var self = this;
	// self.initialData = data;

	for(var field in data){
		var val = data[field];
		// self[field] = ko.observable(val);
		self[field] = val;
		
	}

}