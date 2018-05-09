<html>
<head>    
<!-- bootstrap basic -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>js/bs/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
<script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>js/bs/datatable/jquery-1.12.4.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo base_url(); ?>js/bs/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo base_url();?>js-old/register.js"></script>
    
<!-- Date time picker -->
 <script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    
<title> Sales Payment - List </title>    
    
</head>
<body onload="closeWindow()">
  <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 border">

  <fieldset class="field"> <legend> Sales Payment - <?php echo $code; ?> </legend> </fieldset>            
     
    <style type="text/css">
        #example{ font-size: 13px;}
    </style>        
    <?php echo ! empty($table) ? $table : ''; ?>     
            
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal1"> Add Transaction </button>
                
  <script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>js/bs/datatable/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>js/bs/datatable/dataTables.bootstrap.min.js"></script>
            
    <!-- sweet alert js -->
  <script type="text/javascript" src="<?php echo base_url().'js/sweetalert/sweetalert.min.js'; ?>"></script>
  <style type="text/css">@import url("<?php echo base_url() . 'js/sweetalert/sweetalert.css'; ?>");</style>            
            
  <script type="text/javascript">
    

  function closeWindow() {
setTimeout(function() {
window.close();
}, 600000);
}      
            
  $(function(){
      
    $('#ajaxform,#ajaxform1').submit(function() {
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(data) {
				
				res = data.split("|");
				if (res[0] == "true")
				{   
                    $("#myModal").modal('hide');
				    location.reload(true);
				}
				else{ swal(res[1], "", "error"); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});  
      
    $("#example").dataTable();
      
    $('#dtime1,#dtime2').daterangepicker({
        timePicker: true,
		singleDatePicker: true,
        showDropdowns: true,
        timePicker24Hour: true,
        locale: { format: 'YYYY-MM-DD H:mm'}
	});

     
    $(document).on('click','.text-confirmation',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
        var sites_confirmation  = "<?php echo site_url('sales_payment/update/');?>";
		var url = sites_confirmation +"/"+ del_id;
        
        $("#myModal").modal('show');
        
        // // batas
		 $.ajax({
		 	type: 'POST',
		 	url: url,
    	     cache: false,
		 	headers: { "cache-control": "no-cache" },
		 	success: function(result) {
				
		 		res = result.split("|");
				
                $('#dtime1').val(res[0]);	
		 		$("#taccname").val(res[1]);
		 		$("#taccno").val(res[2]);
		 		$('#taccbank').val(res[3]);
		 		$('#tamount').val(res[4]);
                $('#cstts').val(res[5]);
		 	    $('#cbank').val(res[6]);
                $('#cphase').val(res[7]);
		 	}
		 })
		 return false;	
        
	});
      
      
      // text confirm
      
      
  })
  </script>

</div>     
      </div>
    
      <!-- Modal - Add Form -->
      <div class="modal fade" id="myModal" role="dialog">
         <?php $this->load->view('sales_confirmation'); ?>  
      </div>
      
      <div class="modal fade" id="myModal1" role="dialog">
         <?php $this->load->view('sales_confirmation_form'); ?>  
      </div>
      
    </div>
</body>
</html>