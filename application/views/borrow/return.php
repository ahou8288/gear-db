<h3>Return Gear</h3>
<form action='../borrow/save_return' method="post">
	<h4>Gear still to be returned</h4>
	<table id='dataTable' class='table'></table>

	<input type="hidden" name="returnGear" data-bind="value: ko.toJSON(selectedGear)" />

	<div class="form-group">
		<input type="submit" class="btn btn-default" data-bind="enable: selectedGear().length>0">
	</div>
</form>

<h4>Gear already returned</h4>
<table id='dataTable2' class='table'></table>

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