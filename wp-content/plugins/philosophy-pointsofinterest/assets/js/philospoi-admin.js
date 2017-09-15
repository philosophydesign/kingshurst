jQuery(document).ready(function () {
	var address = jQuery('#points-of-interest-fields #textarea-address').val();
	jQuery('#points-of-interest-fields #textarea-address').blur(function () {
		if (jQuery(this).val() != address) {
			var c = confirm("Get new POI coordinates?");
			if (c) {
				getPOICoordinates();
			}
			address = jQuery(this).val();
		}
		
	});
});

function getPOICoordinates () {
	var address = jQuery('#points-of-interest-fields #textarea-address').val().replace(/\n/g, '%0A');
	jQuery('[name=coordinates]').val("...");
	jQuery.ajax({
		type: "GET",
		url: philospoi_uri+'philospoiaj.php?getlatlng='+address,
		success: function(response) {
			jQuery('[name=coordinates]').val(response);
		}
	});
}