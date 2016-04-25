$(function(){
	
  $("#ignore_words").inputTags();
  
  
  $("#article_spinner_form").on("submit",function(evt){
	evt.preventDefault();
	
	//hide 
	$("#spinned_article_row").hide();

    var formdata = $(this).serialize();
	

    $.blockUI({ message: '<h4>Proccessing The Awesome..</h4>' });
	
	$.ajax({
	dataType : 'json',
    type : 'post',
	data : formdata,
	url : 'proccess_spin.php'
	})
	
	.done(function(callback){
		$.unblockUI();
		if(callback.alert_type=='error'){
			sweetAlert(
              'Oops...',
              callback.alert_msg,
              'error'
             );
		}
		
		else if(callback.alert_type=='success'){
			
			var spinned_article = $("<span />").html(callback.spinned_article).text();
			
			var spin_article_field = $("#spinned_article_area");
			
			spin_article_field.val(spinned_article);
			
			spin_article_field.height = spin_article_field[0].scrollHeight+20+"px";
	 
			$("#spinned_article_row").show();
			$('.main_container').css("height", $(document).height());
		}
		
		else{
			sweetAlert(
              'Oops...',
              'A very strange error occured,actually we have no idea what it is, but we will look in to it..',
              'error'
             );
		}//end 
	})
	
	.fail(function(){
		$.unblockUI();
		sweetAlert(
              'Oops...',
              'Request failed, try again later..',
              'error'
             );
	});
	
  });
  
});