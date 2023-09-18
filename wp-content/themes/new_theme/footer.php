<!-- Footer -->
<footer class="main-footer dark" style="background-color: #000;">
    <div style="margin: 0px 0px 0px 10%;">
        <div class="row">
            <div class="col-md-4 mb-10">
                <img style="width: 20%;" src="<?php echo get_template_directory_uri(); ?>/img/interlook-monogram.png" alt="Interlook Monogram Logo">
            </div>

            <div class="col-md-4 mb-10">
                <?php if ( have_rows( 'content_footer', 'option' ) ) : ?>
                    <div class="row">
	                <?php while ( have_rows( 'content_footer', 'option' ) ) : the_row(); ?>
                        <div class="item fotcont col-md-6 mb-20">
                            <div class="fothead">
                                <h6 style="color: white; text-transform: uppercase; font-weight: 700;">
                                    <?php the_sub_field( 'title' ); ?>
                                </h6>
                            </div>
                            <p class="p-light" style="color: white;"><?php the_sub_field( 'contnent' ); ?></p>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <?php // no rows found ?>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4 mb-10 abot fotcont">
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
        </div>
    </div>
    <div class="sub-footer">
        <div style="margin: 0px 10% 0px 10%;">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-left">
                        <p>Copyright © <?php echo date("Y"); ?>. Interlook. <?php the_field( 'copyright', 'option' ); ?>.</p>
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
<?php wp_footer();?>
</body>
</html>