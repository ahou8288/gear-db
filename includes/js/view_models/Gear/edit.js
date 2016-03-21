

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.gearFields = new RecordViewModel(data['fields_list']);
	
	self.init = function() {
	};
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