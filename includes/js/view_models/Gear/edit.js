

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.fields_list = ko.observableArray();

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
				// console.log(self.initialData.fields_list[index][3]);
				var f_name = self.initialData.fields_list[index][1];
				// console.log(f_name);
				var f_val = self.initialData.fields_list[index][4];
				var f_num = self.initialData.fields_list[index][3][parseInt(f_val)][0];
				if (f_name==='type'){
					f_num=parseInt(f_num)-1;
				}
				
				$(function() {
					var $radios = $('input:radio[name='+f_name+']');
					if($radios.is(':checked') === false) {
						$radios.filter('[value='+f_num+']').prop('checked', true);
					}
				});
			}
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