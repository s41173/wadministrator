<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add New Slider </h4>
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

<form id="upload_form" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Name </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <textarea id="tname" name="tname" class="form-control" required placeholder="Name Desription"></textarea>
      </div>
    </div>

      <div class="form-group">
          <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Url </label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <input id="turl" class="form-control col-md-7 col-xs-12" type="text" name="turl" required placeholder="Url">
          </div>
      </div>
      
      <div class="form-group">
          <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Order </label>
          <div class="col-md-2 col-sm-2 col-xs-12">
            <input id="torder" class="form-control col-md-7 col-xs-12" type="number" name="torder" required placeholder="Order">
          </div>
      </div>
      
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Image </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="file" id="uploadImage" accept="image/*" class="input-medium" title="Upload" name="userfile" /> <br>
          <img id="catimg" style=" max-width:150px; height:auto;">
      </div>
      </div>

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" id="breset" class="btn btn-warning">Reset</button>
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