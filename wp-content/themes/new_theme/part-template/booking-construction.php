<?php



/**



 Template Name: Boooking Construction Only



 */





get_header(); ?>
  <!-- Content -->
  <?php if ( have_rows( 'banner' ) ) : ?>
	<?php while ( have_rows( 'banner' ) ) : the_row(); ?>
  <div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- Header Banner -->
    <section class="banner-header banner-img valign bg-img bg-fixed"
      style="background-position: center;"
      data-overlay-darkgray="5"
      data-background="<?php the_sub_field( 'background' ); ?>"></section>
      <!-- Blog  -->
    <section class="bauen-blog3 section-padding2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<br>
						<h2 class="section-title" style="text-wrap: pretty;"><?php the_sub_field( 'title_page' ); ?></h2>
          </div>
				</div>
				<div class="row">
					<div class="col-md-12">
            <br><br><br>
            <?php echo do_shortcode('[contact-form-7 id="1520" title="Construction Only"]'); ?>
				  </div>
        </div>
      </section>
  <?php endwhile; ?>
  <?php endif; ?>

  <script>
    function redirectToEndpoint() {
        window.location.href = '/booking-success';
    }

    const responseElement = document.getElementsByClassName('wpcf7-response-output')[0];

    const observer = new MutationObserver(function(mutationsList) {
      if (responseElement.innerHTML == 'Thank you for your message. It has been sent.')
      {
        redirectToEndpoint();
      }
    });

    const observerConfig = { attributes: true, childList: true, subtree: true };

    if (responseElement) {
        observer.observe(responseElement, observerConfig);
    } else {
        console.error('Element not found.');
    }
  </script>

<?php get_footer();?>