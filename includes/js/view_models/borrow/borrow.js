

var ViewModel = function(data){
	var self = this;
	//Create variables to hold the data
	self.initialData = data;
	self.gearArray = ko.observableArray();
	self.gear_list= ko.observableArray();

    self.selectedItems = ko.observableArray([]);
	
    self.people=ko.observableArray();
    self.selectedPerson=ko.observable();

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
	}

	self.refreshDatatable = function() {
		self.table = $("#dataTable").DataTable({
			data: self.gearArray(),
			// Specify the columns to show
	        columns: [/*
	            { 	"data": function(row){
		        		return row['id'];
	            	}, 
		            title: "ID" 
		        },*/
		        { 	"data": function(row){
		        		return row['age'];
	            	}, 
		            title: "Age" 
		        },
		        { 	"data": function(row){
		        		return row['name'];
	            	}, 
		            title: "Name" 
		        },
		        { 	"data": function(row){
		        		return row['cat'];
	            	}, 
		            title: "Type" 
		        }
	        ], 
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: [
	        ],
			fixedHeader: true
	        // "pagingType": "full_numbers"
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
		self.table2 = $("#personTable").DataTable({
			data: self.people(),
			// Specify the columns to show
	        columns: [/*
	            { 	"data": function(row){
		        		return row['id'];
	            	}, 
		            title: "ID" 
		        },*/
		        { 	"data": function(row){
		        		return row['name'];
	            	}, 
		            title: "Name" 
		        }
	        ], 
			stateSave: false,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true
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