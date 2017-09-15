<?php 
get_header(); 
the_post();
?>

				<div id="contact-us-content" > 
			<div class="row">
					<div class="col-md-12 col-sm-12 col-sm-12">
						<h1>Here we are</h1>
					</div>
					<div class="col-md-5 col-sm-5 col-sm-12">
					<?php echo the_content(); ?>
					<a target="_blank" href="https://www.hamptons.co.uk/"><img id="agent-hamptons" class="agentlogo" src="<?php echo get_template_directory_uri() ?>/assets/img/agentlogo_hamptons.png"/></a>
					<a target="_blank" href="http://www.rabennett.co.uk/"><img id="agent-rabennett" class="agentlogo" src="<?php echo get_template_directory_uri() ?>/assets/img/agentlogo_rabennett.png"/></a>
					
					</div>
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1" id="contactusmap"> 
				
				</div>
				</div>
			
			</div>
<?php get_footer(); ?> 
			