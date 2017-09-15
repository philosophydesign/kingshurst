<?php 
get_header(); 
the_post();
?>
<div class="container">
	<div class="row" id="page-1">
		<div id="graphics-background" class="col-md-12 col-sm-12 col-xs-12">
			<div id="flower-left-top"></div>
			<div id="flower-left-bot"></div>
			<div id="flower-right-top"></div>
			<div id="flower-right-bot"></div>
			<div id="peacock-left"></div>
			<div id="peacock-right"></div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div id="header" >
				<p class="site-logo">Greenhurst</p>
				<p>Register</p>
			</div>
			<h1>Your gateway to the Cotswolds</h1>
			<div id="intro" >
				<?php echo the_content(); ?>
			</div>
			<a id="register-anchor" href="#register">Register</a>
			
		</div>
	</div>
	<div class="row" id="page-2">
		<div id="register-interest" class="col-md-12 col-sm-12 col-xs-12">
			<a name="register"></a>
			<p class="site-logo">Greenhurst</p>
			<div id="page-2-content">
				<p>Register with us to receive the latest news and updates. Or call us on XXXX XXX XXX</p>
				<?php echo do_shortcode('[register-interest-form form="1"  wrap="true"]'); ?>
			</div>
		</div>
		<div id="wallpaper" class="col-md-12 col-sm-12 col-xs-12">
		</div>
	</div>
</div>
<?php get_footer(); ?> 