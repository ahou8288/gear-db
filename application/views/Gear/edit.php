<!-- Display the page title -->
<h4><?php echo $data['title'] ?></h4>

<form role="form"  action='<?php echo $data['url']?>' method="post" class="form-horizontal">
	<div class="form-group" data-bind="foreach: fields_list"> 
	<!-- The data-bind foreach is a loop structure, it generates the elements inside the div for every element in fields_list -->
	<!-- Inside the foreach binding each element of fieldslist can be accessed as $data, and fieldslist can be accesed as $parent
	The index of the loop is $index. See knockout documentation for more info. -->

		<!-- Print the label for the item -->
		<label class="control-label" data-bind="text:$data['display'], attr: {for: $data['name'] }"></label>
		<!-- ko if: $data['radio']() == 0 -->
			<!-- Print the text input -->
			<input class="form-control" type="text" data-bind="value:$data['value'] ,attr: {id: $data['name'], name: $data['name'] }">
		<!-- /ko -->
		<!-- ko if: $data['radio']() == 1 -->
			<!-- Print the radio buttons. -->
			<br/>
			<div data-bind="foreach: $data['options']">
				<label class="radio-inline">
					<input type="radio" data-bind="value: $data[0], attr: {id: $parent['name'],name: $parent['post_name'] }">
					<span data-bind="text: $data[1]"></span>
				</label>
			</div>
		<!-- /ko -->
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-default">
	</div>
</form>

<h6>Note: for large edits use phpMyAdmin, look in the website setup to find it.</h6>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>