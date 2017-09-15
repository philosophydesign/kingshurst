var googlemapisready = false;
jQuery(document).ready(function () {
	console.log("Document is ready");
	if (googlemapisready) {
		rungooglemapscallbacks(); 
	} else {
		var waitmax = 10;
		var waitcount = 0;
		waitingforgoogle = setInterval(function () {
			console.log('googlemapisready: '+googlemapisready);
			if (googlemapisready) {
				rungooglemapscallbacks();
			} 
			waitcount++;
			if (waitcount > waitmax) {
				clearInterval(waitingforgoogle);
			}
			
		}, 200);
	}
});

function googlemaploaded () {
	console.log("Google Maps is now ready!");
	googlemapisready = true;
}
function rungooglemapscallbacks () {
	console.log("Run the callbacks now");
	console.log(googlemapcallbacks.length+" callbacks found");
	console.log(googlemapcallbacks);
	for (i in googlemapcallbacks) {
		var fn = window[googlemapcallbacks[i]];
		if (typeof fn === "function") {
			console.log(googlemapcallbacks[i]+' about to be called');
			fn();
		} else {
			console.log('No callback');
		}
	}
}