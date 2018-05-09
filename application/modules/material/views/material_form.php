<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add New Material </h4>
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

<form id="upload_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action; ?>" >
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Name </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <input id="tname" class="form-control col-md-7 col-xs-12" type="text" name="tname" required placeholder="Material Name">
      </div>
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Model </label>
      <div class="col-md-9 col-sm-6 col-xs-12">
        <?php $js = "class='form-control' id='' tabindex='-1' style='width:150px;' "; 
        echo form_dropdown('cmodel', $model, isset($default['model']) ? $default['model'] : '', $js); ?>
      </div>
    </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Material </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
       <?php $js = "class='form-control' id='' tabindex='-1' style='width:250px;' "; 
       echo form_dropdown('cmaterial', $material, isset($default['material']) ? $default['material'] : '', $js); ?>
      </div>
    </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Color </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
       <?php $js = "class='select2_single form-control' id='' tabindex='-1' style='width:250px;' "; 
       echo form_dropdown('ccolor', $color, isset($default['color']) ? $default['color'] : '', $js); ?>
      </div>
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
      <div class="col-md-5 col-sm-6 col-xs-12">
       <select name="ctype" class="select2_single form-control" style="width:150px;">
           <option value="SINGLE"> SINGLE </option>
           <option value="DOUBLE"> DOUBLE </option>
           <option value="TRIPLE"> TRIPLE </option>
       </select>
      </div>
    </div>
    
    <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12"> Glass </label>  
      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
          <input type="checkbox" name="cglass" class="flat" id="" value="1">
      </div>
   </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Thickness (mm) </label>
      <div class="col-md-3 col-sm-6 col-xs-12">
       <input type="number" name="tweight" class="form-control" value="0" step="0.01">
      </div>
    </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Price / M </label>
      <div class="col-md-3 col-sm-6 col-xs-12">
       <input type="number" name="tprice" class="form-control" value="0">
      </div>
    </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Price Group </label>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <select name="cgroup" class="form-control" required title="Agent Group">
          <option value="1"> Group 1 </option>
          <option value="2"> Group 2 </option>
          <option value="3"> Group 3 </option>
          <option value="4"> Group 4 </option>
          <option value="5"> Group 5 </option>
        </select>
      </div>
    </div>  

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 btn-group">
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