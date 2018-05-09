<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Sales Confirmation </h4>
</div>
<div class="modal-body">
    
 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_title">
  
  <div class="clearfix"></div> 
</div>
<div class="x_content">

<form id="upload_form" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo site_url("commision/add_process"); ?>" enctype="multipart/form-data">
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Sales Order </label>
      <div class="col-md-3 col-sm-12 col-xs-12">
  <input id="tsalesorder" class="form-control col-md-7 col-xs-12" type="text" name="tsalesorder" value="<?php echo $salescode; ?>" readonly>
      </div> 
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Phase </label>
      <div class="col-md-2 col-sm-12 col-xs-12">
          <select name="cphase" id="cphase" class="form-control">
              <option value="1"> I </option>
              <option value="2"> II </option>
              <option value="3"> III </option>
              <option value="4"> IV </option>
              <option value="5"> V </option>
          </select>
      </div>
      </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Confirm Date </label>
      <div class="col-md-4 col-sm-12 col-xs-12">
  <input id="ds1" class="form-control col-md-7 col-xs-12" type="text" name="tcdates" readonly required>
  <input type="hidden" name="tsid" value="<?php echo $sid; ?>">
      </div> 
    </div>

      <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> Amount <span class="required">*</span></label>
          <div class="col-md-4 col-sm-6 col-xs-12">
 <input type="number" id="tamount" class="form-control col-md-3 col-xs-12" type="text" name="tamount" required placeholder="Amount">   
          </div>
      </div>

      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Source Bank </label>
      <div class="col-md-8 col-sm-12 col-xs-12">
          <?php $js = "class='form-control' id='cbank' tabindex='-1' style='min-width:150px;' "; 
	      echo form_dropdown('cbank', $bank, isset($default['bank']) ? $default['bank'] : '', $js); ?>
      </div>
      </div>
    
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Payment Type </label>
      <div class="col-md-4 col-sm-12 col-xs-12">
          <?php $js = "class='form-control' id='cpayment' tabindex='-1' style='min-width:150px;' "; 
	      echo form_dropdown('cpayment', $payment, isset($default['payment']) ? $default['payment'] : '', $js); ?>
      </div>
      </div>
      
      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button"> Save </button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal"> Close </button>
          </div>
      </div>
  </form> 

</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer"> </div>
</div>
  
</div>