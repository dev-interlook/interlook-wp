<?php



/**



 Template Name: architecture



 */



?>

<?php get_header();?>
  <!-- Content -->
  <div class="content-wrapper">
        <!-- Lines -->
        <section class="content-lines-wrapper">
            <div class="content-lines-inner">
                <div class="content-lines"></div>
            </div>
        </section>
        <!-- Header Banner -->
        <?php $bg_image = get_field( 'bg-image' ); ?>
        <?php if ( $bg_image ) { ?>
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php echo $bg_image['url']; ?>"></section>
        <?php } ?>
        <!-- Services Page -->
        <section class="section-padding2">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title2">Architecture</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p><?php the_field( 'description' ); ?></p>
                        <!-- <p>Architecture non lorem ac erat suscipit bibendum. Nulla facilisi sedeuter nunc volutpat molli sapien veconseyer turpeutionyer masin libero sempe. Fusceler mollis augue sit amet hendrerit vestibulum. Duisteyerionyer venenatis lacus.</p> -->
                        <!--<p><?php the_field( 'description-paragraph02' ); ?></p>-->
                        <!-- <p>Villa gravida eros ut turpis interdum ornare. Interdum et malesu they adamale fames ac anteipsun pimsinefaucibus urabitur arcu site feugiat in volutpat.</p> -->
                        <div class="row mb-30">
                            <div class="col-md-6 gallery-item">
                            <?php $image_left = get_field( 'image-left' ); ?>
                            <?php if ( $image_left ) { ?>
                                <a href="<?php echo $image_left['url']; ?>" title="Architecture" class="img-zoom">
                                <?php } ?>
                                    <div class="gallery-box">
                                    <?php $image_left = get_field( 'image-left' ); ?>
                                    <?php if ( $image_left ) { ?>
                                        <div class="gallery-img"> <img src="<?php echo $image_left['url']; ?>" class="img-fluid mx-auto d-block" alt="Architecture"> </div>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 gallery-item">
                            <?php $image_right = get_field( 'image-right' ); ?>
                            <?php if ( $image_right ) { ?>
                                <a href="<?php echo $image_right['url']; ?>" title="Architecture" class="img-zoom">
                                <?php } ?>
                                    <div class="gallery-box">
                                    <?php $image_right = get_field( 'image-right' ); ?>
                                    <?php if ( $image_right ) { ?>
                                        <div class="gallery-img"> <img src="<?php echo $image_right['url']; ?>" class="img-fluid mx-auto d-block" alt="Architecture"> </div>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-12 gallery-item">
                            <?php $image_center = get_field( 'image-center' ); ?>
                            <?php if ( $image_center ) { ?>
                                <a href="<?php echo $image_center['url']; ?>" title="Architecture" class="img-zoom">
                                <?php } ?>
                                    <div class="gallery-box">
                                    <?php $image_center = get_field( 'image-center' ); ?>
                                    <?php if ( $image_center ) { ?>
                                        <div class="gallery-img"> <img src="<?php echo $image_center['url']; ?>" class="img-fluid mx-auto d-block" alt="Architecture"> </div>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 sidebar-side">
                        <aside class="sidebar blog-sidebar">
                            <div class="sidebar-widget services">
                                <div class="widget-inner">
                                    <div class="sidebar-title">
                                        <h4>All Services</h4>
                                    </div>
                                    <ul>
                                        <li class="active"><a href="#">Achitecture</a></li>
                                        <li><a href="<?php the_field( 'button-interior-design' ); ?>">Interior Design</a></li>
                                        <li><a href="<?php the_field( 'button-urban-design' ); ?>">Urban Design</a></li>
                                        <li><a href="<?php the_field( 'button-planning' ); ?>">Planning</a></li>
                                        <li><a href="<?php the_field( 'button-3d-modelling' ); ?>">3D Modelling</a></li>
                                        <li><a href="<?php the_field( 'button-decor-plan' ); ?>">Decor Plan</a></li>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>

            </div>
        </section>
        <?php get_footer();?>