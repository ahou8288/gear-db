

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.fields_list = ko.observableArray();
	
	self.init = function() {
		self.generateArrays();
	};


	self.generateArrays = function(){
		for(var index in data['fields_list']){
			self.fields_list.push(new RecordViewModel(data['fields_list']));
		}
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