var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.data_array = Array();
	self.fieldsList = Array();
	self.table = Array();
	
	self.init = function() {
		self.generateArrays();
		self.refreshDatatables();
	};

	self.generateArrays = function(){
		// For each of the sets of data, create row arrays and field arrays in the appropriate format
		for (var table_num in data){
			self.data_array.push(ko.observableArray());
			for(var index in data[table_num]['row_data']){
				self.data_array[table_num].push(new RecordViewModel(data[table_num]['row_data'][index]));
			}
			self.fieldsList.push(ko.observableArray());
			for(var index in data[table_num]['Fields']){
				self.fieldsList[table_num].push(new RecordViewModel(data[table_num]['Fields'][index]));
			}
		}
	}

	self.refreshDatatables = function() {
		//This function fills multiple datatables with columns and data.

		for (var table_num in data){ //Generate multiple tables in one function
			// Create the columns array for this table
			columnsArr = [];
			for(var index in self.fieldsList[table_num]()){

				var functionStr="return row['"+self.fieldsList[table_num]()[index]['Fields']+"'];";
				var tempFunc=Function("row",functionStr); //Create a tempoary function to return the right field in each column

				columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
			            title: self.fieldsList[table_num]()[index]['DisplayName'] //Assign the heading of the field to the display name
			        });
			}
			//Put the data into a table
			self.table[table_num] = $("#dataTable"+table_num).DataTable({
				data: self.data_array[table_num](),
		        columns: columnsArr, 
				stateSave: true,
				dom: '<"left"l>fBrtip',
				buttons: [],
				fixedHeader: true
			});
			
			$("#dataTable"+table_num).on('click', 'tbody tr', function(e){
				// Link the rows of the table to the edit pages if the table if the heading of the table has edit in it.
				if (self.initialData[0]['title']=='Edit gear'){
					window.document.location = 'edit/'+self.table[table_num].row( this ).data().id;
				} else if (self.initialData[table_num]['title']=='Edit people') {
					window.document.location = 'edit/'+self.table[table_num].row( this ).data().id;
				}
			});
		}
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