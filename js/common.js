$(document).ready(function () {
	$('.__del').on('click',function(event){
			event.preventDefault();
			console.log($(this).attr('href'));
			var url = $(this).attr('href');
			var token = $('._token').val();
			var datatopost = {origin : $(this).attr('data-origin') , _token : token};
		var r = confirm("Are you Sure to Delete?");
		if (r == true) {
			//ajax to change status
			//var theid = $(this).closest('tr').find('.movieid').val();
			 //fd = new FormData();

          //  fd.append('_token',CSRF_TOKEN );
            //fd.append('id', theid);
			commonajax(datatopost,url,'delel',$(this))
			//window.location.href = url;

		} else {
		    //ajax to change status
		} 
	});

	$(".image-file").on('change', function () {
		setTimeout( function previewFile() {
			var preview = document.querySelector('img#imgprview');
			var file    = document.querySelector('input[type=file]').files[0];
			console.log(file);
			var reader  = new FileReader();
			$('.image_loader').hide();
			setTimeout( function(){ 
				$('#image').show();
			}  , 500 );
			reader.onloadend = function () {
				preview.src = reader.result;
			}
			if (file) {
				reader.readAsDataURL(file);
			} else {
				preview.src = "";
			}
		} , 500 );
});


	function commonajax(datatopost,url,type,$this){
				$.ajax({
							    url: url,
							    type: "post",
							    data: datatopost,
							    beforeSend: function() {
							    	console.log("Beforsend")
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
							    		}
							    	}
							    }
			});

	}
});