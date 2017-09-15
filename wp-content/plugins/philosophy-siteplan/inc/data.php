<?php
class SiteplanData {
	var $spid;
	function __construct () {
		
	}
	function wherestheplanstuff () {
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];
		$dir .= '/'.PHILOSP_folder;
		return($dir);
	}
	function wheresmyplanstuff ($siteplan="") {
		$dir = wherestheplanstuff ();
		if ($siteplan) {
			$dir .= "/".$siteplan;
			if (!file_exists($dir)) {
				mkdir($dir);
				saveDataFile($siteplan);
			}
		}
		return($dir);
	}
	function saveDataFile($siteplan, $data=array()) {
		if (empty($data)) {
			$data['date_created'] = date('Y-m-d H:i:s');
		}
		$data['date_saved'] = date('Y-m-d H:i:s');
		
		$dir = wheresmyplanstuff($siteplan);
		$fn = $dir."/siteplan.json";
		if (!file_exists($fn)) {
			$f = fopen($fn, "w");
			fwrite($f, json_encode($data));
			fclose($f);
		}
		
	}
}