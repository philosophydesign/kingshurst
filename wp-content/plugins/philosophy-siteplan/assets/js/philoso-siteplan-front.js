jQuery(document).ready(function () {
	console.log("Document Ready");
	setScale();
	siteplaninit();
	convertimages();
});
jQuery(window).resize(function () {
	console.log("Document Resize");
	setScale();
	siteplaninit();
	convertimages();
});

var scale = 1;
function setScale() {
	console.log("Set Scale");
	if (window.innerWidth < 798) {
		scale = (window.innerWidth / 798);		
	} else {
		scale = 1;
	}
}

var bounds_width = 796;
var bounds_height = 563;
function convertimages () {
	console.log("Convert Images");
	var lib = 0;
	//var libimages = [];
	jQuery('.leafletimgbox').remove();
	jQuery('.leafletimgbox').unbind();
	jQuery('.leafletimg').each(function () {
		var src = jQuery(this).attr('src');
		console.log(src);
		var w = jQuery(this).width();
		 
		var h = (w / 3) * 2;
		console.log("leafletimg", h+" "+w);
		var libounds = [[0,0], [(jQuery(this).height()*scale),(jQuery(this).width()*scale)]];
		
		jQuery(this).after('<div class="leafletimgbox" id="leafletimgbox-'+lib+'"></div>');
		//libimages[lib] = src;
		var libmap = L.map('leafletimgbox-'+lib, {
			crs: L.CRS.Simple
		});
		//src = "/wp-content/uploads/2017/04/Plan01_1st.png";
		var liimage = L.imageOverlay(src, libounds).addTo(libmap);
		
		libmap.createPane('labels');		
		libmap.fitBounds(libounds);
		
		jQuery(this).hide();
		lib++;
	});
}

function siteplaninit () {
	if (jQuery('#siteplan-map').length > 0) {
		console.log("siteplaninit > Siteplan Scale: "+scale);
		
		// Reset the DIV
		document.getElementById('siteplan-map').outerHTML = '<div id="siteplan-map"></div>';
		
		//	Init the map 		
		var siteplanmap = L.map('siteplan-map', {
				 crs: L.CRS.Simple
		});
		var bounds = [[0,0], [(bounds_height*scale),(bounds_width*scale)]];
		var image = L.imageOverlay('/wp-content/themes/greenhurst-philosophy-v1.3/assets/img/v3fi/site-plan-a2-colour-0002.jpg', bounds).addTo(siteplanmap);
	
		// Draw our shapes		
		var polygon = [];				
		if (typeof drawshapes !== "undefined") {
			var galLinks = document.getElementById('imgLinks');

			for (i in drawshapes) {
				console.log(drawshapes[i][1]);				
				var draw = drawshapes[i][1];
				
				for (di in draw) {
					draw[di][0] = draw[di][0] * scale;
					draw[di][1] = draw[di][1] * scale;
				}
				
				
				polygon[i] = L.polygon (draw, {color: "transparent", fillColor: "#000000", fillOpacity: 0, weight: 2}).addTo(siteplanmap);				
				//polygon.bindPopup('<a href="'+drawshapes[i][2]+'">'+drawshapes[i][0]+'.</a>');
				polygon[i].bindTooltip(drawshapes[i][0]);
				(function (i) {
					polygon[i].on('click',function (e) {
						window.location = drawshapes[i][2];			
					});
				})(i);
				
			}
		}
		
		
		
		
		// zoom the map to the rectangle bounds
		siteplanmap.createPane('labels');
		
		siteplanmap.fitBounds(bounds);
	}
}
//