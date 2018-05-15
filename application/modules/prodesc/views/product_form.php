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
<script type="text/javascript" src="<?php echo base_url();?>js/complete.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/sortir.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/validate.js"></script> 
<script type='text/javascript' src='<?php echo base_url();?>js/jquery.validate.js'></script>  

<script type="text/javascript">
var uri = "<?php echo site_url('ajax')."/"; ?>";
var baseuri = "<?php echo base_url(); ?>";
</script>

<style>
        .refresh{ border:1px solid #AAAAAA; color:#000; padding:2px 5px 2px 5px; margin:0px 2px 0px 2px; background-color:#FFF;}
		.refresh:hover{ background-color:#CCCCCC; color: #FF0000;}
		.refresh:visited{ background-color:#FFF; color: #000000;}	
</style>

<?php 
		
$atts1 = array(
	  'class'      => 'refresh',
	  'title'      => 'add cust',
	  'width'      => '600',
	  'height'     => '400',
	  'scrollbars' => 'yes',
	  'status'     => 'yes',
	  'resizable'  => 'yes',
	  'screenx'    =>  '\'+((parseInt(screen.width) - 600)/2)+\'',
	  'screeny'    =>  '\'+((parseInt(screen.height) - 400)/2)+\'',
);

?>

<script type="text/javascript">	
	function refreshparent() { opener.location.reload(true); }
</script>

<body onUnload="refreshparent()">
<div id="webadmin">
	<div class="title"> <?php $flashmessage = $this->session->flashdata('message'); ?> </div>
	<p class="message"> <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> </p>
	
	<div id="errorbox" class="errorbox"> <?php echo validation_errors(); ?> </div>
	
	<fieldset class="field"> <legend> Inventory </legend>
	<form name="modul_form" class="myform" id="form" method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data" >
				<table>
			
			<tr>	
			<td> <label for="ccategory"> Category (*) </label> </td> <td>:</td>
			<td> <?php $js = 'class="required"'; echo form_dropdown('ccategory', $category, isset($default['category']) ? $default['category'] : '', $js); ?> &nbsp; <br /> </td>
			</tr>
						
			<tr> <td> <label for="tname"> Model / Name (*)</label></td> <td>:</td> 
			<td> <input type="text" class="required" name="tname" size="45" title="Product Model" /> <br /> </td> </tr>
			
			<tr> <td> <label for="tshortdesc"> Short Description </label></td> <td>:</td> 
			<td> <textarea name="tshortdesc" cols="30" rows="2"></textarea> <br /> </td> </tr>
			
			<tr> <td> <label for="tdesc"> Description </label> </td> <td>:</td> 
   	             <td> <textarea name="tdesc" class="required" id="content" >  </textarea> <?php echo display_ckeditor($ckeditor); ?> <br />
	             </td>
			</tr>	
	
	
			<tr> <td> <label for="tprice"> Price </label></td> <td>:</td> 
			<td> <input type="text" class="required" id="tprice" name="tprice" size="10" title="Price" onKeyUp="checkdigit(this.value, 'tprice')" /> <br /> </td> </tr>
			
			<tr> <td> <label> Publish </label> </td> <td>:</td> 
				 <td> Y <input name="rpublish" type="radio" class="required" value="1" />  
				      N <input name="rpublish" type="radio" class="required" value="0" /> <br/>
				 </td> 
			</tr>
			
			<tr> <td> <span class="label"> Image </span> </td> <td>:</td> 
				<td> <input type="file" size="30" title="Upload image" name="userfile"  /> <br /> <?php echo isset($error) ? $error : '';?> </td> </tr>
			
			<tr> <td> <label for="turl1"> Image Url 1 </label></td> <td>:</td> 
			<td> <textarea name="turl1" cols="35" rows="3"></textarea> <br /> </td> </tr>
			
			<tr> <td> <label for="turl2"> Image Url 2 </label></td> <td>:</td> 
			<td> <textarea name="turl2" cols="35" rows="3"></textarea> <br /> </td> </tr>
			
			<tr> <td> <label for="turl3"> Image Url 3 </label></td> <td>:</td> 
			<td> <textarea name="turl3" cols="35" rows="3"></textarea> <br /> </td> </tr>
										   
				</table>
				<p style="margin:15px 0 0 0; float:right;">
					<input type="submit" name="submit" class="button" value=" Save " /> 
					<input type="reset" name="reset" class="button" value=" Cancel " />
				</p>	
			</form>			  
	</fieldset>
</div>
</body>
