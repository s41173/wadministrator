<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Sales Confirmation </h4>
</div>
<div class="modal-body">
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_title">
  
  <div class="clearfix"></div> 
</div>
<div class="x_content">

<form id="ajaxform" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo site_url("sales_payment/confirmation"); ?>" 
      enctype="multipart/form-data">
    
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
  <input id="dtime1" class="form-control col-md-7 col-xs-12" type="text" name="tcdates" required readonly>
      </div> 
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> ACC-Name </label>
      <div class="col-md-6 col-sm-12 col-xs-12">
  <input id="taccname" class="form-control col-md-7 col-xs-12" type="text" name="taccname" required placeholder="Acc Name">
      </div>
    </div>

      <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> ACC-No <span class="required">*</span></label>
          <div class="col-md-6 col-sm-12 col-xs-12">
  <input id="taccno" class="form-control col-md-7 col-xs-12" type="text" name="taccno" required placeholder="Acc No">
          </div>
      </div>
    
      <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> ACC-Bank <span class="required">*</span></label>
          <div class="col-md-6 col-sm-6 col-xs-12">
 <input id="taccbank" class="form-control col-md-7 col-xs-12" type="text" name="taccbank" required placeholder="Bank">   
          </div>
      </div>
    
      <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> Amount <span class="required">*</span></label>
          <div class="col-md-4 col-sm-6 col-xs-12">
 <input type="number" id="tamount" class="form-control col-md-3 col-xs-12" type="text" name="tamount" required placeholder="Amount">   
          </div>
      </div>

      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Merchant Bank </label>
      <div class="col-md-8 col-sm-12 col-xs-12">
          <?php $js = "class='form-control' id='cbank' tabindex='-1' style='min-width:150px;' "; 
	      echo form_dropdown('cbank', $bank, isset($default['bank']) ? $default['bank'] : '', $js); ?>
      </div>
      </div>
      
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Status </label>
      <div class="col-md-4 col-sm-12 col-xs-12">
          <select name="cstts" id="cstts" class="form-control">
              <option value="1"> Confirm </option>
              <option value="0"> Unconfirm </option>
          </select>
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