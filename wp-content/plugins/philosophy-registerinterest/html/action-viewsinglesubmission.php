<?php
if (empty($RI)) {
	$RI = new RegisterInterest();
}
if ((!empty($_GET['submission'])) && (!empty($_GET['form']))) {
	$submission_data = $RI->get_submission_data($_GET['form'], $_GET['submission']);
	echo '<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th>Field</th>		
					<th>Value</th>		
				</tr>
			</thead>
			';	
	
	foreach ($submission_data as $d) {
		echo '<tr>
					<td>'.$d->label.'</td>
					<td>'.$d->value.'</td>
				</tr>';
	}
	
	echo '</table>';	
}

?>

