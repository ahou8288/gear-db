

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.gearArray = ko.observableArray();
	
	self.init = function() {
		self.generateArrays();
		self.refreshDatatable();
		
	};

	self.generateArrays = function(){
		for(var index in data['data']['gear']){
			self.gearArray.push(new RecordViewModel(data['data']['gear'][index]));
	}


	self.refreshDatatable = function() {
		self.table = $("#dataTable").DataTable({
			data: self.gearArray(),
	        columns: [
	            
		        { 	"data": function(row){
		        		return row.Project_Record['Design_Lot_ID'];
	            	}, 
		            title: "Lot ID" 
		        },
		        { 	"data": function(row){
		        		return row.Project_Record['Description'];
	            	}, 
		            title: "Description" 
		        }
	        ], 
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [
	            'colvis', 'excel'
	        ],
			fixedHeader: true
	        // "pagingType": "full_numbers"
		});

/*		$("#dataTable").on('click', 'tbody tr', function(e){
			//window.document.location = 'edit/'+self.table.row( this ).data().Appointment_ID();
			// console.log(self.table.row( this ).data());
			clickedRow = self.table.row( this ).data();
		});*/
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

