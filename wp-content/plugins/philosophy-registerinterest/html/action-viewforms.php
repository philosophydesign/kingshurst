<h2>View Forms</h2>
<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Reference</th>
			<th>Title</th>
			<th colspan="2">Options</th>
		</tr>
	</thead>
	<tbody>
<?php 
foreach ($data['all_forms'] as $f) {
	echo '<tr>
		<td><h3>'.$f->title.'</h3></td>
		<td>'.$f->reference.'</td>
		<td><a href="?page=register-interest&ri-action=editform&form='.$f->id.'">EDIT</a></td>
		<td><a href="?page=register-interest&ri-action=viewsubmissions&form='.$f->id.'">SUBMISSIONS</a></td>
	</tr>';
}
?>
</tbody>
</table>