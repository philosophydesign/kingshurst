<?php 
get_header(); 
the_post();
?>
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">

            <div class="carousel-item active">
              <img class="first-slide" src="<?php echo get_template_directory_uri()?>/assets/img/kingshurst_livingroom_1200x600.jpg" alt="First slide"> 
            </div>
            
            <div class="carousel-item">
              <img class="second-slide" src="<?php echo get_template_directory_uri()?>/assets/img/kingshurst_kitchen_1200x600.jpg" alt="Second slide">
            </div>


            <div class="carousel-item">
              <img class="third-slide" src="<?php echo get_template_directory_uri()?>/assets/img/kingshurst_bedroom_1200x600.jpg" alt="Third slide">
            </div>

          </div>
          <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>

        <section class="featured_copy">
          <div class="container-fluid">
            <div class="row">
              <div class="col-xs-12">
                <div class="featured__wrapper">
                  <h2 class="featured_copy text-center"> Marketing Suite & Show Home Open</h2>
                  <div class="copy">
                    <p>Come and view our newly released 4 bedroom family home at our launch event on Saturday 16th & Sunday 17th September 10am - 4pm</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>


			<div id="page-content" >
				<?php //echo the_content(); ?>
				
			</div>
<?php get_footer(); ?> 
			