jQuery(document).ready(function () {
	jQuery('form.philosri').prepend('<div class="feedback"></div>');
	jQuery('form.philosri').append('<button class="sup clearform">Clear</button>');
	var clearformclicked = false;
	jQuery('body').on('click','form.philosri .clearform',function (event) {
		clearformclicked = true;
		jQuery('.form-row select').each(function (event) {
			jQuery(this).val("?");
		});
		jQuery('.form-row input, form-row.mandatory textarea').each(function (event) {
			jQuery(this).val("");
		});
		return false;
	});
	jQuery('body').on('submit','form.philosri',function (event) {
		event.preventDefault();
		if (clearformclicked == false) {
			submitInterest(jQuery(this));
		}
		return false;
	});
	jQuery('.fieldsetinner').on('blur','.form-row.mandatory input, .form-row.mandatory select, .form-row.mandatory textarea', function () {
		console.log('blur');
		if (jQuery(this).val().length > 0) {
			console.log(jQuery(this).val());
			jQuery(this).parents('.form-row').removeClass('error');
		} else {
			jQuery(this).parents('.form-row').addClass('error');
			console.log("EMPTY");
		}
	});
});

function submitInterest(form_dom) {
	console.log('CALLED submitInterest');
	var satisfied = true;
	
	jQuery('.form-row.mandatory input, .form-row.mandatory select, .form-row.mandatory textarea').each(function () {
		if (jQuery(this).val().length == 0) {
			satisfied = false;
			jQuery(this).parents('.form-row').addClass('error');
		} else {
			jQuery(this).parents('.form-row').removeClass('error');
		}
	});
	if (satisfied) {
		jQuery('body').addClass('loading regint_ajax_loading');
		//jQuery('#propsrch_results').html("Submitting...");
		if (philosriaj.length == 0) {
			philosriaj = "?philosri=submitajax";
		} else {
			philosriaj += '?philosri=submitajax'
		}
		var datastring = form_dom.serialize();
		jQuery.post( philosriaj, datastring,	function( response ) {
			response = JSON.parse(response);
			if (response['success']) {
				form_dom.html("Your interest has been registered");
				window.location = '/thank-you-for-registering/';
			} else {
				form_dom.children('.feedback').html("There was an issue trying to send you message, please try again or get in touch.")
			}
			return false;
		});
	}
	
	return false;
}