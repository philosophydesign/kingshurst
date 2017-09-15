function philossp_InitMap () {
	if (jQuery('#siteplan-map').length) {
		function drawSitePlan () {
	
		var map = new google.maps.Map(document.getElementById('siteplan-map'), {
	          zoom: 19,
	          center: {lat: 52.0917877, lng: -1.8914668},
	         // mapTypeId: 'satellite'
	        });

		var bounds = new google.maps.LatLngBounds(
	            new google.maps.LatLng(52.0913012,-1.8948589), // SW
	            new google.maps.LatLng(52.0925486,-1.8912593)); // NE

	        // The photograph is courtesy of the U.S. Geological Survey.
	        /*var srcImage = 'https://developers.google.com/maps/documentation/' +
	            'javascript/examples/full/images/talkeetna.png';*/
			var srcImage = '/wp-content/themes/greenhurst-philosophy-v1.2/assets/img/siteplantest.png';

	        // The custom USGSOverlay object contains the USGS image,
	        // the bounds of the image, and a reference to the map.
	        overlay = new USGSOverlay(bounds, srcImage, map);
	        
	        
	        
	        var strictBounds = new google.maps.LatLngBounds(
	        		  new google.maps.LatLng(52.0913012,-1.8948589), // SW
	  	            new google.maps.LatLng(52.0925486,-1.8912593) // NE
            );

            // Listen for the dragend event
            google.maps.event.addListener(map, 'dragend', function() {
                if (strictBounds.contains(map.getCenter())) return;
                console.log("were out of bounds");
                // We're out of bounds - Move the map back within the bounds
                var c = map.getCenter(),
                x = c.lng(),
                y = c.lat(),
                maxX = strictBounds.getNorthEast().lng(),
                maxY = strictBounds.getNorthEast().lat(),
                minX = strictBounds.getSouthWest().lng(),
                minY = strictBounds.getSouthWest().lat();

                if (x < minX) x = minX;
                if (x > maxX) x = maxX;
                if (y < minY) y = minY;
                if (y > maxY) y = maxY;

                map.setCenter(new google.maps.LatLng(y, x));
            });
	        
	}
	}
	 var overlay;
     USGSOverlay.prototype = new google.maps.OverlayView();

/** @constructor */
function USGSOverlay(bounds, image, map) {

  // Initialize all properties.
  this.bounds_ = bounds;
  this.image_ = image;
  this.map_ = map;

  // Define a property to hold the image's div. We'll
  // actually create this div upon receipt of the onAdd()
  // method so we'll leave it null for now.
  this.div_ = null;

  // Explicitly call setMap on this overlay.
  this.setMap(map);
}

/**
 * onAdd is called when the map's panes are ready and the overlay has been
 * added to the map.
 */
USGSOverlay.prototype.onAdd = function() {

  var div = document.createElement('div');
  div.style.borderStyle = 'none';
  div.style.borderWidth = '0px';
  div.style.position = 'absolute';

  // Create the img element and attach it to the div.
  var img = document.createElement('img');
  img.src = this.image_;
  img.style.width = '100%';
  img.style.height = '100%';
  img.style.position = 'absolute';
  div.appendChild(img);

  this.div_ = div;

  // Add the element to the "overlayLayer" pane.
  var panes = this.getPanes();
  panes.overlayLayer.appendChild(div);
};

USGSOverlay.prototype.draw = function() {

  // We use the south-west and north-east
  // coordinates of the overlay to peg it to the correct position and size.
  // To do this, we need to retrieve the projection from the overlay.
  var overlayProjection = this.getProjection();

  // Retrieve the south-west and north-east coordinates of this overlay
  // in LatLngs and convert them to pixel coordinates.
  // We'll use these coordinates to resize the div.
  var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
  var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

  // Resize the image's div to fit the indicated dimensions.
  var div = this.div_;
  div.style.left = sw.x + 'px';
  div.style.top = ne.y + 'px';
  div.style.width = (ne.x - sw.x) + 'px';
  div.style.height = (sw.y - ne.y) + 'px';
  console.log("W: "+div.style.width+" H: "+div.style.height);
};

// The onRemove() method will be called automatically from the API if
// we ever set the overlay's map property to 'null'.
USGSOverlay.prototype.onRemove = function() {
  this.div_.parentNode.removeChild(this.div_);
  this.div_ = null;
};
drawSitePlan();
}