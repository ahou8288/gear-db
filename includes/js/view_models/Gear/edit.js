

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.fields_list = ko.observableArray();

	self.init = function() {
		self.generateArrays();
	};

	self.generateArrays = function(){
		// Put each of the fields in an array.
		for(var index in data['fields_list']){
			self.fields_list.push(new RecordViewModel(data['fields_list'][index]));
		}
	}
}

var RecordViewModel = function(data){
	var self = this;
	for(var field in data){
		var val = data[field];
		self[field] = ko.observable(val); //Store the valuse as observable so that they can be edited and update automatically.
	}
}