var mapdivid = "sitemap-interactive-map";
var scale = 1;
var bounds_width = 796;
var bounds_height = 563;
var compwidth = bounds_width;
var compheight = bounds_height;
var currentMousePos = { x: -1, y: -1 };
var menuoverlay;
var menumatrix = {action: {addnewpath: 'Add new shape'}, path: {editpath: 'Edit Shape', deletepath: 'Remove Shape'}}

var activeshape = -1;
var activemode = "notset";

var colours = {
	standard: '#F9C909',
	selected: '#FF0000',
}
var siteplanmap;
var polygon = [];	

var activevertex = 0;
var undocache;



function propertylist (func) {
	h = '<ul class="propertylist">';	
	if (typeof drawshapes !== "undefined") {				
		for (i in drawshapes) {
			if (drawshapes[i][1].length == 0) {
				aclass = 'empty';
				sym = '&#x21B4;';	
			} else {
				aclass = 'done';
				sym = '&#x2713;';
			}
			h += '<li><span>'+sym+'</span><a href="#" data-func="'+func+'" data-id="'+i+'" class="'+aclass+'">'+drawshapes[i][0]+'</a></li>';
		}
	}
	h += '</ul>';
	return (h);
}


jQuery(document).ready(function () {
	
	changemode("default"); 
	siteplaninit();
	bounds_width = jQuery('#'+mapdivid).outerWidth();
	bounds_height = bounds_width * sitemapsrc_scale;
	compwidth = jQuery('.leaflet-overlay-pane img').outerWidth();
	
	
	var menuhtml;
	for(var prop in menumatrix) {
		menuhtml += '<div id="'+prop+'-menu" class="menu-overlay" style="display: none"><ul>';
		for(var action in menumatrix[prop]) {
			menuhtml += '<li>';
			if (action == 'addnewpath') {
				menuhtml += 'Add:'+propertylist('add');
			} else {
				menuhtml += '<a data-func="'+action+'" href="#">'+menumatrix[prop][action]+'</a>';
			}
			menuhtml += '</li>';	
			
		}
		menuhtml += '</ul></div>';
	}
	jQuery('body').prepend(menuhtml);
	
	jQuery('#sitemap-dashboard').on('click', 'button', function () {
		console.log("-----")
		console.log("Dash button: "+jQuery(this).data('action'));
		if (jQuery(this).data('action') == 'draw') {
			changemode(jQuery(this).data('action') );
		} else if (jQuery(this).data('action') == 'cancel') {
			activevertex = 0;
			console.log(undocache);
			changemode("default");
			removeshape(activeshape);
			drawshapes[activeshape][1] = undocache;
			console.log(drawshapes[activeshape]);			
			drawshape(activeshape, colours.standard);
		} else if (jQuery(this).data('action') == 'done') {
			activevertex = 0;
			undocache = [];
			removeshape(activeshape);
			drawshape(activeshape, colours.standard);
			changemode("default");
		}
		update_dashboard();
		
		event.preventDefault();
		event.stopPropagation();
		return false;
	});
	jQuery('body').on('click', '.menu-overlay a', function () {
		console.log("Clicked a menu item");
		
		func = 'action_'+jQuery(this).data('func');
		i = jQuery(this).data('id');
		
		console.log(func+' '+i);
		var fn = window[func];
		if (typeof fn === "function") {
			fn(i);
		} else {
			console.log('No action');
		}
		console.log("Hide menu (done)");
		jQuery('.menu-overlay').hide();
		event.preventDefault();
		event.stopPropagation();
		return false;
	});
	
	//jQuery('body').prepend('<div id="action-menu" class="menu-overlay" style="display: none;"><a data-func="addnewpath" href="#">Add new shape</a></div>');
	//jQuery('body').prepend('<div id="path-menu" class="menu-overlay" style="display: none;"><a data-func="editpath" href="#">Edit shape</a></div>');
	
	jQuery('#'+mapdivid).mousemove(function(event) {
	    currentMousePos.x = event.pageX;
	    currentMousePos.y = event.pageY;
	    var offset = jQuery('#'+mapdivid).offset();
	    var test = convertmouse2coords();	    
	    var mappos = jQuery('.leaflet-map-pane').css('transform').split(', ');
	    var scale = jQuery('.leaflet-overlay-pane img').outerWidth() / compwidth;
	    var debug = '';
	    	debug += 'SCALEW -: '+scale+"\n";
	    	debug += 'VIEWPO w: '+jQuery('#'+mapdivid).outerWidth()+' h: '+jQuery('#'+mapdivid).outerHeight()+"\n";
	    	debug += 'BOUNDS w: '+bounds_width+' h: '+bounds_height+"\n";
	    	debug += 'PANIMG w: '+jQuery('.leaflet-overlay-pane img').outerWidth()+' h: '+jQuery('.leaflet-overlay-pane img').outerHeight()+"\n";
	    	debug += 'OFFSET t: '+offset.top+' l: '+offset.left+"\n";
			debug += 'OVERAL x: '+currentMousePos.x+' y: '+currentMousePos.y+"\n";
			debug += 'RELATI x: '+(currentMousePos.x - offset.left)+' y: '+(currentMousePos.y - offset.top)+"\n";
			debug += 'MAPPOS x: '+parseInt(mappos[4])+' y: '+parseInt(mappos[5])+"\n";
			debug += 'CONVER 1: '+test[1]+' 0: '+test[0]+"\n";
			debug += '----------'+"\n";
			debug += 'activeshape: '+activeshape+"\n";
			debug += 'activevertex: '+activevertex;

		jQuery('#mousedebug').val(debug);
	});	
	jQuery('#'+mapdivid).mousedown(function(event) {
		handleClick('#action-menu', event);
	    event.preventDefault();
		event.stopPropagation();
	    return false;
	});
	/*
	jQuery('#'+mapdivid+' path').mousedown(function(event) {
		handleClick('#path-menu');
		event.preventDefault();
		event.stopPropagation();
		return false;
	});
	*/
	/*
	jQuery('#action-menu a').click(function () {
		// find object
		var fn = window[jQuery(this).data('func')];
		// is object a function?
		if (typeof fn === "function") fn();
		jQuery('.menu-overlay').fadeOut(200);
		event.preventDefault();
		event.stopPropagation();
		return false;
	});
	*/
	jQuery('.menu-overlay, #'+mapdivid).contextmenu(function() {
	    return false;
	});
});
function handleClick(what, event) {
	console.log("handleClick("+what+")");
	console.log(event);
	console.log('Last: '+jQuery(':hover').last().attr('id'));
	
	if ((jQuery('.menu-overlay').is(':visible')) && (jQuery(':hover').last().attr('id') == 'sitemap-interactive-map')) {
		console.log("Hide menu");
		jQuery('.menu-overlay').hide();
		return false;
	}
	
	
	if (event.which == 3) {
		console.log("Right click...");
	} else if (event.which == 1) {
		console.log("Left click...");
	} else {
		console.log("?? 1click...");
	}
	if (
			(activemode == 'default') && 
			(
				((event.which == 3) && (what == '#action-menu') && (jQuery(':hover').last().attr('id') == 'sitemap-interactive-map'))
				||
				((what == '#path-menu'))
			)
		)
			{
		console.log("Show menu");
		jQuery(what).fadeIn(200);
		jQuery('.menu-overlay:not('+what+')').fadeOut(200);
		jQuery(what).css({top: currentMousePos.y, left: currentMousePos.x});
		
	} else if (activemode == 'draw') {
		if (activevertex == 0) {
			undocache = drawshapes[i][1];
			drawshapes[activeshape][1] = [];
			removeshape(activeshape);
			
		}
		drawshapes[activeshape][1][activevertex] = convertmouse2coords();
		activevertex++;
		removeshape(activeshape);
		drawshape(activeshape, colours.selected);
		update_dashboard();
	}
//	event.preventDefault();
//	event.stopPropagation();
}

jQuery(window).resize(function () {
	console.log("Document Resize");
	setScale();
});

function setScale() {
	console.log("Set Scale");
	if (window.innerWidth < 798) {
		scale = (window.innerWidth / 798);		
	} else {
		scale = 1;
	}
}
function convertmouse2coords() {
	var offset = jQuery('#'+mapdivid).offset();
	var relx = currentMousePos.x - offset.left;
	var rely = currentMousePos.y - offset.top + 28;
	var mappos = jQuery('.leaflet-map-pane').css('transform').split(', ');
	mpx = parseInt(mappos[4]);
	mpy = parseInt(mappos[5]);
	
	return([((jQuery('.leaflet-overlay-pane img').outerHeight() - rely ) ), (relx-mpx)])
}
function addnewpath () {
	console.log("ADd new path?");
	var start = convertmouse2coords();
	//drawshapes[] = {};
}
function editpath () {
	console.log("Edit path?");
}

function siteplaninit () {
	if (jQuery('#'+mapdivid).length > 0) {
		console.log("siteplaninit > Siteplan Scale: "+scale);
		
		// Reset the DIV
		document.getElementById(mapdivid).outerHTML = '<div id="'+mapdivid+'"></div>';
		
		//	Init the map 		
		siteplanmap = L.map(mapdivid, {
				 crs: L.CRS.Simple
		});
		var bounds = [[0,0], [(bounds_height*scale),(bounds_width*scale)]];
		var image = L.imageOverlay(sitemapsrc, bounds).addTo(siteplanmap);
	
		
		// zoom the map to the rectangle bounds
		siteplanmap.createPane('labels');
		
		siteplanmap.fitBounds(bounds);	
		
		// Draw our shapes		
					console.log(drawshapes);
		if (typeof drawshapes !== "undefined") {
			var galLinks = document.getElementById('imgLinks');
			
			for (i in drawshapes) {
				
				
				console.log('i: '+i);
				console.log(drawshapes[i][1]);				
				drawshape(i, colours.standard);						
				
				//polygon.bindPopup('<a href="'+drawshapes[i][2]+'">'+drawshapes[i][0]+'.</a>');
				polygon[i].bindTooltip(drawshapes[i][0]);
			}
		}		
	}
}
function drawshape (shape_id, colour) {
	console.log('-----');
	console.log('Draw: '+drawshapes[shape_id][3]+' | Color: '+colour);
	var draw = drawshapes[shape_id][1];
	
	for (di in draw) {
		draw[di][0] = draw[di][0] * scale;
		draw[di][1] = draw[di][1] * scale;
	}
	polygon[drawshapes[shape_id][3]] = L.polygon (draw, {color: colour, fillColor: colour, fillOpacity: .3, weight: 2}).addTo(siteplanmap);
	i = drawshapes[shape_id][3];
	(function (i) {
		polygon[i].on('mousedown',function (e) {
			console.log("-----");
			console.log("Polygon clicked: "+i);
			handleClick('#path-menu', e);
			if (activemode == 'default') {
				if (activeshape > -1) {
					console.log("remove red shape "+activeshape);
					removeshape(activeshape);
					console.log("add yellow shape "+activeshape);
					drawshape(activeshape, colours.standard);
				}
				activeshape = i;
				console.log("active shape is now "+activeshape);
				console.log("remove yellow shape "+activeshape);
				removeshape(activeshape);
				console.log("add red shape "+activeshape);
				drawshape(activeshape, colours.selected);
				
				update_dashboard();
			}
			event.preventDefault();
			event.stopPropagation();
			
		});
	})(i);
	
}
function action_remove(i) {
	console.log('-----');
	console.log('Remove '+i);
	removeshape(i);
	drawshapes[i][1] = [];
	/*
	coords = convertmouse2coords();
	console.log(coords);
	drawshapes[i][1] = [coords, [coords[0]+100, coords[1]], [coords[0]+100, coords[1]+100], [coords[0], coords[1]+100]];
	*/
}
function removeshape(i) {	
	console.log('Remove '+i);
	siteplanmap.removeLayer(polygon[i]);
}
function action_add (i) {
	console.log('-----');
	console.log('Add '+i);
	coords = convertmouse2coords();
	console.log(coords);
	
	if (activeshape > -1) {
		console.log("remove red shape "+activeshape);
		removeshape(activeshape);
		console.log("add yellow shape "+activeshape);
		drawshape(activeshape, colours.standard);
	}
	console.log("Blah");
	activeshape = i;
	
	removeshape(i);
	drawshapes[i][1] = [coords, [coords[0]+100, coords[1]], [coords[0]+100, coords[1]+100], [coords[0], coords[1]+100]];
	console.log(drawshapes[i]);
	drawshape(i, colours.selected);
	
	changemode('draw');
	
}
function update_dashboard () {
	console.log("Update dashboard");
	if ((activeshape > -1) && (typeof drawshapes[activeshape] !== "undefined")) {
		jQuery('#sitemap-dashboard').html("<p>Selected: <span>"+drawshapes[activeshape][0]+'</span> <a target="_blank" href="http://greenhurst.loc/wp-admin/post.php?post='+drawshapes[activeshape][3]+'&action=edit">[^]</a></p>');
		jQuery('#sitemap-dashboard').append('<p>Mode: <span>'+activemode+'</span></p>');
		
		/*
		cdebug = [];
		for (c in drawshapes[activeshape][1]) {
			cdebug[c] = '['+drawshapes[activeshape][1][c][0]+','+drawshapes[activeshape][1][c][1]+']';
		}
		cdebug = cdebug.toString();
		jQuery('#sitemap-dashboard').append('<br><input style="width: 100%;" value="['+cdebug+']">');
		*/	
		jQuery('#sitemap-dashboard').append('<div id="sitemap-dashboard-buttons"></div>');	
		if (activemode == 'draw') {
			jQuery('#sitemap-dashboard-buttons').append('<button data-action="cancel">Cancel</button>');
			jQuery('#sitemap-dashboard-buttons').append('<button data-action="done">Done</button>');
		} else {
			jQuery('#sitemap-dashboard-buttons').append('<button data-action="draw">Draw</button>');
		}
	} else {
		jQuery('#sitemap-dashboard').html("No shape selected");
	}
	console.log(drawshapes);
	var hiddenfields = "";
	for (i in drawshapes) {
		hfa = [];
		for (c in drawshapes[i][1]) {
			hfa[c] = '['+drawshapes[i][1][c][0]+','+drawshapes[i][1][c][1]+']';
		}
		hiddenfields += '<input type="hidden" name="shape['+i+']" value="['+hfa.toString()+']">';
	}
	jQuery('#hiddenfields').html(hiddenfields);
}
function changemode(mode) {
	console.log('Change mode: '+activemode+' => '+mode);
	jQuery('#sitemap-interactive-outer').removeClass('mode-'+activemode);
	
	if (mode != "default") {
		siteplanmap.dragging.disable();
		siteplanmap.touchZoom.disable();
		siteplanmap.doubleClickZoom.disable();
		siteplanmap.scrollWheelZoom.disable();
		siteplanmap.boxZoom.disable();
		siteplanmap.keyboard.disable();
		if (siteplanmap.tap) siteplanmap.tap.disable();
	} else if (activemode != "notset") {
		siteplanmap.dragging.enable();
		siteplanmap.touchZoom.enable();
		siteplanmap.doubleClickZoom.enable();
		siteplanmap.scrollWheelZoom.enable();
		siteplanmap.boxZoom.enable();
		siteplanmap.keyboard.enable();
		if (siteplanmap.tap) siteplanmap.tap.enable();
	}
	if (mode == 'draw') {
		removeshape(activeshape);
	}
	activemode = mode;
	jQuery('#sitemap-interactive-outer').addClass('mode-'+mode);
	update_dashboard();
}