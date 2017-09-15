<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kingshurst | <?php the_title() ?></title>



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

<?php wp_head(); ?>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
 fbq('init', '1551295331845679'); 
fbq('track', 'PageView');
</script>
<noscript>
 <img height="1" width="1" 
src="https://www.facebook.com/tr?id=1551295331845679&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->



</head>
<?php 
global $post;
?>
<body <?php body_class($post->post_name); ?>>
<div id="themenus">
	<div id="full-main-menu">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 hidden-xs">
				<?php
	         	 	wp_nav_menu( array( 'menu' => 'Main (Full)', 'menu_class'=>'' ) );
	          	?>
	          	</div>
	          </div>
	        </div>
	 </div>
	 <?php 
	          	if ($post->post_type == 'properties') {?>
	<div id="sub-main-menu">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 hidden-xs">
					<ul>
						<li><a href="/find-a-home/">&laquo; Back to site plan</a>
						<li><a href="#propertydetails">Property Details</a>
						<li><a href="#floorplans">Floorplans</a>
						<li><a href="#specifications">Specifications</a>
					</ul>
	          	</div>
	           </div>
	          </div>
	         </div>	          		
	          	<?php 
	          	}
	          	?>
	      </div>
	 <div class="bluewhitegradient">
	<div class="container">
		<div class="row" id="page-1">
			<div id="graphics-background" class="col-md-12 col-sm-12 col-xs-12">
				<div id="flower-left-top"></div>
				<div id="flower-right-top"></div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				
				<div id="header" >
					<a href="/" class="site-logo">Greenhurst</a>
					
				</div>