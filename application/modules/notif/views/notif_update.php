<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add Notif </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >

<div class="x_content">

<?php
    
$atts1 = array(
	  'class'      => 'btn btn-primary button_inline',
	  'title'      => 'Customer - List',
	  'width'      => '800',
	  'height'     => '600',
	  'scrollbars' => 'yes',
	  'status'     => 'yes',
	  'resizable'  => 'yes',
	  'screenx'    =>  '\'+((parseInt(screen.width) - 800)/2)+\'',
	  'screeny'    =>  '\'+((parseInt(screen.height) - 600)/2)+\'',
);

?>

<form id="edit_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action_update; ?>" enctype="multipart/form-data">
   			
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" name="tcust" id="ccust2_update" readonly style="width:80px; float:left;">
        <?php echo anchor_popup(site_url("customer/get_list/ccust2/"), '[ ... ]', $atts1); ?> &nbsp;
      </div>

      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
        
        <select name="ctype" id="ctype_update" class="form-control" style="min-width:150px;">
           <option value="0"> Email </option>
           <option value="1"> SMS </option>
           <option value="2"> Email SMS </option>
           <option value="3"> Socket </option>
           <option value="4"> SMS + Socket </option>
           <option value="5"> Email + Socket </option>
           <option value="6"> Email SMS Notif Socket </option>
        </select>
        
      </div>
      
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
        <input type="text" id="tcustomer" class="form-control" readonly style="width:100%;">
      </div>
      
       <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control" name="tsubject" id="tsubject_update" placeholder="Subject">
      </div>
    
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
<textarea class="form-control" name="tcontent" id="tcontent_update" style="width:100%;" rows="5" placeholder="Notif Content"></textarea>
      </div>
    
      <!-- pembatas div -->
      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback"> </div>
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