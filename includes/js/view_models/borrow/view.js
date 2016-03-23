

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.borrowData = ko.observableArray();
	self.fieldsList = ko.observableArray();
	
	self.init = function() {
		// console.log(data);
		self.generateArrays();
		self.refreshDatatable();
		
	};

	self.generateArrays = function(){
		for(var index in data['borrow']){
			self.borrowData.push(new RecordViewModel(data['borrow'][index]));
		}
		for(var index in data['Fields']){
			self.fieldsList.push(new RecordViewModel(data['Fields'][index]));
		}
	}


	self.refreshDatatable = function() {
		columnsArr = [
				{ 	"data": function(row){
		        		return row['id'];
	            	}, 
		            title: "ID" 
		        }];

		for(var index in self.fieldsList()){

			var functionStr="return row['"+self.fieldsList()[index]['Fields']+"'];";
			var tempFunc=Function("row",functionStr);

			columnsArr.push({"data": tempFunc, 
		            title: self.fieldsList()[index]['DisplayName']
		        });
		}

		self.table = $("#dataTable").DataTable({
			data: self.borrowData(),
			columns: columnsArr,
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: ['colvis'],
			fixedHeader: true
	        // "pagingType": "full_numbers"
		});

		$("#dataTable").on('click', 'tbody tr', function(e){
			window.document.location = base_url+'Register/edit/'+self.table.row( this ).data().ID();
		});
	}



}

var RecordViewModel = function(data){
	var self = this;
	self.initialData = data;

	for(var field in data){
		var val = data[field];
		// self[field] = ko.observable(val);
		self[field] = val;
	}

}

