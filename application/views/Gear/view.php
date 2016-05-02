<!-- Display the page title -->
<h4><?php echo $data['title'] ?></h4>
<h5><?php echo $data['subtitle'] ?></h5>
<br/>

<table id="dataTable" class="table">
	<tfoot>
		<tr>
			<?php 
				foreach ($data['fields'] as $index => $field){
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