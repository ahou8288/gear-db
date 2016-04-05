

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.rowData = ko.observableArray();
	self.rowData2 = ko.observableArray();
	self.fieldsList = ko.observableArray();
	
	self.init = function() {
		self.generateArrays();
		self.refreshDatatable();
		self.refreshDatatable2();
		
	};

	self.generateArrays = function(){
		for(var index in data['rows']){
			if(data['rows'][index]['returned']=='0'){
				self.rowData.push(new RecordViewModel(data['rows'][index]));
			} else {
				self.rowData2.push(new RecordViewModel(data['rows'][index]));
			}
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
			data: self.rowData(),
			columns: columnsArr,
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: ['colvis'],
			fixedHeader: true
		});
	}
	self.refreshDatatable2 = function() {
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

		self.table2 = $("#dataTable2").DataTable({
			data: self.rowData2(),
			columns: columnsArr,
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: ['colvis'],
			fixedHeader: true
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

