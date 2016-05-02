

var ViewModel = function(data){
	var self = this;
	//Set up the variables to hold input data
	self.initialData = data;
	self.gearArray = ko.observableArray();
	self.returnedGear = ko.observableArray();
	self.selectedGear = ko.observableArray();
	self.fieldsList = ko.observableArray();

	self.init = function() {
		// Create the data tables
		self.generateArrays();
		self.refreshDatatable1();
		self.refreshDatatable2();
		
	};

	self.generateArrays = function(){
		for(var index in data['gear']){
			// put the controller data into the right format for a datatable
			if (data['gear'][index]['returned']=="0"){
				self.gearArray.push(new RecordViewModel(data['gear'][index]));
			} else {
				self.returnedGear.push(new RecordViewModel(data['gear'][index]));
			}
		}

		for(var index in data['fields']){
			self.fieldsList.push(new RecordViewModel(data['fields'][index]));
		}
	}


	self.refreshDatatable1 = function() {
		//Create the first dataTable
		columnsArr = [];
		for(var index in self.fieldsList()){
			var field=self.fieldsList()[index]['name']();
			var functionStr="return row['"+field+"'];";
			var tempFunc=Function("row",functionStr);  //Create a tempoary function to return the right field in each column

			columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
					title: self.fieldsList()[index]['display']() //Assign the heading of the field to the display name
				});
		}

		self.table = $("#dataTable1").DataTable({
			data: self.gearArray(),
			columns: columnsArr,
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true,
	        initComplete: function () {
	            this.api().columns().every( function () {
	                var column = this;
	                var select = $('<select><option value=""></option></select>')
	                    .appendTo( $(column.footer()).empty() )
	                    .on( 'change', function () {
	                        var val = $.fn.dataTable.util.escapeRegex(
	                            $(this).val()
	                        );
	 
	                        column
	                            .search( val ? '^'+val+'$' : '', true, false )
	                            .draw();
	                    } );
	 
	                column.data().unique().sort().each( function ( d, j ) {
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } );
            	} );
        	}
		});

		$("#dataTable1").on('click', 'tbody tr', function(e){
			// This is called when a row is clicked
			// Manage the selection/removal of gear
			$(this).toggleClass('row_selected');
			self.clickedItem=ko.observable(self.table.row( this ).data());
			if ((self.selectedGear.indexOf(self.clickedItem()) < 0)){
				self.selectedGear.push(self.clickedItem());
			} else{
				self.selectedGear.remove(self.clickedItem());
			}
		});
	}

	self.refreshDatatable2 = function() {
		// Generate the other datatable
		columnsArr = [];
		for(var index in self.fieldsList()){
			var field=self.fieldsList()[index]['name']();
			var functionStr="return row['"+field+"'];";
			var tempFunc=Function("row",functionStr);  //Create a tempoary function to return the right field in each column

			columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
					title: self.fieldsList()[index]['display']() //Assign the heading of the field to the display name
				});
		}
		self.table2 = $("#dataTable2").DataTable({
			data: self.returnedGear(),
			columns: columnsArr,
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true,
			initComplete: function () {
	            this.api().columns().every( function () {
	                var column = this;
	                var select = $('<select><option value=""></option></select>')
	                    .appendTo( $(column.footer()).empty() )
	                    .on( 'change', function () {
	                        var val = $.fn.dataTable.util.escapeRegex(
	                            $(this).val()
	                        );
	 
	                        column
	                            .search( val ? '^'+val+'$' : '', true, false )
	                            .draw();
	                    } );
	 
	                column.data().unique().sort().each( function ( d, j ) {
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } );
            	} );
        	}
		});
	}
}

var RecordViewModel = function(data){
	var self = this;

	for(var field in data){
		var val = data[field];
		self[field] = ko.observable(val); //Use knockout variables.
	}

}