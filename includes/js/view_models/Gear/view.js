var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.data_array = ko.observableArray();
	self.fieldsList = ko.observableArray();
	self.table = '';
	
	self.init = function() {
		self.generateArrays();
		self.refreshDatatables();
	};

	self.generateArrays = function(){
		// For each of the sets of data, create row arrays and field arrays in the appropriate format
		for(var index in data['row_data']){
			self.data_array.push(new RecordViewModel(data['row_data'][index]));
		}
		for(var index in data['fields']){
			self.fieldsList.push(new RecordViewModel(data['fields'][index]));
		}
	}

	self.refreshDatatables = function() {
		// Create the columns array for this table
		columnsArr = [];
		for(var index in self.fieldsList()){
			var field=self.fieldsList()[index]['name'];
			if (field==='deleted'||field==='retired'){
				var functionStr="if (row['"+field+"']==1){return 'Yes';}else{return 'No';}";
			// } else if (field==='returned'){
			// 	var functionStr="if (row['"+field+"']==1){return 'Yes';}else{return 'No';}";
			} else if (field==='date_return'){
				var functionStr="if (row['date_return']=='0000-00-00'){return 'Not returned';}else{return row['date_return'];}";
			} else {
				var functionStr="return row['"+field+"'];";
			}
			var tempFunc=Function("row",functionStr);  //Create a tempoary function to return the right field in each column

			columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
					title: self.fieldsList()[index]['display'] //Assign the heading of the field to the display name
				});
		}

		//Put the data into a table
		self.table = $("#dataTable").DataTable({
			data: self.data_array(),
			columns: columnsArr, 
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true,
			initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select class="form-control"><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
	 
							column
								.search( val ? val : '', true, false )
								.draw();
						} );
					var already_used=Array();
					column.data().unique().sort().each( function ( d, j ) {

						if(d.search('div')==-1){
							if (already_used[d]!=true){
								already_used[d]=true
								select.append( '<option value="'+d+'">'+d+'</option>' )
							}
						} else {
							for (var ind1 in d.split('<div>')){
								var temp_split1=d.split('<div>')[ind1]
								if (temp_split1!=''){
									var correct_string=temp_split1.split('</div>')[0]
									if (already_used[correct_string]!=true){
										select.append( '<option value="'+correct_string+'">'+correct_string+'</option>' )
										already_used[correct_string]=true
									}
								}
							}
						}
					} );
				} );
			}
		});
		
		$("#dataTable").on('click', 'tbody tr', function(e){
			// Link the rows of the table to the edit pages if the table if the heading of the table has edit in it.
			if (self.initialData['url']!='') window.document.location = self.initialData['url']+self.table.row( this ).data()[self.initialData['url_id']];
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