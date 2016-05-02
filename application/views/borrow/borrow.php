<h3>Borrow Gear</h3>

<form action='../borrow/save' method="post">
	<div class="form-group col-lg-12">
		
		<div class="col-lg-12">
			<h5>Person Borrowing Gear</h5>
				<table id='personTable' class='table'><tfoot>
					<tr>
						<?php 
							foreach ($data['person_fields'] as $index => $field){
								echo('<th>');					
								echo('</th>');
							}
						?>
					</tr>
				</tfoot></table>
		</div>
		
		<h5>Select your gear</h5>
		<div class="row">
			<div class="col-lg-8">
					<table id='dataTable' class='table'><tfoot>
						<tr>
							<?php 
								foreach ($data['gear_fields'] as $index => $field){
									echo('<th>');					
									echo('</th>');
								}
							?>
						</tr>
					</tfoot></table>
			</div>

			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Gear selected;</h3>
					</div>
					<div class="panel-body">
				<!-- The data-bind tag in the next line is a reference to knockout js. It updates the contents of the select box. -->
						<span data-bind="foreach: gear_list">
							<h5 data-bind="text:$data.cat+' - '+$data.name"></h5>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<label class="control-label" for="deposit">Deposit</label>
			<input type="text" placeholder="type deposit amount here eg. $20" class="form-control" name="deposit" id="deposit" required>

			<label class="control-label" for="comments">Comments</label>
			<textarea type="text" placeholder="eg. Beginner trad trip"  class="form-control" name="comments" id="comments" rows="5"></textarea> 
		</div>

		<!-- This is how data on what is selected is sent back to the controller -->
		<input type="hidden" name="gear_selected" data-bind="value: ko.toJSON(gear_list)" />
		<input type="hidden" name="person_borrowing" data-bind="value: ko.toJSON(selectedPerson)" />
		



		<div class="form-group">
			<input type="submit" class="btn btn-success" data-bind="enable: gear_list().length>0 && selectedPerson() != undefined">
		</div>
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
	/* Blue row highlight applied to selected rows */
	tr.row_selected td{background-color:#ccccff !important;}
</style>