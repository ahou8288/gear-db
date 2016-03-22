<h3>Borrow Gear</h3>

<div class="col-lg-12">
	<h5>Person Borrowing Gear</h5>
	<table id='personTable' class='table'></table>
</div>
<br/>

<h5>Select your gear</h5>
<table id='dataTable' class='table'></table>
<br/>

<form action='../borrow/save' method="post">
	<div class="form-group col-lg-12">
		<label for="sel1">Gear selected;</label>
		<select class="form-control" multiple="multiple" id="sel1" height="8" data-bind="
			options:gear_list,
			selectedOptions:selectedItems,
			optionsText: function(item) {
				return item.name+' - '+item.age
			}">
		</select>
		<button class="btn btn-default" data-bind="click: removeSelected, enable: selectedItems().length > 0">Remove</button>
	</div>

	<input type="hidden" name="gear_selected" data-bind="value: ko.toJSON(gear_list)" />
	<input type="hidden" name="person_borrowing" data-bind="value: ko.toJSON(selectedPerson)" />

	<div class="form-group">
		<input type="submit" class="btn btn-default" data-bind="enable: gear_list().length>0 && selectedPerson() != undefined">
	</div>
</form>

<script>
	$(document).ready(function() {
		model = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(model);

		model.init();
	})
</script>

<style type="text/css">
	tr.row_selected td{background-color:#ccccff !important;}
</style>