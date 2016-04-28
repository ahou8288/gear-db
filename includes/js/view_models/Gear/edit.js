

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.fields_list = ko.observableArray();

	self.f_names=[];
	self.f_vals=[];

	self.init = function() {
		self.generateArrays();
		self.checkRadio();
	};

	self.generateArrays = function(){
		// Put each of the fields in an array.
		for(var index in data['fields_list']){
			self.fields_list.push(new RecordViewModel(data['fields_list'][index]));
		}
	}
	self.checkRadio = function(){
		for(var index in self.initialData.fields_list){
			if( self.initialData.fields_list[index][2]==1){
				// console.log(self.initialData.fields_list[index]);
				var f_name = self.initialData.fields_list[index][1];
				// console.log(f_name);
				var f_val = parseInt(self.initialData.fields_list[index][4]);
				if (f_name==='type'){
					f_val=f_val-1;
				}
				// console.log(f_val);
				var f_num = self.initialData.fields_list[index][3][f_val][0];
				// console.log(f_num);

				self.f_names.push(f_name);
				self.f_vals.push(f_val);
				// console.log(self.f_vals);
			}
		}
		$(function() {
			var $radios = $('input:radio[name='+f_name+']');
			if($radios.is(':checked') === false) {
				$radios.filter('[value='+f_val+']').prop('checked', true);
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