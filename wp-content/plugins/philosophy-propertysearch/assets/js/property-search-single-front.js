jQuery(document).ready(function () {
//	alert("Test");
});
function propsrch_InitMap_single () {
	console.log("Init map");
	var iconBase = '/wp-content/plugins/philosophy-propertysearch/assets/img/';
    var icons = {
      property: {
        icon: iconBase + 'pin.png'
      }
    };
	if (typeof single_property !== 'undefined') {
		console.log(single_property );
		var map = new google.maps.Map(document.getElementById('propsrch_mapsingle'), {
		      center: {lat: single_property[0], lng: single_property[1]},
		      scrollwheel: false,
		      zoom: 13
		});
		
		console.log("I am here");
		var marker;
    	var mapcontent = "";
    	var infowindow = new google.maps.InfoWindow();
		
	    // Create a marker and set its position.
		var myLatLng = {lat: parseFloat(single_property[0]), lng: parseFloat(single_property[1])};
		mapcontent  = '<h3>'+single_property[2]+'</h3>';
		mapcontent  += '<p>'+single_property[4]+'</p>';
		mapcontent  += '<a href="'+single_property[3]+'">View more</a>';
		console.log(mapcontent);
	    marker = new google.maps.Marker({
	      map: map,
	      position: myLatLng,
	      icon: icons['property'].icon,
	      title: single_property[2]
	    });
	    var i = 1;
	    google.maps.event.addListener(marker, 'click', (function(marker, i) {
	        return function() {
	          infowindow.setContent(mapcontent);
	          infowindow.open(map, marker);
	        }
	      })(marker, i));
		
		
	} else {
		console.log("single_property_latlng is undefined");
	}
}