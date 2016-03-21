<h4>New Gear</h4>

<!-- ko foreach: fields_list()[0] -->
	<strong data-bind="text: fields_list()[0][0]">sS</strong>
<!-- /ko -->
<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>