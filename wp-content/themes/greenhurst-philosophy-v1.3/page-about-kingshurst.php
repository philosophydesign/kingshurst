<?php 
get_header(); 
the_post();
global $post;
$img = wp_get_attachment_image_src(get_post_thumbnail_id(),'large')[0];
?>
			<h1><?php the_title() ?></h1>
			<hr class="page-spacer spacer-1"/>
			<img class="stretchimg" src="<?php 
			echo $img
			?>"/>
			<div id="page-content" >
				<?php echo the_content();		
				?>
				
			</div>
<?php get_footer(); ?> 
			