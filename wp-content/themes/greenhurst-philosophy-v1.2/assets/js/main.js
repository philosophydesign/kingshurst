jQuery(document).ready(function () {
	jQuery('div.form-row').each(function () {
		var placeholder = striphtml(jQuery(this).children('label').html());
		if ((jQuery(this).hasClass('rowtype-textfield')) || (jQuery(this).hasClass('rowtype-body'))) {
			jQuery(this).children('input,textarea').attr('placeholder', placeholder);
		} else if (jQuery(this).hasClass('rowtype-select')) {
			jQuery(this).children('select option').remove();
			jQuery(this).children('select option').unbind();
			
			jQuery(this).children('select').html('<option value="">'+placeholder+'</option>'+jQuery(this).children('select').html());
		}
		
	});
	jQuery('div.form-row label').hide();
	/*
	jQuery('body > .container').prepend('<div class="row"><div class="col-md-12 col-sm-12 col-xs-12" id="mobile-menu-container"><a href="#" id="mobile-trigger">Menu</a><ul id="mobile-menu-main">'+jQuery('#menu-main-menu').html()+'</ul></div></div>');
	jQuery('body > .container').on('click','#mobile-trigger', function () {
		jQuery('#mobile-menu-container').toggleClass("open");
	});
	*/
	
	
});
function striphtml(html)
{
   var tmp = document.createElement("DIV");
   tmp.innerHTML = html;
   return tmp.textContent || tmp.innerText || "";
}
var mapcenter = [52.0896036,-1.896639];
var greenhurts = [52.092468, -1.891833];

function philostheme_InitMap () {
	console.log("philostheme_InitMap!");
	if (jQuery('#contactusmap').length) {
		map = new google.maps.Map(document.getElementById('contactusmap'), {
		      center: {lat:  parseFloat(mapcenter[0]), lng:  parseFloat(mapcenter[1])},
		      scrollwheel: false,
		      zoom: 15,
		});
		var centertitle = "Kingshurst";
		if (typeof themeuri !== "undefined") {
			var marker_plain =  new google.maps.MarkerImage( themeuri+'/assets/img/mappointer_k.png',
		            new google.maps.Size(61, 62),
		            new google.maps.Point(0, 0),
		            new google.maps.Point(6, 50));
		
			var myLatLng = {lat: parseFloat(greenhurts[0]), lng: parseFloat( greenhurts[1])};
			devmarker = new google.maps.Marker({
			      map: map,
			      position: myLatLng,
			      icon: marker_plain,
			      title: centertitle
			    });	
		}
	}
}