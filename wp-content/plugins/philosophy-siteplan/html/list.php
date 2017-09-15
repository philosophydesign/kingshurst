<?php 
$list = getSiteplanList();
?>
<h2>View all siteplans</h2>
<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th>
				Title
			</th>
		</tr>
	</thead>
	<tbody>
	
<?php 
foreach ($list as $l) {
	echo '<tr>
			<td>
				<a href="?page=siteplans&spid='.$l['folder'].'">'.$l['title'].'</a>
			</td>
			<td>
				<a class="button" href="?page=siteplans&spid='.$l['folder'].'">Edit</a>
			</td>
		</tr>';
}
?>
	</tbody>
	</table>