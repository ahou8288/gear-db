

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.gearArray = ko.observableArray();
	self.returnedGear = ko.observableArray();
	self.selectedGear = ko.observableArray();
	
	self.init = function() {
		// console.log(initialData);
		self.generateArrays();
		self.refreshDatatable();
		self.refreshDatatable2();
		
	};

	self.generateArrays = function(){
		for(var index in data['gear']){
			// console.log(data['gear'][index]);
			if (data['gear'][index]['returned']=="0"){
				self.gearArray.push(new RecordViewModel(data['gear'][index]));
			} else {
				self.returnedGear.push(new RecordViewModel(data['gear'][index]));
			}
		}
	}


	self.refreshDatatable = function() {
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
			buttons: [
	        ],
			fixedHeader: true
	        // "pagingType": "full_numbers"
		});

		$("#dataTable").on('click', 'tbody tr', function(e){      
	        $(this).toggleClass('row_selected');
	        self.clickedItem=ko.observable(self.table.row( this ).data());
	        if ((self.clickedItem() != "") && (self.selectedGear.indexOf(self.clickedItem()) < 0)){
	        	self.selectedGear.push(self.clickedItem());
	        }
		});
	}

	self.refreshDatatable2 = function() {
		self.table = $("#dataTable2").DataTable({
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
	self.initialData = data;

	for(var field in data){
		var val = data[field];
		self[field] = ko.observable(val);
		// self[field] = val;
		
	}

}