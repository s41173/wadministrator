<div id="webadmin">

	<div class="title"> <?php $flashmessage = $this->session->flashdata('message'); ?> </div>
	<p class="message"> <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> </p>
	
	<div id="errorbox" class="errorbox"> <?php echo validation_errors(); ?> </div>
	
	<fieldset class="field"> <legend> Add Project </legend>
	<form name="admin_form" id="sajaxform" class="myform" method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
		<table>
			<tr> 
			<td> <span class="label"> Name </span> </td> <td>: &nbsp;</td> 
			<td>
			    <input type="text" class="required" name="tname" size="35" title="Category name" 
			    value="<?php echo set_value('tname', isset($default['name']) ? $default['name'] : ''); ?>" required /> <br />
			</td>
			</tr>
			
		 <tr> <td><span class="label"> Description </span></td> <td>:</td> 
		 <td> <textarea name="tlocation" title="Location" cols="25" rows="2"><?php echo set_value('tlocation', isset($default['location']) ? $default['location'] : ''); ?></textarea> <br/> </td> </tr>	
		   
		   <tr> <td> <span class="label"> Date </span></td> <td>:</td> 
				<td> 
					<div id="datetimepicker" class="input-append date">
                    <input type="text" name="tdate" readonly="readonly" class="input-small" 
					value="<?php echo set_value('tdate', isset($default['dates']) ? $default['dates'] : ''); ?>" required></input>
					  <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
					</div>
				</td>
		   </tr>
           
           
           <tr> <td> <span class="label"> Image </span> </td> <td>:</td> <td> 
				<a class="fancy" href="<?php echo isset($default['image']) ? $default['image'] : ''; ?>"> 
				<img alt="" width="250" src="<?php echo set_value('tket', isset($default['image']) ? $default['image'] : ''); ?>" >
				</a>  </td> </tr>
                
           <tr> <td> <span class="label"> Change Image </span> </td> <td>:</td> <td> <input type="file" title="Upload image" name="userfile" size="35" /> <br /> <?php echo isset($error) ? $error : '';?> <small>*) Leave it blank if not upload images.</small> </td> </tr>
		   
		   <tr> <td><span class="label"> Url </span></td> <td>:</td> 
 <td> <textarea name="turl1" title="Url1" cols="25" rows="2"><?php echo set_value('turl1', isset($default['url1']) ? $default['url1'] : ''); ?></textarea> <br/>
      <img width="150" height="200" src="<?php echo set_value('turl1', isset($default['url1']) ? $default['url1'] : ''); ?>" />
  </td> </tr>	
  
  <tr> <td> <span class="label"> Status </span> </td> <td>:</td> 
<td> Y <input name="rpublish" class="required" type="radio" title="Y" value="1" <?php echo set_radio('rpublish', '1', isset($default['publish']) && $default['publish'] == '1' ? TRUE : FALSE); ?> />
N <input name="rpublish" class="required" type="radio" title="N" value="0" <?php echo set_radio('rpublish', '0', isset($default['publish']) && $default['publish'] == '0' ? TRUE : FALSE); ?> />  <br/> 
					   </td> 
				    </tr>
		   				
		</table>				  
	<br /> <p>
		<input type="submit" name="submit" class="btn" title="Process" value=" Save " />
		<input type="reset" name="reset" class="btn" title="Cancel" value=" Cancel " />
	</p>
  </form>
  </fieldset>  
</div>

<script type="text/javascript">
      $('#datetimepicker').datetimepicker({
        format: 'yyyy-MM-dd'
      });
</script>

<div id="webadmin2">
	
     <?php echo ! empty($table) ? $table : ''; ?>
	 <div class="paging"> <?php echo ! empty($pagination) ? $pagination : ''; ?> </div> 
	
	<!-- links -->
	<div class="buttonplace"> <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?> </div>
</div>
