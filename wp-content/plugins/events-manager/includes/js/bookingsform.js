var em_booking_doing_ajax = false;
$('#em-booking-form').addClass('em-booking-form'); //backward compatability
$(document).on('submit', '.em-booking-form', function(e){
	e.preventDefault();
	var em_booking_form = $(this);
	$.ajax({
		url: EM.bookingajaxurl,
		data: em_booking_form.serializeArray(),
		dataType: 'jsonp',
		type:'post',
		beforeSend: function(formData, jqForm, options) {
			if(em_booking_doing_ajax){
				alert(EM.bookingInProgress);
				return false;
			}
			em_booking_doing_ajax = true;
			$('.em-booking-message').remove();
			em_booking_form.parent().append('<div id="em-loading"></div>');
		},
		success : function(response, statusText, xhr, $form) {
			$('#em-loading').remove();
			$('.em-booking-message').remove();
			//show error or success message
			if(response.result){
				$('<div class="em-booking-message-success em-booking-message">'+response.message+'</div>').insertBefore(em_booking_form);
				em_booking_form.hide();
				$('.em-booking-login').hide();
				$(document).trigger('em_booking_success', [response, em_booking_form[0]]);
				document.dispatchEvent( new CustomEvent('em_booking_success', {
				    detail: {
                        response : response,
                        form : em_booking_form[0]
                    }
			    }));
				if( response.redirect ){ //custom redirect hook
					window.location.href = response.redirect;
				}
			}else{
				if( response.errors != null ){
					if( $.isArray(response.errors) && response.errors.length > 0 ){
						var error_msg;
						response.errors.each(function(i, el){
							error_msg = error_msg + el;
						});
						$('<div class="em-booking-message-error em-booking-message">'+error_msg.errors+'</div>').insertBefore(em_booking_form);
					}else{
						$('<div class="em-booking-message-error em-booking-message">'+response.errors+'</div>').insertBefore(em_booking_form);							
					}
				}else{
					$('<div class="em-booking-message-error em-booking-message">'+response.message+'</div>').insertBefore(em_booking_form);
				}
				$(document).trigger('em_booking_error', [response]);
			}
		    $('html, body').animate({ scrollTop: $('.em-booking-message').offset().top - EM.booking_offset }); //sends user back to top of form
			em_booking_doing_ajax = false;
			//run extra actions after showing the message here
			if( response.gateway != null ){
				$(document).trigger('em_booking_gateway_add_'+response.gateway, [response]);
			}
			if( !response.result && typeof Recaptcha != 'undefined' && typeof RecaptchaState != 'undefined'){
				Recaptcha.reload();
			}else if( !response.result && typeof grecaptcha != 'undefined' ){
				grecaptcha.reset();
			}
			$(document).trigger('em_booking_complete', [response]);
		},
		error : function(jqXHR, textStatus, errorThrown){
			$(document).trigger('em_booking_ajax_error', [jqXHR, textStatus, errorThrown]);
		},
		complete : function(jqXHR, textStatus){
			em_booking_doing_ajax = false;
			$('#em-loading').remove();
			$(document).trigger('em_booking_ajax_complete', [jqXHR, textStatus]);
		}
	});
	return false;	
});