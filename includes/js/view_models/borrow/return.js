

var ViewModel = function(data){
	var self = this;
	//Set up the variables to hold input data
	self.initialData = data;
	self.gearArray = ko.observableArray();
	self.returnedGear = ko.observableArray();
	self.selectedGear = ko.observableArray();
	
	self.init = function() {
		// Create the data tables
		self.generateArrays();
		self.refreshDatatable();
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
	}


	self.refreshDatatable = function() {
		//Create the first dataTable
		self.table = $("#dataTable").DataTable({
			data: self.gearArray(),
	        columns: [
	            { 	"data": function(row){
		        		return row['id'];
	            	}, 
		            title: "ID" 
		        },
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
		        		return row['type'];
	            	}, 
		            title: "Type" 
		        }
	        ], 
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true
	        // "pagingType": "full_numbers"
		});

		$("#dataTable").on('click', 'tbody tr', function(e){
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
		self.table2 = $("#dataTable2").DataTable({
			data: self.returnedGear(),
	        columns: [
	            { 	"data": function(row){
		        		return row['id'];
	            	}, 
		            title: "ID" 
		        },
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
		        		return row['type'];
	            	}, 
		            title: "Type" 
		        }
	        ], 
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [],
			fixedHeader: true
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