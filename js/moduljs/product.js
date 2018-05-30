$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	 
	// // date time picker
	// $('#d1,#d2,#d3,#d4,#d5').daterangepicker({
		 // locale: {format: 'YYYY/MM/DD'}
	// }); 
	
	// $('#ds1,#ds2').daterangepicker({
    //     locale: {format: 'YYYY/MM/DD'},
	// 	singleDatePicker: true,
	// 	showDropdowns: true,
	// 	pickDate: false
	//  });

	$('#tstart').timepicker({
		timeFormat: 'HH:mm',
		interval: 60,
		minTime: '10',
		maxTime: '6:00pm',
		defaultTime: '10',
		startTime: '10:00',
		dynamic: false,
		dropdown: true,
		scrollbar: true
        });

        $('#tend').timepicker({
            timeFormat: 'HH:mm',
            interval: 60,
            minTime: '3',
            maxTime: '8:00pm',
            defaultTime: '12',
            startTime: '12:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });

	

	$(document).on('click','#crestrict',function(e)
	{	
		if($(this).prop('checked')) {
			
			$('#tstart').prop('readonly', false);
			$('#tend').prop('readonly', false);
			$('#tqty').prop('readonly', false);

		} else {
			$('#tstart').prop('readonly', true);
			$('#tend').prop('readonly', true);
			$('#tqty').prop('readonly', true);
		}
		
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
		
		window.location.href = url;
	});
	
		// fungsi jquery update
	$(document).on('click','.text-ledger',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_ledger +"/"+ del_id;
		
		window.location.href = url;
		
	});

	// fungsi assembly status
	$(document).on('click','.text-assembly',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_assembly +"/"+ del_id;
		$(".error").fadeOut();		
		
		$("#myModal4").modal('show');
		$.post(url,
			{id:$(this).attr('data-id')},
			function(result)
			{
				
			   res = result.split("|");
			   var val = res[2].split(",");
				
			   $("#tid_update").val(res[0]);
			   $("#tname_update").val(res[1]);
			   $("#cmaterial_update").val(val).change();
			}   
		);
	});

	// fungsi calculator status
	$(document).on('click','.text-calculator',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_calculator +"/"+ del_id;
		$(".error").fadeOut();		
		$("#tid_updates").val(del_id);
	    $("#myModal9").modal('show');
	
		$.post(url,
			{id:$(this).attr('data-id')},
			function(result)
			{
			   res = result.split("|");
			   $("#tname_updates").val(res[0]);
			   $("#tmodel_updates").val(res[1]);

			   $("#material_table").empty();
			   $("#stotal").html(0);
			}   
		);

		combo_color(del_id);
	});

	$('#cglasstype').change(function() {
		
	    var val = $("#cglasstype").val();
		$.ajax({
			type: 'POST',
			url: sites_glass+"/"+val,
			data: $(this).serialize(),
			success: function(data)
			{
			   document.getElementById("glassbox").innerHTML = data;
			}
		})
		return false;
	});

		// ajax form non upload data
	$("#calculator_form").on('submit',(function(e) {
		
		var elem = $(this);

		e.preventDefault();
		$.ajax({
        	url: $(this).attr('action'),
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend : function()
			{
				//$("#preview").fadeOut();
			},
			success: function(data)
		    {
			    console.log(data);
				res = data.split("|");
				$("#material_table").empty();
				$("#material_table").append(res[0]);
				$("#stotal").html(res[1]);
		    },
		  	error: function(e) 
	    	{
				//$("#error").html(e).fadeIn();
				error_mess(3,e);
				console.log(e.responseText);	
	    	} 	        
	   });
	     
	}));
	
	// fungsi attribute status
	$(document).on('click','.text-attribute',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_attribute +"/"+ del_id;
		$(".error").fadeOut();
		
		console.log(url);
		
		$("#myModal2").modal('show');
		$('#frame').attr('src',url);
		$('#frame_title').html('Product Attribute');	
	});
	
	$(document).on('click','.text-img',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_image +"/"+ del_id;
		$(".error").fadeOut();
		
		$("#myModal2").modal('show');
		$('#frame').attr('src',url);
		$('#frame_title').html('Product Image');	
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

	// recommend status
	$(document).on('click','.recomend_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_recomend +"/"+ del_id;
		$(".error").fadeOut();
		
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
		
		var cat = $("#ccategory").val();
		var model = $("#cmodel").val();
		var publish = $("#cpublish").val();
		var param = ['searching',cat,model,publish];
		
		// alert(publish+" - "+dates);
		
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
			var recomend = 'btn btn-danger';
			
				// console.log(source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3]);
			
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
			if (s[i][6] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
			if (s[i][7] == 1){ recomend = 'btn btn-success'; }else { recomend = 'btn btn-warning'; }
			oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
						  i+1,
'<img src="'+s[i][3]+'" class="img_product" alt="'+s[i][3]+'">',
						  s[i][2],
						  s[i][1],
						  s[i][4],
						  s[i][5],
						  s[i][8],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="'+recomend+' btn-xs recomend_status" id="' +s[i][0]+ '" title="Recomend Status"> <i class="fa fa-fire"> </i> </a> '+
'<a href="" class="btn btn-default btn-xs text-img" id="'+s[i][0]+'" title="Product Image"> <i class="fa fa-picture-o"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
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
			var recomend = 'btn btn-danger';
			
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
						  if (s[i][6] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
						  if (s[i][7] == 1){ recomend = 'btn btn-success'; }else { recomend = 'btn btn-warning'; }
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
'<img src="'+s[i][3]+'" class="img_product" alt="'+s[i][3]+'">',
										s[i][2],
										s[i][1],
										s[i][4],
										s[i][5],
										s[i][8],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Primary Status"> <i class="fa fa-power-off"> </i> </a> '+
'<a href="" class="'+recomend+' btn-xs recomend_status" id="' +s[i][0]+ '" title="Recomend Status"> <i class="fa fa-fire"> </i> </a> '+
'<a href="" class="btn btn-default btn-xs text-img" id="'+s[i][0]+'" title="Product Image"> <i class="fa fa-picture-o"> </i> </a> '+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a> '+
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

	function restriction(){

		alert("Hello World");
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
	