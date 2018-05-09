	
<div id="webadmin">
	
	<div class="title"> <?php $flashmessage = $this->session->flashdata('message'); ?> </div>
	<p class="message"> <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> </p>
	
	<div id="errorbox" class="errorbox"> <?php echo validation_errors(); ?> </div>
	
	<fieldset class="field"> <legend> Product Description </legend>
	<form name="modul_form" class="myform" id="form" method="post" action="<?php echo $form_action; ?>">
		<table>
		
			<tr> 					
				<td> Category : &nbsp; </td>
				<td>
				     <select class="input-xlarge" name="ccategory"> 
<option value="a" <?php echo set_select('ccategory', 'a', isset($default['category']) && $default['category'] == 'a' ? TRUE : FALSE); ?> /> Feature &amp; Benefit </option>
<option value="b" <?php echo set_select('ccategory', 'b', isset($default['category']) && $default['category'] == 'b' ? TRUE : FALSE); ?> /> Interior &amp; Exterior Finish Option </option>
<option value="c" <?php echo set_select('ccategory', 'c', isset($default['category']) && $default['category'] == 'c' ? TRUE : FALSE); ?> /> Design Pattern &amp; Grilles </option>
<option value="d" <?php echo set_select('ccategory', 'd', isset($default['category']) && $default['category'] == 'd' ? TRUE : FALSE); ?> /> Hardware &amp; Accessories </option>  
					 </select> 
				</td>
			</tr>
			
			<tr>					
			    <td> Description : &nbsp; </td> 
 				<td> <textarea name="tdesc" class="" id="content" ><?php echo set_value('tshortdesc', isset($default['desc']) ? $default['desc'] : ''); ?></textarea>                     <?php echo display_ckeditor($ckeditor); ?> 
				</td>	
			</tr>
			
			<tr>
				<td></td> 
				 <td> 
				   <br /> <input type="submit" name="submit" class="btn" title="" value="Save" /> 
				          <input type="reset" name="reset" class="btn" title="" value="Cancel" />  
				 </td>
			</tr>
			
		</table>	
	</form>			  
	</fieldset>
	
</div>


<div id="webadmin2">
	
	<form name="search_form" class="myform" method="post" action="<?php echo ! empty($form_action_del) ? $form_action_del : ''; ?>">
      <?php echo ! empty($table) ? $table : ''; ?>
	  <div class="paging"> <?php echo ! empty($pagination) ? $pagination : ''; ?> </div>
	</form>	
		
	<!-- links -->
	<div class="buttonplace"> <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?> </div>

	
</div>

