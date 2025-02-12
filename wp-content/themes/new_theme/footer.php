<!-- Footer -->
<footer class="main-footer dark">
    <div style="margin: 0px 0px 0px 4%;">
        <div class="row">
            <div class="col-md-4 mb-20">
                <div class="col-md-12" style="padding: 0;">
                    <img id="footer-logo" class="monogram-logo-width" src="<?php echo get_template_directory_uri(); ?>/img/interlook-logo-black.png" alt="Interlook Monogram Logo">
                </div>
                <div class="col-md-12 display-only-desktop" style="position: absolute; bottom: 0; left: 0;">
                    <div class="text-left">
                        <p style="color: black; font-size: 15px;">
                            Copyright © <?php echo date("Y"); ?>. Interlook. <?php the_field( 'copyright', 'option' ); ?>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-10">
                <?php if ( have_rows( 'content_footer', 'option' ) ) : ?>
                    <div class="row">
	                <?php while ( have_rows( 'content_footer', 'option' ) ) : the_row(); ?>
                        <?php if (get_row_index() >= 3) : ?>
                        <div class="item fotcont col-md-6 mb-30 mt-50">
                        <?php else : ?>
                        <div class="item fotcont col-md-6 mb-20">
                        <?php endif; ?>
                            <div class="fothead">
                                <h6 style="color: black; text-transform: uppercase; font-weight: 700;">
                                    <?php the_sub_field( 'title' ); ?>
                                </h6>
                            </div>
                            <p id="footer-<?php the_sub_field( 'title' ); ?>" class="p-light" style="color: black;"><?php the_sub_field( 'contnent' ); ?></p>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <?php // no rows found ?>
                <?php endif; ?>
            </div>
            
            <div class="col-md-3 mb-10 abot fotcont">
                <?php if ( have_rows( 'sosial_media', 'option' ) ) : ?>
                    <?php while ( have_rows( 'sosial_media', 'option' ) ) : the_row(); ?>
                    <div class="fothead text-right" style="margin-right: 52px;">
                        <h6>Follow Us On</h6>
                    </div>
                    <div class="social-icon text-right" style="margin-right: 30px;">
                        <a href="<?php the_sub_field( 'instagram' ); ?>"><i class="fab fa-instagram"></i></a>
                        <a href="<?php the_sub_field( 'youtube' ); ?>"><i class="fab fa-youtube"></i></a>
                        <a href="<?php the_sub_field( 'tiktok' ); ?>"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="col-md-12 mb-50"></div>
        </div>
    </div>
    <div class="sub-footer display-only-phone">
        <div style="margin: 0px 10% 0px 10%;">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-left">
                        <p style="color: black;">
                            Copyright © <?php echo date("Y"); ?>. Interlook. <?php the_field( 'copyright', 'option' ); ?>.
                        </p>
                    </div>
                </div>
                <!-- <div class="col-md-4 abot">
                </div>
                <div class="col-md-4">
                    <p class="right"><a href="#">Terms &amp; Conditions</a></p>
                </div> -->
            </div>
        </div>
    </div>
</footer>
</div>
<!-- jQuery -->
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-3.5.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-migrate-3.0.0.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/modernizr-2.6.2.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/imagesloaded.pkgd.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.isotope.v3.0.2.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/pace.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/popper.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/scrollIt.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.waypoints.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/s/owl.carousel.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.stellar.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.magnific-popup.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/YouTubePopUp.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/custom.js"></script>

<script>
    $(document).click(function(e) {
        if (!$(e.target).is('#navbarSupportedContent')) {
            // $('#navbarSupportedContent').collapse('hide');
        }
    });
    $('textarea').attr('rows', 3)

    // Scroll to top if next button from CF7 plugin clicked
    $('.cf7mls_next').click(function(e) {
        document.querySelector('.fieldset-cf7mls').scrollIntoView({
            behavior: 'smooth'
        })
    });
</script>

<?php wp_footer();?>
</body>
</html>