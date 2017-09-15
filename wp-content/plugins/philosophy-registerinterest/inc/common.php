<?php
function RIDB($d) {
	//echo $d.'<br>';
}
function RIDB_shownav($m) {
	if (!empty($m[3])) {
		return false;
	}
	if ((empty($m[2])) || ((!empty($m[2])) && ( current_user_can( 'manage_options' ))))  {
		return true;
	} else {
		return false;
	}
}

function listFormMethods () {
	foreach(get_class_methods('GesForms') as $f) {
		if (strstr($f, 'make_')) {
			$list[] =  str_replace('make_', '', $f);
		}
	}
	sort($list);
	foreach ($list as $k) {
		echo "'$k',<Br>";
	}
	exit;
}
function listOfBrowsers () {
	$browsers = array('chrome','ie','safari','opera','firefox');
	return($browsers);
}
function PHILOSRI_handleSubmit() {
	if ((empty($_POST['philosri_formref'])) || (empty( $_SESSION['philosri_nonce'][$_POST['philosri_formref']]))) {
		if ((isset($_GET['philosri'])) && ($_GET['philosri'] == 'submitajax')) {
			ajaxrespond (false, 'no_nonce');
		} else {
			header("location: /?no_nonce");
			exit;
		}
	}
	if ($_POST['philosri_nonce'] != $_SESSION['philosri_nonce'][$_POST['philosri_formref']]) {
		#echo $_POST['philosri_nonce'].' != '.$_SESSION['philosri_nonce'];
		if ((isset($_GET['philosri'])) && ($_GET['philosri'] == 'submitajax')) {
			ajaxrespond (false, 'bad_nonce');
		} else {
			header("location: /?badnonce");
			exit;
		}
	} else {
		$RI = new RegisterInterest();
		$RI->handle_submission($_POST['philosri_formref']);
		
		if ((isset($_GET['philosri'])) && ($_GET['philosri'] == 'submitajax')) {
			ajaxrespond (true, 'sent');
		} else {
			$url = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
			if (strstr($url,'?')) {
				$url .= '&';
			} else {
				$url .= '?';
			}
			$url .= 'philosri_status=complete';
			header("location: ".$url);
			exit;
		}
		
		
	}
}