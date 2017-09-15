var xht_gnl_poi = jQuery.ajax();
var mapcenter = [0,0];
var centertitle = "";
var poitypes = ""; 
function get_markers () {
	poitypes = ""; 
	jQuery('#neighborhood-poi-categories-form input').each(function () {
		if (jQuery(this).prop('checked') == true) {
			poitypes += jQuery(this).val()+",";
		}
	});
	jQuery('body').addClass("poi-map-loading");
	xht_gnl_poi.abort();
	xht_gnl_poi = jQuery.ajax({
		type: "GET",
		url: philospoiaj+'?poifor='+poifor+'&poit='+poitypes ,
		success: function(response) {
			response = JSON.parse(response);
			nhmm = response['nhmm'];
			mapcenter = response['mapcenter'];
			centertitle = response['centertitle'];
			philospoi_InitMap();
			jQuery('body').removeClass("poi-map-loading");
		}
	});
}
var map;
var maphasinit = false;
var markerobj = [];
var devmarker;

function philospoi_InitMap () {
	if (jQuery('#neighborhoodmap').length) {
		if (typeof philospoiaj === "undefined") {
			return false;
		}
		if (typeof poifor !== "undefined") { 
			if (typeof nhmm === "undefined")  {
				get_markers();
			} else if (typeof nhmm !== "undefined") {
				if (maphasinit == false) {
					map = new google.maps.Map(document.getElementById('neighborhoodmap'), {
					      center: {lat:  parseFloat(mapcenter[0]), lng:  parseFloat(mapcenter[1])},
					      scrollwheel: false,
					      zoom: 13,
					      styles: [{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#7b7b7b"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c8d7d4"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#070707"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}]
					});
					maphasinit = true;
				} else {
				    var center = new google.maps.LatLng(parseFloat(mapcenter[0]), parseFloat(mapcenter[1]));
					map.panTo(center);
					for (i in markerobj) {
						markerobj[i].setMap(null);
					}
					devmarker.setMap(null);
					markerobj.length = 0;
					markerobj = [];
				}
				jQuery('.philospoilookup').parents('li').removeClass('active');
				jQuery('[data-philospoilookup="'+poifor+'"]').parents('li').addClass('active');
				
				var mapcontent = "";
				var infowindow = new google.maps.InfoWindow();
				
				
				var iconBase = philospoi_uri+'/assets/img/';
				
				var marker_types = []; 
				if (typeof poiTypes !== "undefined") {
					for (i in poiTypes) {
						marker_types[poiTypes[i]] =  new google.maps.MarkerImage( iconBase + 'marker-'+poiTypes[i]+'.png',
				                new google.maps.Size(47, 68),
				                new google.maps.Point(0, 0),
				                new google.maps.Point(24, 67));
					}
					
				}
			    var marker_plain =  new google.maps.MarkerImage( iconBase + 'marker.png',
		                new google.maps.Size(47, 68),
		                new google.maps.Point(0, 0),
		                new google.maps.Point(24, 67));
			    // Create a marker and set its position.
				var myLatLng = {lat: parseFloat(mapcenter[0]), lng: parseFloat( mapcenter[1])};
				devmarker = new google.maps.Marker({
				      map: map,
				      position: myLatLng,
				      icon: marker_plain,
				      title: centertitle
				    });	
				 google.maps.event.addListener(devmarker, 'click', (function(marker, i) {
				        return function() {
				          infowindow.setContent("<h3>"+centertitle+"</h3>The development");
				          infowindow.open(map, devmarker);
				        }
				      })(devmarker, i));
				 
				for (i in nhmm) {
					myLatLng = {lat: parseFloat(nhmm[i][2][0]), lng: parseFloat( nhmm[i][2][1])};
					if ((typeof nhmm[i][5] !== "undefined") && (typeof nhmm[i][5][0] !== "undefined") && (typeof marker_types[nhmm[i][5][0]])) {
						var usemarker = marker_types[nhmm[i][5][0]];
					} else {
						var usemarker = marker_plain;
					}
					
					markerobj[i] = new google.maps.Marker({
					      map: map,
					      position: myLatLng,
					      icon: usemarker,
					      title: nhmm[i][0]
					    });
					 google.maps.event.addListener(markerobj[i], 'click', (function(marker, i) {
					        return function() {
					          infowindow.setContent("<div class=\"mapmarkercontent neighborhooditem\"><h3>"+nhmm[i][0]+"</h3><p>Distance: "+nhmm[i][1].toFixed(2)+'km</p><img src=\"'+nhmm[i][4][0]+'\"><p class=\"markerdes\">'+nhmm[i][3]+'</p></div>');
					          infowindow.open(map, marker);
					        }
					      })(markerobj[i], i));
						}
			    /*
			    var i = 1;
			   
				*/
			}
		} else {
		}
	}
}




jQuery(document).ready(function () {
	jQuery('.philospoilookup').click(function () {
		poifor = jQuery(this).data('philospoilookup');		
		get_markers ();
		return false;
	});
	jQuery('.poi-type input').change(function () {
		var poitypesnew = "";
		jQuery('#neighborhood-poi-categories-form input').each(function () {
			if (jQuery(this).prop('checked') == true) {
				poitypesnew += jQuery(this).val()+",";
			}
		});
		if (poitypesnew != poitypes) {
			get_markers ();
		}
	});
});