<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel"> Shipping - Update </h4>
        </div>
        
 <div class="modal-body"> 
 
 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>

 <!-- form edit -->
 <form id="upload_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
 action="<?php echo $form_action_update; ?>" enctype="multipart/form-data">
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Sales No </label>
      <div class="col-md-4 col-sm-5 col-xs-12">
        <input id="tsales" readonly class="form-control col-md-3 col-xs-12" type="text" name="tsales">
        <input id="tsales_date" readonly class="form-control col-md-3 col-xs-12" type="text" name="tsales_date">
        <input type="hidden" id="tid_update" name="tid_update">
      </div>
    </div>
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Courier </label>
      <div class="col-md-4 col-sm-5 col-xs-12">
        <input id="tcourier" readonly class="form-control col-md-3 col-xs-12" type="text" name="tcourier">
        <input id="tpackage" readonly class="form-control col-md-3 col-xs-12" type="text" name="tpackage">
      </div>
    </div> 
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Destination </label>
      <div class="col-md-5 col-sm-5 col-xs-12">
        <input id="tdesc" readonly class="form-control col-md-3 col-xs-12" type="text" name="tdesc">
        <input id="tdistrict" readonly class="form-control col-md-3 col-xs-12" type="text" name="tdesc">
      </div>
    </div>
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Address </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <textarea cols="55" name="taddress" id="taddress" rows="3"></textarea>
      </div>
    </div>
     
      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
          </div>
      </div>
  </form> 
  <!-- form edit -->
  
  </div>
 </div>
</div>