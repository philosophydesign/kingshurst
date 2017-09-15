<?php 
get_header(); 
the_post();
$destinations = get_field('destinations');
?>
			<h1><?php the_title() ?></h1>
			<hr class="page-spacer spacer-1"/>
			<div class="row">
				<div class="col-md-3">
					<?php the_content() ?>
				</div>
				<div class="col-md-9">
					<div class="row">
						<?php 
						$css = array();
						$i = 1;
						foreach ($destinations as $d) {
							echo '<div class="col-md-4 col-sm-6 col-xs-12 destination" id="destination-'.$i.'">
							<div class="dest-img" style="background-image: url('.$d['image']['sizes']['destination'].');"></div>
							<h4>'.$d['destination'].'</h4>
							<p class="dest-times">
								'.$d['timings'].'
							</p>
							</div>';	
							$css['m'] .= '#destination-'.$i.' > div.dest-img {background-image: url('.$d['image']['sizes']['gallery-mobile'].') !important;} ';
							$css['t'] .= '#destination-'.$i.' > div.dest-img {background-image: url('.$d['image']['sizes']['gallery-tablet'].') !important;} ';
							$i++;
						}
						?>
						<style>
						@media screen and (max-width: 767px) {
							<?php 
							echo $css['m']
							?>
						}
						@media screen and (min-width: 768px) and (max-width: 991px) {
							<?php 
							echo $css['t']
							?>
						}
						</style>
					</div>
				</div>
			</div>
			<?php 
			
			foreach ($destinations as $d) {
				
				
			}
			
			?>
<?php get_footer(); ?> 
			