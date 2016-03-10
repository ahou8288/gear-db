<h4>Please select the dates you require</h4>

<form role="form" action='<?= base_url();?>Timesheets/request' method="post">
  <div class="row">
    <div class="form-group col-md-4">
      <label for="startDate">Start Date:</label>
      <input type="text" class="form-control datepicker" id="startDate" name="startDate" data-bind='value: startDate'>
    </div>
  </div>

  <div class="row">
    <div class="alert alert-warning" role="alert" data-bind='visible: checkNotMonday'>Warning: You have not selected a monday</div>
  </div>

  <div class="row">
    <div class="form-group col-md-4">
      <label for="finishDate">Finish Date:</label>
      <input type="text" class="form-control datepicker" id="finishDate" name="finishDate" data-bind='value: finishDate'>
    </div>
  </div>

  <div class="row">
    <div class="alert alert-warning" role="alert" data-bind='visible: checkTooLong'>Warning: You have selected more than one week</div>
    <div class="alert alert-danger" role="alert" data-bind='visible: checkInvalidFinish'>Error: You have selected a finish date before the start date</div>
  </div>

  <button type="submit" name="format" value="view"class="btn btn-primary" data-bind='disable: checkInvalidFinish'>View</button>
  <button type="submit" name="format" value="download"class="btn btn-info" data-bind='disable: checkInvalidFinish'>Download</button>
</form>
<script>
  $(document).ready(function() {
    model = new ViewModel();
    ko.applyBindings(model);
    model.init()
  })
</script>