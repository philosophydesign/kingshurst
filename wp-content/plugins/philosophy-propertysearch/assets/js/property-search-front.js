var oldmult = 1; 
var mult = 1;
var unitmults = new Array();


/*
 * 0 - multiplier
 * 1 - incremements
 */


unitmults['sqm'] = ['Sq M', 		1, 				50];
unitmults['sqf'] = ['Sq Ft',		10.764,			5]
unitmults['acr'] = ['AC',			0.00024711,		1];
unitmults['hec'] = ['ha',			0.0001,			1];




jQuery(document).ready(function () {
	
	if (typeof max_total_area_metres  === "undefined") {
		max_total_area_metres = 10000;
	}
	if (typeof sizeunits === "undefined") {
		sizeunits = 'sqm';
	}
	if (mapmode == true) {
		console.log("getNewList - ready");
		getNewList("form");
	}
	jQuery('.propsrch_extras_form button').hide();
	jQuery('.propsrch_form button').hide();
	 
	jQuery( ".row-pr .multitext-container" ).append('<div id="price-slider"></div>');
	jQuery( ".row-pr .multitext-container" ).append('<div id="price-slider-feedback">&pound;0 - &pound;9,999,999</div>');
	jQuery( ".row-pr .multitext-container input[type=text]").attr('type','hidden');
	jQuery( ".row-pr .multitext-container label").hide();
	 jQuery( "#price-slider" ).slider({
	      range: true,
	      min: 0,
	      max: 9999999,
	      step: 500,
	      values: [ 0, 1000000],
	      slide: function( event, ui ) {
	    	  jQuery( "#price-slider-feedback" ).html( "&pound;" + ui.values[ 0 ].formatMoney(2, '.', ',') + " - &pound;" + ui.values[ 1 ].formatMoney(2, '.', ',') );
	      },
	      change: function (event, ui) {getNewList("form"); }
	    });
	 
	 
	 jQuery( ".row-sz .multitext-container" ).append('<div id="size-slider"></div>');
	 
	 
	
	 /*
	 jQuery(".rowtype-multiselect select").chosen({disable_search_threshold: 10});
	 jQuery(".rowtype-multiselect select").chosen().change(function () {
		 getNewList("form");
		 return false;
	 });
	 */
	 
	 
	 jQuery(".propsrch_form .row-pt select, .propsrch_form .row-sz select").parents('div.form-row').addClass('nomagnify');
	 jQuery(".propsrch_form .row-pt select, .propsrch_form .row-sz select").select2();
	 
	 jQuery(".rowtype-multiselect select").select2();
	 jQuery(".rowtype-multiselect select").on("change", function (e) {
		
		 var name = "duplicate_"+jQuery(this).attr('name');
		 var name2 = name.replace("[]","");
		 
		 jQuery('.'+name2).remove();
		 jQuery('.'+name2).unbind();
		 var str = '';
		 jQuery(this).children("option:selected" ).each(function() {
			 console.log("A");
			 str += '<input type="hidden" class="'+name2+'" name="'+name+'" value="'+jQuery(this).text()+'">';
		 });
		 console.log("getNewList - multiselect");
		 getNewList("form");
		 jQuery('.propsrch_extras_form').append(str);
	 
	 });
	 jQuery(".rowtype-select select, .rowtype-ref_select select").on("change", function (e) {
		 getNewList("form");
	 });
			
	 jQuery('input[name=tn]').change(function() {
		 console.log("getNewList - input[name=tn]");
		getNewList("form"); 
	 });
	 
	
	 jQuery('.propsrch_extras_form #propsrch_ordersel').change(function () {
		 console.log("getNewList - propsrch_ordersel");
		 jQuery('.propsrch_form input[name="sb"]').val(jQuery(this).val());
		 getNewList("extras"); 
	 });
	 changeSizeUnits(false);
	 jQuery( ".row-un select").change(function () {
		 changeSizeUnits(true);
		 return false;
	 });
//	console.log(propsrch_resulttemplate);
});
var xht_gnl = jQuery.ajax();
function getNewList(mode) {
//	alert('Test');
	//console.log('Test');
	console.log('CALLED getNewList');
	if (mode == "form") {
		var datastring = jQuery('.propsrch_form').serialize();
	} else if (mode == "extras") {
		var datastring = jQuery('.propsrch_extras_form').serialize();
	}
	console.log(datastring);
	jQuery('body').addClass('loading propsrch_ajax_loading');
	jQuery('#propsrch_results').html("Loading");
	if (propsrchaj.length == 0) {
		propsrchaj = "?getnewlist";
	}
	
	var newdatastring = datastring.replace(/psv=(map|list)/,"");
	
	jQuery('.viewaslist').attr('href','?'+newdatastring+'&psv=list'); 
	jQuery('.viewasmap').attr('href','?'+newdatastring+'&psv=map');
	if (mapmode == true) {
		jQuery('body').addClass('propsrchmaploading');
	}
	xht_gnl.abort();
	xht_gnl = jQuery.ajax({
		type: "GET",
		url: propsrchaj,
		data: datastring,
		success: function(response) {
			jQuery('#propsrch_results').html("Done");
			 response = JSON.parse(response);
			 var htmldump = "";
			 mapmarkers = [];
			 var count = 0;
			 for (i in response) {
			     count++;
			 }
			 if (count > 0) {
				 for (i in response) {
					 if (mapmode == true) {
						 for (j in response[i]) {
	//						 console.log(response[i][j][0]+' '+response[i][j][1]);
							 if (typeof mapmarkers[i] === "undefined") {
								 mapmarkers[i] = [];
							 }
							 if (response[i][j][0] == 'post_title') {
								 mapmarkers[i][0] = response[i][j][1]; 
							 }
							 if (response[i][j][0] == 'latlng') {
								 var latlng = response[i][j][1].split(",");
								 mapmarkers[i][1] = latlng[0];
								 mapmarkers[i][2] = latlng[1];
							 }
							 if (response[i][j][0] == 'linktoproperty') {
								 mapmarkers[i][3] = response[i][j][1];
							 }
							 if (response[i][j][0] == 'address') {
								 mapmarkers[i][4] = response[i][j][1];
							 }
						 }
						 
					 } else {
						 var html = propsrch_resulttemplate;
						 for (j in response[i]) {
							 var replacewith = '';
							 if (j == 'funcvals') {
								 for (k in response[i][j]) {
									 if (response[i][j][k][1] === null) {
										 //console.log("FOUND NULL");
										 replacewith = "";
									 } else {
										 replacewith = response[i][j][k][1];
									 }
									 html = html.replaceAll('*{'+response[i][j][k][0]+'}*', replacewith);
								 }
								 
							 } else {
								 //console.log(response[i][j][0]+' '+(typeof response[i][j][1])+' '+ response[i][j][1].length());
								 if (response[i][j][1] === null) {
									 //console.log("FOUND NULL");
									 replacewith = "";
								 } else {
									 replacewith = response[i][j][1];
								 }
								 html = html.replaceAll('*|'+response[i][j][0]+'|*', replacewith);
							 }
						 }
						 htmldump += html;
					 }
				 }
			 } else {
				 htmldump = propsrch_noresulttemplate;
			 }
			 jQuery('#propsrch_results').html(htmldump);
			 if (mapmode == true) {
				jQuery('body').removeClass('propsrchmaploading')
			 }
			 
			jQuery('body').removeClass('propsrch_ajax_loading');
			jQuery('body').removeClass('loading');
			 if (mapmode == true) {
				propsrch_InitMap();
			}
			return false;
		}
	});
	return false;
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	    c = isNaN(c = Math.abs(c)) ? 2 : c, 
	    d = d == undefined ? "." : d, 
	    t = t == undefined ? "," : t, 
	    s = n < 0 ? "-" : "", 
	    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
	    j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };
	 

function propsrch_InitMap () {
//	alert("Map stuff");
	var iconBase = '/wp-content/plugins/philosophy-propertysearch/assets/img/';
    var icons = {
      property: {
        icon: iconBase + 'pin.png'
      }
    };
	// Create a map object and specify the DOM element for display.
    var map = new google.maps.Map(document.getElementById('propsrch_map'), {
      center: {lat: 54.6545512, lng: -3.3410385},
      scrollwheel: false,
      zoom: 6
    });
    if ((typeof mapmarkers !== "undefined") && (mapmarkers.length > 0)) {
    	var marker;
    	var mapcontent = [];
    	var infowindow = new google.maps.InfoWindow();
		for(i in mapmarkers) {
		    // Create a marker and set its position.
			var myLatLng = {lat: parseFloat(mapmarkers[i][1]), lng: parseFloat(mapmarkers[i][2])};
			mapcontent[i]  = '<h3>'+mapmarkers[i][0]+'</h3>';
			mapcontent[i]  += '<p>'+mapmarkers[i][4]+'</p>';
			mapcontent[i]  += '<a href="'+mapmarkers[i][3]+'">View more</a>';
			
		    marker = new google.maps.Marker({
		      map: map,
		      position: myLatLng,
		      icon: icons['property'].icon,
		      title: mapmarkers[i][0]
		    });
		    google.maps.event.addListener(marker, 'click', (function(marker, i) {
		        return function() {
		          infowindow.setContent(mapcontent[i]);
		          infowindow.open(map, marker);
		        }
		      })(marker, i));
		}
    }
    
}
function convertSize (size) {
	size = (size / oldmult) * mult;
	return(size);
}
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function changeSizeUnits (clear) {
	console.log('changeSizeUnits-clear: '+clear);
	oldmult = mult; 
	if (clear) {
		 sizeunits = jQuery( ".row-un select").val();
	}
	 
	var multlabel;
	
	if (typeof unitmults[sizeunits] !== "undefined") {
		multlabel = unitmults[sizeunits][0];
		mult = unitmults[sizeunits][1];
		increm = unitmults[sizeunits][2];
	} else {
		multlabel = unitmults['sqm'][0];
		mult = 1;
		increm = 1;
	}
	
	console.log('changeSizeUnits: '+sizeunits+' | '+mult+' | '+increm);
	if (jQuery('#select-sz').length) {
		console.log('SU: '+sizeunits);
		console.log(arearangeconversions);
		if (arearangeconversions[sizeunits].length) {
			console.log(arearangeconversions[sizeunits]);
			jQuery('#select-sz').html("");
			var newoptionshtml = "";
			for (i in arearangeconversions[sizeunits]) {
				newoptionshtml += '<option value="'+arearangeconversions[sizeunits][i][0]+' - '+arearangeconversions[sizeunits][i][1]+'">'+numberWithCommas(arearangeconversions[sizeunits][i][0])+' - '+numberWithCommas(arearangeconversions[sizeunits][i][1])+'</option>';
			}
			jQuery('#select-sz').html(newoptionshtml);
			 jQuery(".propsrch_form .row-sz select").select2();

			
		} else {
			console.log('Cannot load new units');
		}
		
	} else {
		 var size_from = jQuery( ".row-sz .multitext-container [name=szf]").val();
		 if (size_from == "") {
			 jQuery( ".row-sz .multitext-container [name=szf]").val(0);
			 size_from = 0;
		 }
		 if (typeof max_total_area  === "undefined") {
			 max_total_area = 100000;
		 }
		 
	
		 console.log(multlabel)
		 
		 max_total_area = max_total_area_metres * mult;
		 console.log(max_total_area);
		 max_total_area = Math.ceil(max_total_area/increm)*increm;
		 
		 var size_to = jQuery( ".row-sz .multitext-container [name=szt]").val();
		 if (size_to == "") {
			 jQuery( ".row-sz .multitext-container [name=szt").val(max_total_area);
			 size_to = max_total_area;
		 }
		 if (clear) {
			 size_from = convertSize(size_from);
			 size_to = convertSize(size_to);
			 setSizeSliderVals(size_from, size_to);	
		}
		
		
		 
		 size_from = Math.ceil(size_from);
		 size_to = Math.ceil(size_to);
		 multlabel = " "+multlabel;
		 jQuery('#size-slider-feedback').remove();
		 jQuery('#size-slider-feedback').unbind();
		 jQuery( ".row-sz .multitext-container" ).append('<div id="size-slider-feedback">'+size_from+multlabel+' - '+size_to+multlabel+'</div>');
		 var size_slider_values = [size_from,size_to];
		 
		 jQuery( ".row-sz .multitext-container label").hide();
		 jQuery( ".row-sz .multitext-container input[type=text]").attr('type','hidden');
		 jQuery( "#size-slider" ).slider({
			 range: true,
			 min: 0,
			 max: max_total_area,
			 step: increm,
			 values: size_slider_values,
			 slide: function( event, ui ) {
				 setSizeSliderVals(ui.values[ 0 ], ui.values[ 1 ]);
				 jQuery( "#size-slider-feedback" ).html(ui.values[ 0 ] + multlabel+" - " + ui.values[ 1 ]+multlabel);
			 },
			 change: function (event, ui) {getNewList("form");}
		 });
	}
}
function setSizeSliderVals(from, to) {
	 jQuery( ".row-sz .multitext-container [name=szf]").val(from);
	 jQuery( ".row-sz .multitext-container [name=szt]").val(to);
	 jQuery( ".propsrch_extras_form #duplicate_szf").val(from);
	 jQuery( ".propsrch_extras_form #duplicate_szt").val(to);
}