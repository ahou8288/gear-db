<h4>New Gear</h4>

<form role="form"  action='../gear/save' method="post" class="form-horizontal">
	<?php 
		foreach ($data['fields_list'] as $index => $val){
			echo '<div class="form-group">';
				echo '<label class="control-label" for='. $data['fields_list'][$index]['Field'] .'>'.$data['fields_list'][$index]['DisplayName'].'</label>';
				echo '<input class="form-control" id='. $data['fields_list'][$index]['Field'] .' name='. $data['fields_list'][$index]['Field'] .'>';
			echo '</div>';
		}
	?>
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