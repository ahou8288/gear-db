

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.fields_list = ko.observableArray();

	self.f_names=[];
	self.f_vals=[];

	self.init = function() {
		self.generateArrays();
		if (data['id']>0) self.checkRadio();
	};

	self.generateArrays = function(){
		// Put each of the fields in an array.
		for(var index in data['fields_list']){
			self.fields_list.push(new RecordViewModel(data['fields_list'][index]));
		}
	}
	self.checkRadio = function(){
		for(var index in self.initialData.fields_list){ //For each input
			if( self.initialData.fields_list[index]['radio']==1){ //If it is a radio button
				var f_name = self.initialData.fields_list[index]['post_name']; //find the name of the input
				var f_val = parseInt(self.initialData.fields_list[index]['value']); //Find the which button it is
				var f_num = self.initialData.fields_list[index]['options'][f_val][0]; //Find the value of this button
				// console.log(f_name);
				// console.log(f_val);
				// console.log(f_num);
				//Store the name and value of the button
				self.f_names.push(f_name);
				self.f_vals.push(f_val);
			}
		}

		// Check the radio buttons
		$(function() {
			for (var i = 0; i < self.f_names.length; i++) { //For each button
				var $radios = $('input:radio[name='+self.f_names[i]+']'); //Find the set of input buttons
				if($radios.is(':checked') === false) {
					$radios.filter('[value='+self.f_vals[i]+']').prop('checked', true); //Check the correct button by matching the value
				}
			}
		});
	}
}

var RecordViewModel = function(data){
	var self = this;
	for(var field in data){
		var val = data[field];
		self[field] = ko.observable(val); //Store the valuse as observable so that they can be edited and update automatically.
	}
}