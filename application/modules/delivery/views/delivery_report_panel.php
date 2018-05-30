<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Shipping Report </h4>
</div>
<div class="modal-body">
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_title">
  
  <div class="clearfix"></div> 
</div>
<div class="x_content">

<form id="" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
action="<?php echo $form_action_report; ?>" enctype="multipart/form-data">
        
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Deliver Period </label>
        <div class="col-md-9 col-sm-9 col-xs-12">     
<input type="text" readonly style="width: 200px" name="deliveryperiod" id="d2" class="form-control active" value=""> 
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Status </label>
        <div class="col-md-3 col-sm-12 col-xs-12">     
<select name="cpaid" class="form-control">
    <option value=""> -- </option>
    <option value="1"> Paid </option>
    <option value="0"> Unpaid </option>
</select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
        <div class="col-md-3 col-sm-9 col-xs-12">     
			<select name="ctype" class="form-control">
              <option value="0"> Summary </option>
              <option value="1"> Pivottable </option>
            </select>
        </div>
    </div>

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary">Preview</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
      </div>
  </form> 
  <div id="err"></div>
</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer">
      
    </div>
  </div>
  
</div>