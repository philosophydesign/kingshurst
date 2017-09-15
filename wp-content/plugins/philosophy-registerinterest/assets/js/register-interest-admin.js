var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

jQuery(document).ready(function () {
	jQuery('.viewsubmission').click(function () {
		var sid = jQuery(this).data('submission');
		var data = {
			'action': 'getsinglesubmission',
			'submission': sid ,   
			'form': getUrlParameter('form') ,   
		};
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
//			alert('Got this from the server: ' + response);
			jQuery('#RI-popup-content').html("");
			jQuery('#RI-popup-overlay').show();
			jQuery('#submission-table').addClass("inbackground");
			response = response = JSON.parse(response);
			
			
			var html = '<p id="close-submission-box"><a class="button" href="">Close</a></p>';
			
			html += '<div id="submission-meta">'
			html += '<p>Date: '+response['meta']['date']+'</p>';
			html += '<p>IP Address: '+response['meta']['ip_address']+'</p>';
			if (response['meta']['post_title']) {
				html += '<p>Interested In: <a href="'+response['meta']['post_title']+'">'+response['meta']['post_title']+'</a></p>';
			}
			html += '<p>Platform: '+response['meta']['date']+'</p>';
			html += '<p id="submission-browser"><span class="browser-logo '+response['meta']['browser_class']+'"></span><br>'+response['meta']['browser']+'</p>';
			
			
			html += '</div>'; 
			
			html += '<table class="widefat striped">'; 
			
			for (i = 0; i < response['data'].length; i++) { 
				html += "<tr><th>"+response['data'][i]['label']+"</th><td>"+response['data'][i]['value']+"</td></tr>";
			}
			
			jQuery('#RI-popup-content').html(html);
			
		});
		return false;
		
	});
	jQuery('.viewemaillog').click(function () {
		var sid = jQuery(this).data('submission');
		var data = {
				'action': 'getemailreadlog',
				'submission': sid ,
				'form': getUrlParameter('form')  
		};
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
			
			response = response = JSON.parse(response);
			
			if (response['data'] == 'no_log') {
				alert("There are no log entries");
			} else {
				jQuery('#RI-popup-content').html("");
				jQuery('#RI-popup-overlay').show();
				jQuery('#submission-table').addClass("inbackground");
				
				var html = '<p id="close-popup-box"><a class="button" href="">Close</a></p>';
				html += '<div id="submission-meta">';
				html += '<table class="widefat striped"><tr><th>Date</th><th>IP</th></tr>'; 
				for (i = 0; i < response['data'].length; i++) { 
					html += "<tr><td>"+response['data'][i]['date']+'</td><td>'+response['data'][i]['ip']+'</td></tr>'; 
				}
				html += '</table>';
				html += '</div>';
				jQuery('#RI-popup-content').html(html);
			}
			return false;
			
		});
		return false;
		
	});
	jQuery('#RI-popup-content').live('click', '#close-submission-box', function () {
		jQuery('#RI-popup-content').html("");
		jQuery('#RI-popup-overlay').hide();
		jQuery('#submission-table').removeClass("inbackground");
		return false;
	});
	
	jQuery('.horizontal-tabs li').click(function () {
		var tab = jQuery(this).data('tabid');
		jQuery('.active').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('.tab-container').hide();
		jQuery('.tab-container#'+tab).show();
		
	});
	jQuery('#RI_addform_title').blur(function () {
		jQuery('#RI_addform_ref').val(cleanStr(jQuery(this).val()));
	});
	jQuery('#RI_addform_title').keyup(function () {
		jQuery('#RI_addform_ref').val(cleanStr(jQuery(this).val()));
	});
});