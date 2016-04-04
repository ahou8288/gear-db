<h4>Return Gear</h4>
<h5>Click on gear to return it</h5>
<table id='dataTable' class='table'></table>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>