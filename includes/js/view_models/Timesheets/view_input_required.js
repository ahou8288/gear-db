var ViewModel = function(data){
	var self = this;
	self.startDate = ko.observable();
	self.finishDate = ko.observable();

	self.init = function(){
		var previousMonday = moment().day("Monday");
		self.startDate(previousMonday.format("DD/MM/YYYY"));
		var finishDay = previousMonday.add(4, 'days');
		self.finishDate(finishDay.format("DD/MM/YYYY"));

	}

	self.checkNotMonday = ko.computed(function(){
		if(self.startDate()){
			var startDay = moment(self.startDate(), "DD/MM/YYYY");
			if(startDay.weekday() == "1"){
				return false;
			} else {
				return true;
			}
		}
		return false;
	});

	self.checkTooLong = ko.computed(function(){
		if(self.startDate() && self.finishDate()){
			var startDay = moment(self.startDate(), "DD/MM/YYYY");
			var finishDay = moment(self.finishDate(), "DD/MM/YYYY");
			if(finishDay.diff(startDay, 'days') < 0 || finishDay.diff(startDay, 'days') == "4"){
				return false;
			} else {
				return true;
			}
		}
		return false;
	});
	self.checkInvalidFinish = ko.computed(function(){
		if(self.startDate() && self.finishDate()){
			var startDay = moment(self.startDate(), "DD/MM/YYYY");
			var finishDay = moment(self.finishDate(), "DD/MM/YYYY");
			if(finishDay.diff(startDay, 'days') >= 0 ){
				return false;
			} else {
				return true;
			}
		}
		return false;
	});

	self.startDate.subscribe(function(newValue){
		console.log(newValue);

		// var split = newValue.split("/");
		// d = new Date(split[2], split[1], split[0]);
		// newDate = addDays(d, 4);
		// console.log(newDate);
		
		var startDay = moment(newValue, "DD/MM/YYYY");
		var finishDay = startDay.add(4, 'days');
		self.finishDate(finishDay.format("DD/MM/YYYY"));
	})	

}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}