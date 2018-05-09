<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add New Agent </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >

<div class="x_content">

<form id="upload_form" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
   			
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control has-feedback-left" id="tcode" name="tcode" placeholder="Agent Code" value="<?php echo $code; ?>">
        <span class="fa fa-book form-control-feedback left" aria-hidden="true"></span> 
      </div>

      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" id="tname" name="tname" placeholder="Agent Name">
        <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span> 
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="email" class="form-control has-feedback-left" id="temail" name="temail" placeholder="Email">
        <span class="fa fa-envelope form-control-feedback left" aria-hidden="true"></span> 
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <select name="ctype" class="form-control" required>
          <option value=""> -- Agent Type -- </option>
          <option value="store"> Material Store </option>
          <option value="contractor"> Contractor </option>
        </select>
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control has-feedback-left" id="tphone1" name="tphone1" placeholder="Phone 1">
        <span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span> 
      </div>

      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" id="tphone2" name="tphone2" placeholder="Phone 2">
        <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span> 
      </div>
      
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
        <textarea name="taddress" id="taddress" class="form-control" placeholder="Address"></textarea>
          <input type="checkbox" id="cshipbox" name="cshipbox" checked value="1">
          <small style="color:#2A3F54;"> *) Use this address for shipping </small>
      </div>
    
      <!-- pembatas div -->
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
      </div>
       <!-- pembatas div -->
        
     <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
<?php $js = "class='select2_single form-control' placeholder='Select City' id='ccity' tabindex='-1' style='width:100%;' "; 
echo form_dropdown('ccity', $city, isset($default['city']) ? $default['city'] : '', $js); ?>
     </div>
    
     <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
        <div class="select_box"></div>
     </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control has-feedback-left" id="tzip" name="tzip" placeholder="ZIP">
        <span class="fa fa-file-archive-o form-control-feedback left" aria-hidden="true"></span> 
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" id="twebsite" name="twebsite" placeholder="Website">
        <span class="fa fa-internet-explorer form-control-feedback right" aria-hidden="true"></span> 
      </div>
    
<!-- account -->
    
      <div class="col-md-12 col-sm-6 col-xs-12 form-group has-feedback">
        <label> Bank Account : </label>
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control has-feedback-left" id="taccno" name="taccno" placeholder="Acc-No">
        <span class="fa fa-book form-control-feedback left" aria-hidden="true"></span> 
      </div>
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" id="taccname" name="taccname" placeholder="Acc-Name">
        <span class="fa fa-book form-control-feedback right" aria-hidden="true"></span> 
      </div>
    
      <div class="col-md-12 col-sm-6 col-xs-12 form-group has-feedback">
        <textarea name="tbank" id="tbank" class="form-control" placeholder="Bank Details"></textarea>
      </div>
    
<!-- account -->
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <select name="cgroup" class="form-control" required title="Agent Group">
          <option value=""> -- Group -- </option>
          <option value="1"> Group 1 </option>
          <option value="2"> Group 2 </option>
          <option value="3"> Group 3 </option>
          <option value="4"> Group 4 </option>
          <option value="5"> Group 5 </option>
        </select>
      </div>    
    
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="file" id="uploadImage" accept="image/*" class="input-medium" title="Upload" name="userfile" /> <br>
        <img id="catimg" style=" max-width:50px; height:auto;">
      </div>
          
      <!-- pembatas div -->
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
          
          
      </div>
       <!-- pembatas div --> 

      <div class="clearfix"></div> 
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="reset" id="breset" class="btn btn-warning">Reset</button>
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