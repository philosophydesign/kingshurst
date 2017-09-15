<?php 
get_header(); 
the_post();
global $post;
?>
			<h1><?php the_title() ?></h1>
			<hr class="page-spacer spacer-1"/>
			<?php 
			?>			
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div id="contactusmap"></div>
			
			</div>
			<div class="col-md-6 col-sm-5 col-sm-12" id="contact-details">
			<?php echo the_content(); ?>
			</div>
<?php get_footer(); ?> 
			
					
