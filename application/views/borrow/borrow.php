<h3>Borrow Gear</h3>

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
			}"> </select>
		<button class="btn btn-default" data-bind="click: removeSelected, enable: selectedItems().length > 0">Remove</button>
	</div>

	<input type="hidden" name="gear_selected" data-bind="value: ko.toJSON(gear_list)" />

	<div class="form-group">
		<input type="submit" class="btn btn-default">
	</div>
</form>

<script>
	$(document).ready(function() {
		model = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(model);

		model.init();
	})
</script>