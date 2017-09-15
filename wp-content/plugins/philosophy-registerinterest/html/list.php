<?php
echo '<ul>';
foreach ($data['list_utils'] as $h => $l) {
	if (RIDB_shownav($l))  {
		echo '<li><a class="button" href="?page=register-interest&ri-action='.$h.'">'.$l[0].'</a><p>'.$l[1].'</p></li>';
	}
}
echo '</ul>';