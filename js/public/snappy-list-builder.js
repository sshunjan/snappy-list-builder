jQuery(document).ready(function($){

	var wpajax_url = document.location.protocol + '//' + document.location.host + 'wp-admin/admin-ajax.php';

	var email_capture_url = wpajax_url += '?action=slb_save_subscription';

	$('form.slb-form').bind('submit', function(){

		$form = $(this);

		var from_data = form.serialize();

		$.ajax({
			'method': 'post',
			'url' : email_capture_url,
			'data' : form_data,
			'dataType' : 'json',
			'cache' : false,
			'success': function(data, textStatus){
				if(data.status == 1){
					$form[0].reset();
					alert(data.message);
				}else{

					var msg = data.message + "\r" + data.error + "\r";

					$.each(data.errors, function(key,value){
						msg += '\r';
						msg += '-' + value;
					});

					alert(msg);

				}
			},
			'error': function( jqXHR, textStatus, errorThrown){

			}
		});

		// stop the form from submitting normally

		return false;
	});

});
