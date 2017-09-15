<h2><?php 
if ($_GET['spid'] == 'new') {
	echo 'Create new siteplan';
} else {
	echo 'Edit siteplan';
}

?></h2>
<form method="post" action="?page=siteplans&spid=<?php echo $_GET['spid'] ?>&spdo=save" enctype="multipart/form-data">
<?php 
$FB = new GesForms();
$FB->values = getDataForSiteplan($_GET['spid']);
$output = $FB->outputFields($FB->makeStructObj(getsiteplanadminformstruct()));
echo $output;
?>
<div id="hiddenfields"></div>
<button>Save Siteplan</button>
<?php

$src = wheresmyplanstuff( $_GET['spid'], true).'/'.$FB->values['siteplan'];
if (!empty($FB->values['siteplan'])) {
	$i = getimagesize(wheresmyplanstuff( $_GET['spid'], false).'/'.$FB->values['siteplan']);
?>
<script type="text/javascript">
	var sitemapsrc = "<?php echo $src ?>";
	var sitemapsrc_width = <?php echo $i[0] ?>;
	var sitemapsrc_height = <?php echo $i[1] ?>; 
	var sitemapsrc_scale = <?php echo $i[1] / $i[0] ?>;
	var drawshapes = new Array();
<?php 
$x = 0;
$properties = get_posts([
		'post_type'=>'properties',
		'post_status'=>'publish',
		'posts_per_page'=> -1,
		'orderby'=>'menu_order',
		'order'		=>'ASC'
]);
foreach ($properties as $p) {
	$shape = get_field('siteplan_coordinates', $p->ID);
	$link = get_permalink($p->ID);
	if (!empty($shape)) {
		$shapes[$p->post_name] = [$p->post_title,$shape,$link,$p->ID];
	} else {
		$shapes[$p->post_name] = [$p->post_title,'[]',$link,$p->ID];
	}
}

foreach ($shapes as $k => $s) {
	echo '// Draw shape '.$k."\n";
	echo '	drawshapes['.$s[3].'] = ["'.$s[0].'",'.$s[1].',"'.$s[2].'",'.$s[3].']'.";\n";
	$x++;
}
?>
</script>
<p><textarea readonly="readonly" id="mousedebug"></textarea></p>
<div id="sitemap-dashboard"></div>
<div id="sitemap-interactive-outer">
	<div id="sitemap-interactive-map"></div>
</div>
<?php }?>
</form>