<!-- Display the page title -->
<h4><?php echo $data[0]['page_title'] ?></h4>
<br/>

<!-- Use a bootstrap accordian to hold all the tables. This is what enforces the property that when one table is selected the other tables close. -->
<div class="panel-group" id="tableAccordion">
	<?php 
		foreach($data as $num => $val){ // Create a dataTable inside an accordian for every set of data that is sent to the view.
			echo('<div class="panel panel-default">');
			echo('	<a class="col-xs-12" data-toggle="collapse" data-parent="#tableAccordion" href="#p'.$num.'">'); //Each accodian has ids p0, p1, p2..., this link is how the headings open the tables.
			echo('		<div class="panel-heading projHeading">');
			echo('			<div class="row">');
			//Print the title and subtitle that was sent to the view.
			echo('				<div class="col-lg-4"><span class="panel-title">'.$val['title'].'</span></div>');
			echo('				<div class="col-lg-8">'.$val['subtitle'].'</div>');
			echo('			</div>');
			echo('		</div>');
			echo('	</a>');
			echo('	<div class="panel-collapse collapse');
			if ($num==0) echo (' in'); //Make the first table open by default. (the "in" class opens the accordian)
			echo('" id="p'.$num.'">');
			echo('		<div class="panel-body">');
			echo('			<table id="dataTable'.$num.'" class="table"></table>'); //Apply a number to the datatable so that the js can reference it.
			echo('		</div>');
			echo('	</div>');
			echo('</div>');
		}
	?>
</div>

<script>
	// This script calls the javascript which sets up that dataTable and stuff
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>