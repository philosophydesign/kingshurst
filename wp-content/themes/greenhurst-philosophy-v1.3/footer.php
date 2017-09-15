				
			</div>
			
		</div>
		
		
	</div>
</div>
<?php 
global $post;

if (is_single()) {
	$f =  get_stylesheet_directory().'/extra-single-'.$post->post_type.'.php';
} else {
	$f =  get_stylesheet_directory().'/extra-'.$post->post_name.'.php';
}
if (file_exists($f)) {
	include($f);
} else {
	
	$p = get_field("extra_pages");
	$x = 1;
	if (!empty($p)) {
	foreach ($p as $s) {
			?>
		<div class="extrapage <?php echo $s['class'] ?>">
			<div class="container">
				<div class="row">
					<div class="stopatmax  col-md-12 col-sm-12 col-xs-12">
						<h2><?php echo $s['title'] ?></h2>
						<hr class="page-spacer spacer-1"/>
					</div>
					<div class="stopatmax extracontent col-md-12 col-sm-12 col-xs-12">
						<?php echo $s['content'] ?>
					</div>
					<?php if (!empty($s['image'])) {
						?>
					<div class="stopatmax  col-md-12 col-sm-12 col-xs-12 extracontentimage">
						<img src="<?php echo $s['image']['sizes']['large'] ?>"/>
					
						</div>
						<?php } ?>
				</div>
			</div>
		</div>
		<?php 
			$x++;
		}
	}
}
?>
<div class="registerbackground">
	<div class="container">
		<div class="row">
			<div class="stopatmax col-md-12 col-sm-12 col-xs-12">
				<?php 
				$p = reset(get_posts([
					"name"=>"register-footer",
					"post_status"=>"publish",
					"post_type"=>"page",
					"posts_per_page" => 1
				]));
				echo $p->post_content;
				?>
				<?php echo do_shortcode('[register-interest-form form="1" wrap="true"]'); ?>
					<hr class="page-spacer spacer-2"/>
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<h4>DEVELOPED BY</h4>	
						<div class="svgr paulnewman" id="footer-developedby">					
							<img width="248" height="118" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/v3fi/pnnh-logo.png"/>
						</div>				
					</div>
					<div class="col-md-4 col-sm-4 col-xs-6" id="footer-jointsalesagent">
						<h4>JOINT SALES AGENTS</h4>
						<div class="svgr rabennett">					
							<img  width="127" height="37" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/v3fi/ra-bennett-logo-green.png"/>
						</div>
						<div class="svgr hamptons">					
							<img  width="107" height="37" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/v3fi/hamptons-logo.png"/>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-6">
						<div class="svgr htb">
							<img  width="100" height="100" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/v3fi/htb-logo.png"/>
							
							<p style="margin-top: 1.5em; font-size: .85em;">Help To Buy is available, please <a style="color: black;" href="/contact/">contact our sales advisors</a> for further information.</p>					
						</div>
					</div>
				
				</div>
				
				
			</div>
		</div>	
	</div>
</div>
<?php wp_footer(); 

?>

<!--
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
-->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-60205633-16', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
