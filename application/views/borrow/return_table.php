<h4>Search for the gear you want to return Gear</h4>
<h5>Click on gear to return it</h5>
<table id='dataTable' class='table'>
	

        <tfoot>
            <tr>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Name"></th>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Position"></th>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Office"></th>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Age"></th>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Start date"></th>
            	<th rowspan="1" colspan="1"><input type="text" placeholder="Search Salary"></th></tr>
        </tfoot>
</table>

<script>
	// This script calls the javascript which sets up that dataTable and stuff
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>