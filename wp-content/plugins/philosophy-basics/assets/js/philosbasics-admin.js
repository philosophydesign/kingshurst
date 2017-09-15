function cleanStr(strin) {
	return(strin.replace(/[^a-zA-Z\-\s]/g, '').replace(/\s+/g, '-').toLowerCase());
}