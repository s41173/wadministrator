$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({dom: 'T<"clear">lfrtip', tableTools: {"sSwfPath": site}});
	 
	// // date time picker
	$('#d1,#d2,#d3,#d4,#d5').daterangepicker({
		 locale: {format: 'YYYY/MM/DD'}
    }); 
	
	$('#ds1,#ds2').daterangepicker({
        locale: {format: 'YYYY/MM/DD'},
		singleDatePicker: true,
        showDropdowns: true
	});


	load_data();  
	
	// batas dtatable

	// fungsi jquery konfirmasi pembayaran
	$(document).on('click','.text-confirmation',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_confirmation +"/"+ del_id;
		$(".error").fadeOut();

		// window.location.href = url;
		window.open(url, "Sales Payment : "+del_id, "toolbar=yes,scrollbars=yes,resizable=yes,top=200,left=600,width=1024,height=800");	
	});
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_get +"/"+ del_id;
		
		window.location.href = url;
		
	});

	// commision process
	$(document).on('click','.text-commision',function(e)
	{	e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_commision +"/"+ del_id;
		
		window.location.href = url;
		
	});
	
		// fungsi jquery update
	$(document).on('click','.text-print',function(e)
	{	e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_print_invoice +"/"+ del_id +"/invoice";
		
		// window.location.href = url;
		window.open(url, "Invoice SO-0"+del_id, "toolbar=yes,scrollbars=yes,resizable=yes,top=200,left=600,width=800,height=600");
		
	});

	$(document).on('click','.text-shipping',function(e)
	{	e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_print_invoice +"/"+ del_id +"/shipping";
		
		// window.location.href = url;
		window.open(url, "Invoice SO-0"+del_id, "toolbar=yes,scrollbars=yes,resizable=yes,top=200,left=600,width=800,height=600");
		
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
	
   // fungsi ajax form sales
	$('#salesformdata').submit(function() {

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
			        error_mess(1,res[1],0);
					
				    var url = sites_get +"/"+ res[2];
		            window.location.href = url;
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});

	// ajax transaction data 
	$('#ajaxtransform,#ajaxtransform1').submit(function() {

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
			        error_mess(1,res[1],0);
					location.reload(true);
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			},
			error: function(e) 
	    	{
				$("#error").html(e).fadeIn();
				console.log(e.responseText);	
	    	} 
		})
		return false;
	});

		// fungsi ajax get customer
	$(document).on('change','#ccustomer',function(e)
	{	
		e.preventDefault();
		var value = $("#ccustomer").val();
		var url = sites+'/get_customer/'+value;

		if (value){ 
		    // batas
			$.ajax({
				type: 'POST',
				url: url,
	    	    cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
				var res = result.split('|');
				$("#temail").val(res[0]);
				$("#tshipadd,#tshipaddkurir").val(res[1]);
				}
			})
			return false;

		}else { swal('Error Load Data...!', "", "error"); }

	});

		// fungsi ajax city ongkir
	$(document).on('change','#ccity_ongkir',function(e)
	{	
		e.preventDefault();
		var value = $(this).val();
		var url = sites+'/get_district/'+value;

		if (value){ 
		    // batas
			$.ajax({
				type: 'POST',
				url: url,
	    	    cache: false,
				headers: { "cache-control": "no-cache" },
				success: function(result) {
				  $("#districtbox").show();	
				  $("#districtbox").html(result);
				}
			})
			return false;

		}else {  $("#districtbox").hide(); swal('Error Load Data...!', "", "error"); }

	});

	// ckship
	$('#ckship').change(function() {
        if($(this).is(":checked")) {
          
          var par = $("#tshipadd").val();	
          $("#tshipaddkurir").val(par);

        }else { $("#tshipaddkurir").val(""); }
    });

	// get details product
	$(document).on('change','#cproduct',function(e)
	{	
		e.preventDefault();

		var pid = $("#cproduct").val();
		var url = sites+'/get_product/'+pid;

		if (pid){
	    // batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
			res = result.split('|');
				$("#tprice").val(res[0]); 
				console.log(res[1]);
			}
		})
		return false; }else { $("#tprice").val('0');  }
	});

	$(document).on('change','#cdistrict',function(e)
	{	
		e.preventDefault();

		var city = $("#ccity_ongkir").val();
		var district = $(this).val();
		var weight = $("#tweight").val();

		var url = sites+'/ongkir_nilai/';
		
	    // batas
		$.ajax({
			type: 'POST',
			url: url,
		    data: "city="+ city + "&district=" + district + "&weight=" + weight,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				res = result.split('|');
		     	$("#shipn").html(res[2]);
				$("#tpackage").val(res[1]);
				$("#rate").val(res[0]);
			}
		})
		return false;

	});

	$('#searchform').submit(function() {
		
		var cust = $("#ccustomer_search").val();
		var confirm = $("#cconfirm").val();
		var param = ['searching',cust,confirm];
		
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
				load_data_search(param);
			}
		});
		return false;
		swal('Error Load Data...!', "", "error");
		
	});	


		
// document ready end	
});


	function load_data_search(search)
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			
				console.log(source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3]);
			
		    $.ajax({
				type : 'GET',
				url: source+"/"+search[0]+"/"+search[1]+"/"+search[2],
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
						  if (s[i][8] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][2],
										s[i][9],
										s[i][3],
										s[i][4],
										s[i][5],
										s[i][6],
										s[i][7],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Confirmation Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-success btn-xs text-print" id="' +s[i][0]+ '" title="Invoice Status"> <i class="fa fa-print"> </i> </a> '+
'<a href="" class="btn btn-default btn-xs text-confirmation" id="' +s[i][0]+ '" title="Payment Confirmation"> <i class="fa fa-credit-card-alt"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-commision" id="' +s[i][0]+ '" title="Commision Payment"> <i class="fa fa-money"> </i> </a> '+
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
						  if (s[i][8] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][2],
										s[i][9],
										s[i][3],
										s[i][4],
										s[i][5],
										s[i][6],
										s[i][7],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Confirmation Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="btn btn-success btn-xs text-print" id="' +s[i][0]+ '" title="Invoice Status"> <i class="fa fa-print"> </i> </a> '+
'<a href="" class="btn btn-default btn-xs text-confirmation" id="' +s[i][0]+ '" title="Payment Confirmation"> <i class="fa fa-credit-card-alt"> </i> </a> '+
'<a href="" class="btn btn-warning btn-xs text-commision" id="' +s[i][0]+ '" title="Commision Payment"> <i class="fa fa-money"> </i> </a> '+
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
		  $("#tname, #tmodel, #tsku").val("");
		  $("#catimg").attr("src","");
	  });
	}
	
	function load_form()
	{
		$(document).ready(function (e) {
			
		  	$.ajax({
				type : 'GET',
				url: source,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(data) 
				{   
					// alert(data[0][1]);
					$("#tname").val(data[0][1]);
					$("#taddress").val(data[0][2]);
					$("#ccity").val(data[0][13]).change();
					$("#tzip").val(data[0][9]);
					$("#tphone").val(data[0][3]);
					$("#tphone2").val(data[0][4]);
					$("#tmail").val(data[0][5]);
					$("#tbillmail").val(data[0][6]);
					$("#ttechmail").val(data[0][7]);
					$("#tccmail").val(data[0][8]);
					$("#taccount_name").val(data[0][10]);
					$("#taccount_no").val(data[0][11]);
					$("#tbank").val(data[0][12]);
					$("#tsitename").val(data[0][14]);
					$("#tmetadesc").val(data[0][15]);
					$("#tmetakey").val(data[0][16]);
					$("#catimg_update").attr("src","");
					$("#catimg_update").attr("src",base_url+"images/property/"+data[0][17]);
			   
				},
				error: function(e){
				   //console.log(e.responseText);	
				}
				
			});  
			
	    });  // end document ready	
	}
	