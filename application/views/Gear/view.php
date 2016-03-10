<h4>Gear Display</h4>

<table id='dataTable' class='table'></table>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>