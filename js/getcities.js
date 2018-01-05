$(document).ready(function () {
	trigger = $('.countryddwrapper').attr('data-autogetcities');
	if(trigger == 'Yes'){
		var country_id = $('.countrydd').val();
		var token = $('._token').val();
		var baseurl = $('.countryddwrapper').attr('data-base');
		var type="getcities";
		var datatopost = {country_id : country_id , _token : token};
		var url = baseurl+'/get-cities';
		citiesajax(datatopost,url,type);
	}
	$(".countrydd").on('change', function () {
		var country_id = $('.countrydd').val();
		var token = $('._token').val();
		var baseurl = $('.countryddwrapper').attr('data-base');
		var type="getcities";
		var datatopost = {country_id : country_id , _token : token};
		var url = baseurl+'/get-cities';
		citiesajax(datatopost,url,type);
	});
	function citiesajax(datatopost,url,type){
				$.ajax({
							    url: url,
							    type: "post",
							    data: datatopost,
							    beforeSend: function() {
							    	console.log("Beforsend")
							    	$('.citydd').html('<option>...Loading </option>');
							    },
							    complete: function(){
							    	console.log("complete")
							    },
							    success: function(data){
							    	console.log(data);
							    	if(data.status == 'success'){
							    		if(type == 'delel'){
							    			alert("Record Deleted!");
							    			$this.closest('tr').remove();
							    		} else if(type == 'getcities'){
							    			var out = '';
							    			$.each(data.cities, function( index, value ) {
							    				var selected ='';
							    				if($('.page').val() == 'editdriver'){
													var selectedcity = $('.selectedcity').val();
													if(value._id == selectedcity){
														selected ='selected'
													}	
												} 
							    				out+='<option value="'+value._id+'" '+selected+' >'+value.name+'</option>';
											});
											$('.citydd').html('');
											$('.citydd').html(out);

							    		}
							    	}
							    }
			});

	}
});