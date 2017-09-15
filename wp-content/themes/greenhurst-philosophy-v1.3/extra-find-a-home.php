<div class="extrapage property-search bblue">
	<div class="container">
		<div class="row">
			<div class="extracontent col-md-12 col-sm-12 col-xs-12">
				<h2>Property Search</h2>
				<hr class="page-spacer spacer-1"/>


<?php 
$properties = get_posts([
		'post_type'=>'properties',
		'post_status'=>'publish',
		'posts_per_page'=> -1,
		'orderby'=>'menu_order',
		'order'		=>'ASC'
]);
echo '<table id="property-table" class="hidden-xs">
		<thead>
			<tr>
				<th class="pdt-plot">House No.</th>
				<th class="pdt-beds">No. beds</th>
				<th class="pdt-type">Type</th>
				<th class="pdt-sqf">Approx gross <br> internal sq ft</th>
				<th class="pdt-parking">Parking</th>
				<th class="pdt-price">Price</th>
				<th class="pdt-availability">Availability</th>
				<th class="pdt-link"></th>
			</tr>
		</thead>
			';

$nv = new NumericVals();
$pt = new PropTaxonomy();

$statustext = [
	'forsale'	=>	'Available',
	'reserved'	=>	'RESERVED',
	'sold'		=>	'Sold'
];

$shapes = array();

$h2buy = [];
foreach ($properties as $p) {
	
	
	$plot = get_field('plot_no', $p->ID);
	if (get_field('help_to_buy', $p->ID)) {
		$h2buy[] = $plot;
	}
	$parking = get_field('parking', $p->ID);
	
	$sqm = $nv->get_value($p->ID, 'total_area');
	$sqm = (!empty($sqm)) ? $sqm->value : 0;
	$sqf = number_format(round($sqm * 10.7639),0);
	
	$type = $pt->getValByPostAndCat($p->ID, 'property_type');
	$te = [];
	foreach ($type as $t) {
		$te[] = $t->term_value;
	}
	$te = implode(', ', $te);
	
	$no_bedrooms = reset(get_post_meta($p->ID, 'propsrch_no_bedrooms'));
	$tenure = reset(get_post_meta($p->ID, 'propsrch_tenure'));
	$price = $nv->get_value($p->ID, 'price');
	$price = (isset($price->value)) ? $price->value : '';
	
	
	
	$link = get_permalink($p->ID);
	$availability = reset(get_post_meta($p->ID, 'propsrch_status'));
	$availability = (isset($statustext[$availability])) ? $statustext[$availability] : ''; 
	if ($availability == 'RESERVED') {$price = '';}
	else if ($price == '0.00') {$price = 'POA';}
	else {$price = '&pound;'.number_format($price);}
	
	$shape = get_field('siteplan_coordinates', $p->ID);
	if (!empty($shape)) {
		$shapes[$p->post_name] = [$p->post_title,$shape,$link]; 
	}
	
	echo '<tr>
				<td class="pdt-plot">'.$plot.'</td>
				<td class="pdt-beds">'.$no_bedrooms.'</td>
				<td class="pdt-type">'.$te.'</td>
				<td class="pdt-sqf">'.$sqf.'</td>
				<td class="pdt-parking">'.$parking.'</td>
				<td class="pdt-price">'.$price.'</td>
				<td class="pdt-availability">'.$availability.'</td>
				<td class="pdt-link"><a href="'.$link.'">View details</a></td>
			</tr>';
	$mobstuff .= '<div class="mobile-property-result">
				<p><span>Plot</span>  <span>'.$plot.'</span></p>
				<p><span>Beds</span> <span>'.$no_bedrooms.'</span></p>
				<p><span>Type</span> <span>'.$te.'</span></p>
				<p><span>Sq Ft</span> <span>'.$sqf.'</span></p>
				<p><span>Parking</span> <span>'.$parking.'</span></p>
				<p><span>Price</span> <span>'.$price.'</span></p>
				<p><span>Availability</span> <span>'.$availability.'</span></p>
				<a href="'.$link.'">View details</a>
						
		</div>';
	
	/*
	echo '<div class="sr-row">
			<div class="sr-col">'.$plot.'</div>
			<div class="sr-col">'.$no_bedrooms.'</div>
			<div class="sr-col">'.$type.'</div>
					
			<div class="sr-col">'.$parking.'</div>					
		</div>';
		*/
	
}

echo '</table>';
echo '<div id="h2bnotice"><img  width="100" height="100" src="'.get_stylesheet_directory_uri().'/assets/img/v3fi/htb-logo.png"/>';
echo '<p><strong>Help to Buy is available on ';
echo (count($h2buy) > 1) ? 'plots ' : 'plot ';
$list = implode(', ', $h2buy);
$search = ', ';
$replace = ', and ';

echo strrev(implode(strrev($replace), explode(strrev($search), strrev($list), 2))); //output: bourbon, scotch, and beer
echo '</strong>
		<br>Please <a href="/contact/">contact our sales agents to find out more</a></p>
		</div>';


echo '<div class="visible-xs">'.$mobstuff.'</div>';
?>
			<a target="_blank" href="/kingshurst-brochure-19-05-17.pdf" id="download-brochure">Download brochure</a>
			
			<p>Please refer to individual floorplans. Floorplans are not to scale and are 
			indicative only. Location of windows, doors, kitchen units and appliances, and 
			bathroom fittings may differ. All measurements have been prepared from preliminary 
			plans and are intended to give a general indication of the proposed development 
			and the size and layout of individual plots. Measurements shown are maximum room 
			measurements. Total areas shown are maximum and may vary for each unit within a type. 
			All dimensions are quoted in conformity with RICS (GIA EIFA) code of measuring 
			 practice (6th edition).</p>
</div>
</div>
</div>
</div>
<script type="text/javascript">
var drawshapes = new Array();
<?php 
$x = 0;
foreach ($shapes as $k => $s) {
	echo '// Draw shape '.$k."\n";
	echo 'drawshapes['.$x.'] = ["'.$s[0].'",'.$s[1].',"'.$s[2].'"]'.";\n";
	$x++;
}
?>
</script>