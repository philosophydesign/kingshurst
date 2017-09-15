<?php 
get_header(); 
the_post();


?>
			<h1><?php the_title() ?></h1>
			<hr class="page-spacer spacer-1"/>
			<?php 
			$gallery = get_field("gallery");
			$css = array();
			$gi = 1;
			foreach ($gallery as $g) {
				if (!empty($g['description'])) {
					$g['description'] = '<p class="gallery-image-desc">'.$g['description'].'</p>';
				}
				echo '<div id="gallery-image-'.$gi.'" class="gallery-image '.$use.' '.$g['size'].' col-sm-6 col-xs-12">
					<div style="background-image: url('.$g['image']['sizes']['gallery-'.$g['size']].')">
						'.$g['description'].' 
						</div>';
				//echo '<pre style="overflow: scroll; width: 100%; height: 300px; background-color: transparent; color: red; text-shadow: 0 0 3px #FFF;">'.print_r($g['image'],1).'</pre>';
				echo '</div>';
// 				break;
				$css['m'] .= '#gallery-image-'.$gi.' > div {background-image: url('.$g['image']['sizes']['gallery-mobile'].') !important;} ';
				$css['t'] .= '#gallery-image-'.$gi.' > div {background-image: url('.$g['image']['sizes']['gallery-tablet'].') !important;} ';
				$gi++;
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
<?php get_footer(); ?> 
			