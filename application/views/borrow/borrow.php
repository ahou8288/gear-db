<h3>Borrow Gear</h3>

<div class="col-lg-12">
	<h5>Person Borrowing Gear</h5>
	<table id='personTable' class='table'></table>
</div>
<br/>

<div class="col-lg-12">
	<h5>Select your gear</h5>
	<table id='dataTable' class='table'></table>
</div>
<br/>

<form action='../borrow/save' method="post">
	<div class="form-group col-lg-12">
		<label for="sel1">Gear selected;</label>
		<div class="row">
			<div class="col-lg-8">
				<select class="form-control" multiple="multiple" id="sel1" height="8" data-bind="
					options:gear_list,
					selectedOptions:selectedItems,
					optionsText: function(item) {
						return item.name+' - '+item.age
					}">
				</select>
			</div>
			<div class="col-lg-4">
				<button class="btn btn-default" data-bind="click: removeSelected, enable: selectedItems().length > 0">Remove</button>
			</div>
		</div>

		<input type="hidden" name="gear_selected" data-bind="value: ko.toJSON(gear_list)" />
		<input type="hidden" name="person_borrowing" data-bind="value: ko.toJSON(selectedPerson)" />
		
		<label class="control-label" for="deposit">Deposit</label>
		<input type="text" class="form-control" name="deposit" id="deposit">

		<label class="control-label" for="comments">Comments</label>
		<textarea type="text" class="form-control" name="comments" id="comments" rows="5"></textarea> 

		<div class="form-group">
			<input type="submit" class="btn btn-default" data-bind="enable: gear_list().length>0 && selectedPerson() != undefined">
		</div>
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