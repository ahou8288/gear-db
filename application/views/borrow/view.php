<h3>Borrowed Gear Display</h3>
<h4>Currently borrowed gear</h4>
<table id='dataTable' class='table'></table>
<h4>Previously returned gear</h4>
<table id='dataTable2' class='table'></table>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>