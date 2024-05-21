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
        window.location.href = '/booking-success?cp=worksoul';
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

<!-- Modify the Header & Footer -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("header-logo").src = "<?php echo get_template_directory_uri(); ?>/img/worksoul-logo-black.png";
    document.getElementById("footer-logo").src = "<?php echo get_template_directory_uri(); ?>/img/worksoul-logo-black.png";
    document.getElementById("footer-Phone").innerText = "+62 812 2125 4040";
    document.getElementById("footer-Email").innerText = "worksoul.co.id@gmail.com";
    document.getElementById("footer-Our Address").innerText = "Office\nJalan Cijerokaso, Cluster Green Residence Unit E, Sarijadi - Bandung\n\nWorkshop\nJalan Haji Gofur RT 05 RW 18, Desa Cilame, Kabupaten Bandung Barat";
  });
</script>