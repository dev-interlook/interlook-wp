 <!-- Footer -->
 <footer class="main-footer dark">
            <div class="container">
                <div class="row">
                <?php if ( have_rows( 'content_footer', 'option' ) ) : ?>
	<?php while ( have_rows( 'content_footer', 'option' ) ) : the_row(); ?>
                    <div class="col-md-4 mb-30">
                        <div class="item fotcont">
                            <div class="fothead">
                                <h6><?php the_sub_field( 'title' ); ?></h6>
                            </div>
                            <p><?php the_sub_field( 'contnent' ); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
<?php else : ?>
	<?php // no rows found ?>
<?php endif; ?>
                    
                </div>
            </div>
            <div class="sub-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-left">
                                <p>© <?php echo date("Y"); ?> <?php the_field( 'copyright', 'option' ); ?>.</p>
                            </div>
                        </div>
                        <div class="col-md-4 abot">
                        <?php if ( have_rows( 'sosial_media', 'option' ) ) : ?>
	                    <?php while ( have_rows( 'sosial_media', 'option' ) ) : the_row(); ?>
                            <div class="social-icon"> 
                                <a href="<?php the_sub_field( 'facebook' ); ?>"><i class="ti-facebook"></i></a>
                                <a href="<?php the_sub_field( 'twitter' ); ?>"><i class="ti-twitter"></i></a> 
                                <a href="<?php the_sub_field( 'instagram' ); ?>"><i class="ti-instagram"></i></a> 
                                <a href="<?php the_sub_field( 'pinterest' ); ?>"><i class="ti-pinterest"></i></a> 
                            </div>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <!--<p class="right"><a href="#">Terms &amp; Conditions</a></p>-->
                        </div>
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