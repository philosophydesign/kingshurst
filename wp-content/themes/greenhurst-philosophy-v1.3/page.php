<?php 
get_header(); 
the_post();
global $post;
?>
			<h1><?php the_title() ?></h1>
			<hr class="page-spacer spacer-1"/>
			<?php 
			?>			
			<div id="page-content" >
				<?php echo the_content();		
				?>
				
			</div>
<?php get_footer(); ?> 
			