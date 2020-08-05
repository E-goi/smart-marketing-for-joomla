jQuery(document).ready(function($) {
	$('#jm_fields').change(function() {
		if(($(this).val() != '') && ($('#egoi').val() != '')){
			$('#save_map_fields').prop('disabled', false);
		}else{
			$('#save_map_fields').prop('disabled', true);
		}
	});

	$('#egoi').change(function() {
		if(($(this).val() != '') && ($('#jm_fields').val() != '')){
			$('#save_map_fields').prop('disabled', false);
		}else{
			$('#save_map_fields').prop('disabled', true);
		}
	});

	$('#save_map_fields').click(function() {
		
		var $jm = $('#jm_fields');
		var $jm_name = $('#jm_fields option:selected');
		var $egoi = $('#egoi');
		var $egoi_name = $('#egoi option:selected');

		if(($jm.val() != '') && ($egoi.val() != '')){

			$('#load_map').show();

			$.ajax({
			    type: 'POST',
			    data:({
			        jm: $jm.val(),
			        jm_name: $jm_name.text(),
			        egoi: $egoi.val(),
			        egoi_name: $egoi_name.text(),
			        token_egoi_api: 1
			    }),
			    success:function(data, status) {
			    	if(data == 'ERROR'){
			    		$("#error_map").show();
			    	}else{
			    		$(data).appendTo('#all_fields_mapped');
			    		$("#error_map").hide();
			    	}
			       	
			       	$jm.val('');
			       	$egoi.val('');
			       	$('#save_map_fields').prop('disabled', true);
			       	$('#load_map').hide();
			    },
			    error:function(status){
			    	if(status){
				    	$("#error_map").show();
				    	$('#load_map').hide();
				    	$('.col_map').hide();
				    }
			    }
			});
		}

	});


	$('.egoi_fields').live('click', function(){

		var id = $(this).data('target');
		var tr = 'egoi_fields_'+id;
		$('.load_map').show();
		
		$.ajax({
		    type: 'POST',
		    data:({
		        token_egoi_api: 1,
		        id_egoi: id
		    }),
		    success:function(data, status) {
		       $('#'+tr).remove();
		       $('.load_map').hide();
		    },
		    error:function(status){
		    	if(status){
			    	$("#error").show();
			    	$('#load_map').hide();
			    }
		    }
		});

	});
});