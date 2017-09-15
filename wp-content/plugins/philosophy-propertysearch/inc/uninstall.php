#<?php
global $wpdb;
$q = 'show tables like propsrch_postlinks';
$r = $wpdb->get_row($q);
if (empty($r)) {
	#$q= 'DROP TABLE propsrch_postlinks';
	#$wpdb->query($q);
}