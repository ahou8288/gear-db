<?php echo link_tag('includes/css/view.css'); ?>

<h4>Pacific Highway Progress</h4>

<div class="panel-group" id="accordionProj">
  <!-- ko foreach: projects -->
  <div class="panel panel-default">
    <div class="panel-heading projHeading">
      <h2 class="panel-title">
        <div class="row">
          <a class='col-xs-6' data-toggle="collapse" data-parent="#accordionProj" data-bind="text: name, attr: {href: '#collapseProject'+$index()}"></a>
          <div class='col-xs-6'>
            <div class="progress">
              <div class="progress-bar" role="progressbar"  data-bind='style: {width: Percent() + "%"}, css: "progress-bar-" + Colour()'></div>
            </div>
          </div>
        </div>
      </h2>
    </div>
    <div class="panel-collapse collapse" data-bind="attr: {id: 'collapseProject'+$index()}">
      <h5 data-bind="text: 'Project Total: Spent $'+Total()+' of $'+Budget()"></h5>

      <div class="panel-body" data-bind='foreach: children'>
    		<div class="panel-group">
    		  <div class="panel panel-default">
    		    <div class="panel-heading taskHeading">
    		      <h4 class="panel-title">
    		        <div class="row">
    		          <a class='col-xs-6' data-toggle="collapse" data-bind="text: name, attr: {href: '#collapseProejct'+$parentContext.$index() +'Task'+$index()}"></a>
    		    	     <span class='col-xs-6'>
                	  <div class="progress">
                	    <div class="progress-bar" role="progressbar"  data-bind='style: {width: Percent() + "%"}, css: "progress-bar-" + Colour()'></div>
                 	  </div>
              	  </span>
              	</div>
    		      </h4>
    		    </div>
    		    <div class="panel-collapse collapse" data-bind="attr: {id: 'collapseProejct'+$parentContext.$index() +'Task'+$index()}">
    		   	  <div class="row">
    		        <h5 class='col-xs-8' data-bind="text: 'Task Total: Spent $'+Actual()+' of $'+Budget()"></h5><h5 class='col-xs-2'>Actual</h5><h5 class='col-xs-2'>Budgeted</h5>
    		      </div>
    		      <div class="panel-body" data-bind='foreach: children'>
    		        <div class='row'>
    		        	<p class='col-xs-4' data-bind='text: name'></p>
    		      	   <span class='col-xs-4'>
      			       	 <div class="progress">
    					        <div role="progressbar" data-bind='text: PercentText(), style: {width: Percent() + "%"}, css: "progress-bar-" + Colour()'></div>
    					      </div>
  				        </span>
    				      <div class='col-xs-2' data-bind='text: Actual'></div>
    				      <div class='col-xs-2' data-bind='text: Budgeted'></div>
    		        </div>
    		      </div>
    		    </div>
    		  </div>
    		</div>
      </div>
      
    </div>
  </div>
  <!-- /ko -->
</div>

<script>
  $(document).ready(function() {
    model = new ViewModel(<?php echo json_encode($data) ?>);
    ko.applyBindings(model);
    model.init();
  })
</script>