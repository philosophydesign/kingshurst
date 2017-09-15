<?php 
get_header(); 
the_post();
?>

			
			<div id="page-content" >
				<?php echo the_content(); ?>
				<?php 
				if ($post->post_name == 'home') {
				?>
				<p class="centerit">
					<a href="/register/" class="c2a">Register your interest</a>
				</p>
				<div id="peacock-full"></div>
				<?php } ?>
			</div>
<?php get_footer(); ?> 
			