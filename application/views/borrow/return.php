<h3>Return Gear</h3>

<h4>Gear still to be returned</h4>

<table id='dataTable' class='table'></table>

<h4>Gear already returned</h4>

<table id='dataTable2' class='table'></table>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>