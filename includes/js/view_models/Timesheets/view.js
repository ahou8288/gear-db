

var ViewModel = function(data){
	var self = this;
	self.initialData = data;
	self.labourDetails = ko.observableArray();
	//self.Staff = ko.observableArray();
	//self.Staff = {};
	//self.Projects = {};





	
	self.init = function() {
		self.generateArrays();
		self.refreshDatatable();
		
	};

	self.generateArrays = function(){
		for(var index in data['table']['LD']){
			self.labourDetails.push(new RecordViewModel(data['table']['LD'][index]));
			
		}
		// for(var index in data['Staff']){
		// 	//self.Staff.push(new RecordViewModel(data['Staff'][index]));
		// 	self.Staff[data['Staff'][index]['Employee']] = data['Staff'][index];
			
		// }
		// for(var index in data['Projects']){
		// 	self.Projects[data['Projects'][index]['Project_Number']] = data['Projects'][index];
			
		// }
	}


	self.refreshDatatable = function() {
		self.table = $("#dataTable").DataTable({
			data: self.labourDetails(),
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
		        },
		        { 	"data": function(row){
		        	
	            		return 'Opus';
	            	}, 
		            title: "Employee ID" 
		        } ,
		        { 	"data": function(row){
		        		console.log(row);
		        		return row.Staff_Record['FirstName'];
		        		
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Employee First Name" 
		        }, 
		        { 	"data": function(row){
		        		return row.Staff_Record['LastName'];
		        		
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Employee Last Name" 
		        }, 
		        { 	"data": function(row){
		        		return "L"+row.Staff_Level;
		        		
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Role ID" 
		        }, 
		        { 	"data": function(row){
		        		return row.Staff_Record['Role'];
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Role Description" 
		        }, 
		        { 	"data": function(row){
	            		return row.RegHrs;
	            	}, 
		            title: "Hours" 
		        }, 
		        { 	"data": function(row){
	            		return "";
	            	}, 
		            title: "Variation No." 
		        },
		        { 	"data": function(row){
		        		return row.Staff_Rate;
		        		
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Banded Rate A$/hr" 
		        } ,
		        { 	"data": function(row){
		        	
		        		return Math.round(row.Staff_Rate*row.RegHrs*100, 2)/100;
		        		
	            		//return self.Staff[row.Employee()]['FirstName'];
	            	}, 
		            title: "Claimed Amount A$" 
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

		$("#dataTable").on('click', 'tbody tr', function(e){
			//window.document.location = 'edit/'+self.table.row( this ).data().Appointment_ID();
			// console.log(self.table.row( this ).data());
			clickedRow = self.table.row( this ).data();
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

