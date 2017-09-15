<h2>View Form Submissions</h2>
<?php 
if ((!empty($_GET['submission'])) && (!empty($_GET['form']))) {
	include("action-viewsinglesubmission.php");
} else if (!isset($_GET['form'])) {
	echo 'Which form would you like to view submissions for?<ul>';
	foreach ($data['all_forms'] as $form) {
		echo '<li><a href="?page=register-interest&ri-action=viewsubmissions&form='.$form->id.'">'.$form->title.'</a></li>';
	}
	echo '</ul>';
} else {
	echo '<p><a class="button button-primary" href="?page=register-interest&ri-action=viewsubmissions&form='.$_GET['form'].'&export=csv">CSV Export</a></p>';
	$tablehtml = '<table id="submission-table" class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>Date</th>		
						<th>Email</th>		
						<th>Interested In</th>		
						<th>IP Address</th>		
						<th>Browser</th>		
						<th>Platform</th>		
						<th>Email Read</th>		
						<th>More</th>		
					</tr>
				</thead>
				<tbody>';
	
	
	
	$browsers = listOfBrowsers();
	
	
	$stats = array();
	$statheaders = [
		'devices'=>'What devices?',
		'browser'=>'What browser?',
		'sources'=>'Source of Enquiry'
	];
	$stats['devices'] = array();
	$stats['browser'] = array();
	$stats['sources'] = array();
	
	$count = 0;
	foreach ($data['all_submissions'] as $s) {
		$bdetect = new Browser($s->ua);
		
		$platform_info = $bdetect->isMobile() ? 'Mobile' : 'Desktop';
		$browser = $bdetect->getBrowser();
		$interest = ($s->post_id) ? '<a href="'.get_permalink($s->post_id).'" target="_blank"> '.$s->post_title.' ('.$s->post_type.')</a>' : 'N/A';
		
		$hasread = $s->has_read ? 'Yes' : 'No';
		
		$stats['devices'][$platform_info] = (empty($stats['devices'][$platform_info]))
			? 1
			: $stats['devices'][$platform_info] + 1;
		$stats['browser'][$browser] = (empty($stats['browser'][$browser]))
			? 1
			: $stats['browser'][$browser] + 1;
		
		$source = $RI->get_submission_data_row($_GET['form'], $s->id, 'source');
		if (!empty($source)) {
			$stats['sources'][$source->value] = (empty($stats['sources'][$source->value])) 
				? 1 
				: $stats['sources'][$source->value] + 1;
		}
		$tablehtml .= '<tr>
				<td>'.date(REGINT_DATEFORMAT, strtotime($s->date)).'</td>
				<td><a class="emaillink" href="mailto:'.$s->email.'">'.$s->email.'</a></td>
				<td>'.$interest.'</td>
				<td>'.$s->ip_address.'</td>
				<td><div class="ua_hover_toggle"><span class="browser-logo '.str_replace(' ','-',strtolower($bdetect->getBrowser())).'"></span>'.ucwords($bdetect->getBrowser()).'<br><span class="useragentdetail">'.$s->ua.'</span></div></td>
				<td>'.$platform_info.'</td>
				<td><span class="hasread hasread-'.strtolower($hasread).'">'.$hasread.'</span><a  data-submission="'.$s->id.'"  class="button viewemaillog" href="?page=register-interest&ri-action=emaillog&submission='.$s->id.'">Log</a></td>
				<td><a data-submission="'.$s->id.'" class="button viewsubmission" href="?page=register-interest&ri-action=viewsubmissions&form='.$_GET['form'].'&submission='.$s->id.'">Submission</a></td>
			</tr>';
		$count++;
	}
	$tablehtml .= '
			</tbody>
		</table>'; 
	foreach ($stats as $h => $d) {
		echo '<div class="statbox">';
		if (isset($statheaders[$h])) {
			echo '<h3>'.$statheaders[$h].'</h3>';
		}
		echo '<table class="stattable">';
		foreach ($d as $k => $c) {
			$p = round(($c/$count)*100,1);
			echo '<tr>
				<th>'.$k.'</th>
				<td>'.$c.'</td>
				<td>'.$p.'%</td>
			</tr>';		
		}
		echo '</table>';
		echo '</div>';
	}
			
	echo $tablehtml;
}
?>
<div id="RI-popup-overlay" style="display: none;">
<div id="RI-popup-content"></div>
</div>
<?php 
/* ?>
<form method="post" action="http://catalyst.loc/wp-admin/admin-ajax.php">
<input name="submission">
<input name="form">
<input type="hidden" name="action" value="getsinglesubmission">
<button>GO</button>
</form>
*/ ?>