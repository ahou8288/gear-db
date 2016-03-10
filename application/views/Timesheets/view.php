<h4>Timesheet details</h4>

<a class="btn btn-info" href='<?=base_url()?>/Timesheets/download/<?=$data['startDate']?>/<?=$data['finishDate']?>' >Download</a>

<table id='dataTable' class='table'>

</table>


<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);

    model.init();
  })
</script>