<?php 
get_header(); 
the_post();
$nv = new NumericVals();

$hpng = get_field('header_png');
$hpng = (isset($hpng['sizes']['large'])) ? $hpng['sizes']['large'] : ''; 

$hsvg = get_field('header_svg');
$hsvg = (isset($hsvg['sizes']['large'])) ? $hsvg['sizes']['large'] : ''; 


$sizes = get_field('room_sizes');
$floorplans = get_field('floorplans');
$price = $nv->get_value($post->ID, 'price');
$price = (isset($price->value)) ? $price->value : '';
if ($availability == 'Reserved') {$price = '';}
else if ($price == '0.00') {$price = 'Price on application';}
else {$price = '&pound;'.number_format($price);}

?><a name="propertydetails"></a>
			<h1 class="single-property-h1"><?php the_title() ?></h1>
			<style>
				.single-property-h1 {
					background-image: url(<?php echo $hpng ?>)
				}
				.svg .single-property-h1 {
					background-image: url(<?php echo $hsvg ?>)
				}
			
			</style>
			
			<hr class="page-spacer spacer-1"/>
			
			<div id="page-content" >
				<?php
				$img = get_field('illustration');
				if (!empty($img)) {
					echo '<img src="'.$img['sizes']['large'].'" style="width: 100%; height: auto; margin-bottom: 20px;">';
				}
					
				?>
			</div>
			
			<div id="property-measurements">
				
				<?php 
					foreach ($sizes as $s) {
						echo '<div class="pm-row row">';
						if (!$s['is_total']) {
							echo '
							<div class="pm-col col-md-5 col-sm-5 col-xs-5">'.$s['room'].'</div>
							<div class="pm-col col-md-4 col-sm-4 col-xs-4">'.$s['metric'].'</div>
							<div class="pm-col col-md-3 col-sm-3 col-xs-3">'.$s['imperial'].'</div>
						';
						} else {
							echo '
								<div class="pm-col col-md-4 col-sm-4 col-xs-4 totalrow">Total</div>
								<div class="pm-col col-md-8 col-sm-8 col-xs-8 totalrow pm-total">'.$s['total'].'</div>';
								
						}
						echo '</div>';				
					
					}
				?>
			</div>
			<p id="property-price">Price: <?php echo $price ?></p>
			<?php if (get_field('help_to_buy')) {
			echo '<p id="h2b_available"><strong>Help to Buy is availale for this property.</strong></p>';
			}?>
			<a name="floorplans"></a>
			<div id="property-floorplans">
				<div class="row">
				<?php 
				if (!empty($floorplans)) {
					foreach ($floorplans as $f) {
						echo '<div class="col-md-12 col-sm-12 col-xs-12 floorplan">
								<img class="leafletimg" width="'.$f['floorplan']['sizes']['large-width'].'"  height="'.$f['floorplan']['sizes']['large-height'].'"src="'.$f['floorplan']['sizes']['large'].'">
							</div>';
					}
				}
				?>
				</div>		
						
				<p id="floorplannote">Development layouts provide approximate measurements only. Dimensions are 
				for guidance only and are not intended to be used to calculate carpet sizes, 
				appliance space, or items of furniture. These plans are not to scale.</p>
			</div>
			
<?php get_footer(); ?> 
			