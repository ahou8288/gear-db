<h4>Search for the gear you want to return</h4>
<h5>Click on gear to return it</h5>
<table id='dataTable' class='table'>
	<tfoot>
		<tr>
			<?php 
				foreach ($data['Fields'] as $index => $field){
					echo('<th>');					
					echo('</th>');
				}
			?>
		</tr>
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