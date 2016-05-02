

var ViewModel = function(data){
	var self = this;
	//Create variables to hold the data
	self.initialData = data;
	self.gearArray = ko.observableArray();
	self.gear_list= ko.observableArray();

    self.selectedItems = ko.observableArray([]);
	
    self.people=ko.observableArray();
    self.selectedPerson=ko.observable();


	self.field_gear = ko.observableArray();
	self.field_people= ko.observableArray();

	self.init = function() {
		//Call the required functions on startup
		self.generateArrays();
		self.refreshDatatable();
		self.refreshDatatable2();
	};
 
    self.removeSelected = function () {
    	//Remove an item
        self.gear_list.removeAll(self.selectedItems());
        self.selectedItems([]); // Clear selection
    };

	self.generateArrays = function(){
		//Put the data from the controller into arrays.
		for(var index in data['gear']){
			self.gearArray.push(new RecordViewModel(data['gear'][index]));
		}
		for(var index in data['people']){
			self.people.push(new RecordViewModel(data['people'][index]));
		}
		for(var index in data['person_fields']){
			self.field_people.push(new RecordViewModel(data['person_fields'][index]));
		}
		for(var index in data['gear_fields']){
			self.field_gear.push(new RecordViewModel(data['gear_fields'][index]));
		}
	}

	self.refreshDatatable = function() {
		columnsArr = [];
		for(var index in self.field_gear()){
			var field=self.field_gear()[index]['name'];
			var functionStr="return row['"+field+"'];";
			var tempFunc=Function("row",functionStr);  //Create a tempoary function to return the right field in each column

			columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
		            title: self.field_gear()[index]['display'] //Assign the heading of the field to the display name
		        });
		}
		self.table = $("#dataTable").DataTable({
			data: self.gearArray(),
			// Specify the columns to show
	        columns:columnsArr, 
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: [
	        ],
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
	                            .search( val ? '^'+val+'$' : '', true, false )
	                            .draw();
	                    } );
	 
	                column.data().unique().sort().each( function ( d, j ) {
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } );
            	} );
        	}
		});

		$("#dataTable").on('click', 'tbody tr', function(e){
			// When this row is clicked selet or deselect the item.

			self.itemToAdd=ko.observable(self.table.row( this ).data());
	        if ((self.itemToAdd() != "") && (self.gear_list.indexOf(self.itemToAdd()) < 0)){ // Prevent blanks and duplicates
	            self.gear_list.push(self.itemToAdd());
	        } else {
	        	self.gear_list.remove(self.itemToAdd());
	        }
	        $(this).toggleClass('row_selected');
		});
	}


	self.refreshDatatable2 = function() {
		columnsArr = [];
		for(var index in self.field_people()){
			var field=self.field_people()[index]['name'];
			var functionStr="return row['"+field+"'];";
			var tempFunc=Function("row",functionStr);  //Create a tempoary function to return the right field in each column

			columnsArr.push({"data": tempFunc, //Assign the data of this column to the return value of the function
		            title: self.field_people()[index]['display'] //Assign the heading of the field to the display name
		        });
		}
		self.table2 = $("#personTable").DataTable({
			data: self.people(),
			// Specify the columns to show
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
	                            .search( val ? '^'+val+'$' : '', true, false )
	                            .draw();
	                    } );
	 
	                column.data().unique().sort().each( function ( d, j ) {
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } );
            	} );
        	}
		});
		$("#personTable tbody tr").on('click',function(event) {
			// When this row is clicked select or delselect the item.
	        $("#personTable tbody tr").removeClass('row_selected');        
	        $(this).addClass('row_selected');

			self.selectedPerson(self.table2.row( this ).data());
	    });
	}
}

var RecordViewModel = function(data){
	// This function puts the data into an appropriate structure
	var self = this;

	for(var field in data){
		var val = data[field];
		self[field] = val;
	}
}