
 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<!-- Date time picker -->
 <script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


<style type="text/css">
  a:hover { text-decoration:none;}
</style>

<script src="<?php echo base_url(); ?>js/moduljs/product.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('configuration/add_process/');?>";
	var sites_edit = "<?php echo site_url('configuration/update_process/');?>";
	var sites_del  = "<?php echo site_url('configuration/delete/');?>";
	var sites_get  = "<?php echo site_url('configuration/update/');?>";
	var source = "<?php echo $source;?>";
    var url  = "<?php echo $graph;?>";
	
</script>

          <div class="row"> 
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel" >
              
              <!-- xtitle -->
              <div class="x_title">
              
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a> </li>
                </ul>
                
                <div class="clearfix"></div>
              </div>
              <!-- xtitle -->
                
                <div class="x_content">
                      
                  
             <!-- Smart Wizard -->
<div id="wizard" class="form_wizard wizard_horizontal">
  
  <ul class="wizard_steps">
    <li>
      <a href="#step-1">
        <span class="step_no">1</span>
        <span class="step_descr"> <small> General </small> </span>
      </a>
    </li>
   
  </ul>
  
  <div id="errors" class="alert alert-danger alert-dismissible fade in" role="alert"> 
     <?php $flashmessage = $this->session->flashdata('message'); ?> 
	 <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> 
  </div>
  
  <div id="step-1">
    <!-- form -->
    <form id="supload_form_parent" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action.'/1'; ?>" 
      enctype="multipart/form-data">
		
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> SKU </label>
	  <div class="col-md-6 col-sm-6 col-xs-12">
        <input type="text" class="form-control" id="tsku" required name="tsku" placeholder="SKU" 
        value="<?php echo isset($default['sku']) ? $default['sku'] : '' ?>">
      </div>
      </div>
	
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name"> Category </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
         <?php $js = "class='form-control' id='ccategory' tabindex='-1' style='min-width:150px;' "; 
	     echo form_dropdown('ccategory', $category, isset($default['category']) ? $default['category'] : '', $js); ?>
        </div>
      </div>
      
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> Model </label>
        <div class="col-md-3 col-sm-6 col-xs-12">
        <?php $js = "class='form-control' id='cmodel' tabindex='-1' style='min-width:150px;' "; 
	    echo form_dropdown('cmodel', $model, isset($default['model']) ? $default['model'] : '', $js); ?>
        </div>
      </div>
            
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Name </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
  	       <input type="text" title="Product Name" class="form-control" id="tname" name="tname" required placeholder=""
           value="<?php echo isset($default['name']) ? $default['name'] : '' ?>" />
        </div>
      </div>
        
      <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Color </label>
                    <div class="col-md-4 col-sm-3 col-xs-12">
       <?php $js = "class='select2_multiple form-control' id='ccolor' multiple='multiple' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('ccolor[]', $color, $array, $js); ?>              
                    </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Flat-Price </label>  
          <div class="col-md-6 col-sm-12 col-xs-12 form-group">
              <?php $js = "class='flat'"; echo form_checkbox('cflat', 1, set_value('cflat', isset($default['flat']) ? $default['flat'] : 'FALSE'), $js); ?> 
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Accessories </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
              <input type="number" name="tbone" class="form-control" style="width:80px;" value="<?php echo isset($default['bone']) ? $default['bone'] : '' ?>">
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Sash - Active Sash </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
            <table>
                <tr> 
                    <td> <input type="number" name="tsash" class="form-control" style="width:80px;" value="<?php echo isset($default['sash']) ? $default['sash'] : '' ?>">
                    </td> 
                    <td> <input type="number" name="tactivesash" class="form-control" style="width:80px;" value="<?php echo isset($default['activesash']) ? $default['activesash'] : '' ?>">
                    </td> 
                </tr>
            </table>
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Top Fixed Glass </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
              <input type="number" name="tkacamati" class="form-control" style="width:80px;" value="<?php echo isset($default['fixedglass']) ? $default['fixedglass'] : '' ?>">
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Bottom Fixed Glass </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
              <input type="number" name="tkacamatibawah" class="form-control" style="width:80px;" value="<?php echo isset($default['fixedglassbottom']) ? $default['fixedglassbottom'] : '' ?>">
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Sash Bone </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
              <input type="text" name="ttulangdaun" class="form-control" style="width:80px;" value="<?php echo isset($default['tulangdaun']) ? $default['tulangdaun'] : '0' ?>">
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Panel </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
<input type="text" name="tpanel" class="form-control" style="width:80px;" value="<?php echo isset($default['panel']) ? $default['panel'] : '0' ?>">
          </div>
      </div>
        
      <div class="form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Weight - Unit m<sup>3</sup> </label>  
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
<input type="text" name="tweight" class="form-control" style="width:80px;" value="<?php echo isset($default['weight']) ? $default['weight'] : '0' ?>">
          </div>
      </div>
      
      <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Image </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="file" id="uploadImage" accept="image/*" class="input-medium" title="Upload" name="userfile" /> <br>
          <img id="catimg" style=" max-width:100px; height:auto;" src="<?php echo isset($default['image']) ? $default['image'] : '' ?>">
      </div>
      </div>   
      
      <div class="form-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php echo $this->ckeditor->editor('tdesc', isset($default['description']) ? $default['description'] : '');?> 
        </div>
      </div>
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button"> Save General </button>
        </div>
      </div>
      
	</form>
    <!-- end div layer 1 -->
  </div>
  
</div>
<!-- End SmartWizard Content -->
                      
     </div>
       
       <!-- links -->
       <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
       <!-- links -->
                     
    </div>
  </div>
      
      <script src="<?php echo base_url(); ?>js/icheck/icheck.min.js"></script>
      
       <!-- Datatables JS -->
        <script src="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/jszip.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/pdfmake.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/vfs_fonts.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.scroller.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.js"></script>
    
    <!-- jQuery Smart Wizard -->
    <script src="<?php echo base_url(); ?>js/wizard/jquery.smartWizard.js"></script>
        
        <!-- jQuery Smart Wizard -->
    <script>
      $(document).ready(function() {
        $('#wizard').smartWizard();

        $('#wizard_verticle').smartWizard({
          transitionEffect: 'slide'
        });

      });
    </script>
    <!-- /jQuery Smart Wizard -->
    
    
