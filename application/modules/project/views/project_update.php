<style type="text/css">@import url("<?php echo base_url() . 'css/style.css'; ?>");</style>
<style type="text/css">@import url("<?php echo base_url() . 'development-bundle/themes/base/ui.all.css'; ?>");</style>
<style type="text/css">@import url("<?php echo base_url() . 'css/jquery.fancybox-1.3.4.css'; ?>");</style>

<script type="text/javascript" src="<?php echo base_url();?>js/register.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.tools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo base_url();?> js/complete.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/sortir.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/validate.js"></script> 
<script type='text/javascript' src='<?php echo base_url();?>js/jquery.validate.js'></script>  

<script type="text/javascript">
var uri = "<?php echo site_url('ajax')."/"; ?>";
var baseuri = "<?php echo base_url(); ?>";
</script>


<div id="webadmin">

	<div class="title"> <?php $flashmessage = $this->session->flashdata('message'); ?> </div>
	<p class="message"> <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> </p>
	
	<div id="errorbox" class="errorbox"> <?php echo validation_errors(); ?> </div>
	
	<fieldset class="field"> <legend> Project - Update </legend>
	<form name="admin_form" id="form" class="myform" method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
		<table>
			<tr> <td><label for="tname">Name</label></td> <td>:</td> 
			<td><input type="text" class="required" name="tname" size="35" title="Category name" 
			value="<?php echo set_value('tname', isset($default['name']) ? $default['name'] : ''); ?>" /> <br /> </td> </tr>
			
			<tr> <td><label for="cparent">Parent Category </label></td> <td>:</td> 
			  <td>  
			    <?php $js = 'size="10", class="required"';  echo form_dropdown('cparent', $category, isset($default['parent']) ? $default['parent'] : '', $js); ?> 
			  </td> 
		   </tr>
		   
		   <tr> <td><label for="tdesc">Description</label></td> <td>:</td> <td> <textarea name="tdesc" title="Product Description" cols="25" rows="2"><?php echo set_value('tdesc', isset($default['desc']) ? $default['desc'] : ''); ?></textarea> <br/> </td> </tr>					
		</table>				  
	<p>
		<input type="submit" name="submit" class="button" title="Klik tombol untuk proses data" value=" Save " />
		<input type="reset" name="reset" class="button" title="Klik tombol untuk proses data" value=" Cancel " />
	</p>
  </form>
  </fieldset>  
</div>
