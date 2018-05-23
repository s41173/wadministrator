$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	 
	// date time picker
	$('#d1,#d2,#d3,#d4,#d5').daterangepicker({
		 locale: {format: 'YYYY/MM/DD hh:mm'},
		 singleDatePicker: true,
		 showDropdowns: true,
		 timePicker: true,
		 timePicker24Hour: true,
		 startDate: moment().startOf('hour')
	}); 

	$('#ds1,#ds2').daterangepicker({
		locale: {format: 'YYYY/MM/DD'}
    }); 
	
	load_data();  
	// batas dtatable
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_get +"/"+ del_id;
		$(".error").fadeOut();
		
		$("#myModal2").modal('show');
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {

				res = result.split("|");				
				resets();
				$("#tid_update").val(res[0]);
				$("#ccust2_update").val(res[1]);
				$("#d2").val(res[2]);
				$("#ctype_form_update").val(res[3]).change();
				$("#tamount_update").val(res[6]);
				if (res[3] == 0){ $('#cbank_update').hide(); }else{
					$('#cbank_update').show();
					$('#cbank_update').val(res[7]).change();
				}
			}
		})
		return false;
	});

	$(document).on('click','.text-redeem',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_redeem +"/"+ del_id;
		$(".error").fadeOut();

		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   
			        error_mess(1,res[1],0);
					load_data();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }			
			}
		})
		return false;
	});

	$("#cbank,#cbank_update").hide();	
	// fungsi fade in / out cbank
	$(document).on('change','#ctype_form, #ctype_form_update',function(e)
	{	
		e.preventDefault();
		var value = $(this).val();
		if (value == 2){ $("#cbank").show(); $("#cbank_update").show(); }else{ $("#cbank").hide(); $("#cbank_update").hide(); }
	});
	
	
		// fungsi ajax combo
	$(document).on('change','#ccity,#ccity_update',function(e)
	{	
		e.preventDefault();
		var value = $(this).val();
		var url = sites_ajax+'/ajaxcombo_district/';
		
		console.log(value);
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    data: "value="+ value,
			success: function(result) {
			$('#cdistrict_update').hide();
			$(".select_box").html(result);
			}
		})
		return false;	
	});
	
	// publish status
	$(document).on('click','.primary_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_primary +"/"+ del_id;
		$(".error").fadeOut();
		
		// $("#myModal2").modal('show');
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   
			        error_mess(1,res[1],0);
					load_data();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
	});
	
	
    $('#searchform').submit(function() {
		
		var cust = $("#ccust").val();
		var type = $("#ctype").val();
		var publish = $("#cpublish").val();
		var param = ['searching',cust,type,publish];
		
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(data) {
				
				if (!param[1]){ param[1] = 'null'; }
				if (!param[2]){ param[2] = 'null'; }
				if (!param[3]){ param[3] = 'null'; }
				load_data_search(param);
			}
		})
		return false;
		swal('Error Load Data...!', "", "error");
		
	});	
		
// document ready end	
});

	function load_data_search(search=null)
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
            
		    $.ajax({
				type : 'GET',
                url: source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3],
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				       console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
		for(var i = 0; i < s.length; i++) {
			if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
			if (s[i][8] == 1){ stts_redeem = 'btn btn-success'; }else { stts_redeem = 'btn btn-danger'; }
			oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
						  i+1,
						  s[i][1],
						  s[i][2],
						  s[i][3],
						  s[i][6],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="'+stts_redeem+' btn-xs text-redeem" id="' +s[i][0]+ '" title="Redeem Status"> <i class="fa fa-exchange"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div>'
							  ]);										
							  } // End For 
											
				},
				error: function(e){
				   oTable.fnClearTable();  
				   //console.log(e.responseText);	
				}
				
			});  // end document ready	
			
        });
	}

    // fungsi load data
	function load_data()
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			var stts_redeem = 'btn btn-danger';
			
		    $.ajax({
				type : 'GET',
				url: source,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				       console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
							for(var i = 0; i < s.length; i++) {
						  if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  if (s[i][8] == 1){ stts_redeem = 'btn btn-success'; }else { stts_redeem = 'btn btn-danger'; }
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
                                        s[i][2],
                                        s[i][3],
                                        s[i][6],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="'+stts_redeem+' btn-xs text-redeem" id="' +s[i][0]+ '" title="Redeem Status"> <i class="fa fa-exchange"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div>'
										    ]);										
											} // End For 
											
				},
				error: function(e){
				   oTable.fnClearTable();  
				   console.log(e.responseText);	
				}
				
			});  // end document ready	
			
        });
	}
	
	// batas fungsi load data
	function resets()
	{  
	   $(document).ready(function (e) {
		  // reset form
		  $("#breset").click();
		  $("#catimg").attr("src","");
	  });
	}
	