<h3>Return Gear</h3>
<form action='../save_return' method="post">
	<div class="form-group">
		<input type="submit" class="btn btn-info" data-bind="enable: selectedGear().length>0" value="Return Selected Gear">
	</div>

	<h4>Gear still to be returned</h4>
	<table id='dataTable' class='table'></table>

	<input type="hidden" name="selectedGear" data-bind="value: ko.toJSON(selectedGear)" />

	<h4>Gear already returned</h4>
	<table id='dataTable2' class='table'></table>


	<div class="form-group">
		<input type="submit" class="btn btn-info" data-bind="enable: selectedGear().length>0" value="Return Selected Gear">
	</div>
</form>
<script>
	// This script calls the javascript which sets up that dataTable and stuff
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);
    model.init();
  })
</script>

<style type="text/css">
	tr.row_selected td{background-color:#ccccff !important;}
</style>