var ViewModel = function(recordsArray) {
	var self = this;

	self.table = null;
	self.recordsArray = recordsArray;
	self.recordsVMArray = null;


	self.init = function() {
		self.generateRecordsVMArray();
		self.refreshDatatable();
	};

	self.generateRecordsVMArray = function() {
		self.recordsVMArray = new Array();
		for(var index in self.recordsArray){
			self.recordsVMArray.push(new RecordViewModel(self.recordsArray[index]));
		}
	}

	self.refreshDatatable = function() {
		self.table = $("#recordsTable").DataTable({
			data: self.recordsVMArray,
	        columns: [
	            { 	"data": function(row){
	            		return row.ID();
	            	}, 
		            title: "ID" 
		        },
		        { 	"data": function(row){
	            		return row.Client();
	            	}, 
		            title: "Client" 
		        },
		        { 	"data": function(row){
	            		return row.Year();
	            	}, 
		            title: "Year" 
		        }, 
		        { 	"data": function(row){
	            		return row.Box();
	            	}, 
		            title: "Box" 
		        }, 
		        { 	"data": function(row){
	            		return row.Contents();
	            	}, 
		            title: "Contents" 
		        }, 
	        ], 
			stateSave: true,
			dom: '<"left"l>fBrtip',
			buttons: [
	            'colvis'
	        ],
			fixedHeader: true
	        // "pagingType": "full_numbers"
		});



		$("#recordsTable").on('click', 'tbody tr', function(e){
			//alert($(this).attr('data-structureId'));
			// alert('sup');
			//window.document.location = $(this).attr('data-url');
			// console.log( self.table.row( this ).data() );
			window.document.location = 'edit/'+self.table.row( this ).data().ID();
		});
	};

};

var RecordViewModel = function(data){
	var self = this;

	// console.log(data);
	for(var field in data){
		var val = data[field];
		switch(field){
			case "External":
			case "FS":
			case "ITR":
			case "ITR_COR":
			case "WP":
			case "LT":
			case "COR":
			case "Other":
				if(data[field]){
					val = (data[field] == "TRUE" || data[field] == 1);
				}
				break;
			case "ID":
			case "Year":
				if(data[field]){
					val = parseInt(data[field]);
				}
				break;
				
		}
		self[field] = ko.observable(val);
		
	}

	self.Contents = ko.computed(function(){
		var contentsList = "";
		var fields = ['FS', 'ITR', 'ITR_COR', 'WP', 'LT', 'COR'];

		for(var fieldIndex in fields){
			if(self[fields[fieldIndex]] && self[fields[fieldIndex]]()){
				if(contentsList){
					contentsList += ", ";
				}
				contentsList += fields[fieldIndex];
			}
		}

		if(self.Other() && self['Other_Description']()){
			if(contentsList){
				contentsList += ", ";
			}
			contentsList += self['Other_Description']();
			// console.log(self['Other_Description']());
		}

		return contentsList;
	});
}

