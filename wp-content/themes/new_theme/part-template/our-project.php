<?php



/**



 Template Name: our-project



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
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php echo $bg_image['url']; ?>">
        <?php } ?>
            
        </section>
        <!-- Project Page -->
        <section class="section-padding2">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title2">Cotton House</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p><?php the_field( 'description-paragraph01' ); ?></p>
                        <p><?php the_field( 'description-paragraph02' ); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><b>Year : </b> <?php the_field( 'year' ); ?></p>
                        <p><b>Company : </b> <?php the_field( 'company' ); ?></p>
                        <p><b>Project Name : </b> <?php the_field( 'project-name' ); ?></p>
                        <p><b>Location : </b> <?php the_field( 'location' ); ?></p>
                    </div>
                </div>
                <div class="row mt-30">
                    <div class="col-md-6 gallery-item">
                    <?php $image_left_top = get_field( 'image-left-top' ); ?>
                    <?php if ( $image_left_top ) { ?>
                        <a href="<?php echo $image_left_top['url']; ?>" title="Architecture" class="img-zoom">
                        <?php } ?>
                            <div class="gallery-box">
                            <?php $image_left_top = get_field( 'image-left-top' ); ?>
                            <?php if ( $image_left_top ) { ?>
                                <div class="gallery-img"> <img src="<?php echo $image_left_top['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                                <?php } ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 gallery-item">
                    <?php $image_right_top = get_field( 'image-right-top' ); ?>
                    <?php if ( $image_right_top ) { ?>
                        <a href="<?php echo $image_right_top['url']; ?>" title="Architecture" class="img-zoom">
                        <?php } ?>
                            <div class="gallery-box">
                            <?php $image_right_top = get_field( 'image-right-top' ); ?>
                            <?php if ( $image_right_top ) { ?>
                                <div class="gallery-img"> <img src="<?php echo $image_right_top['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                                <?php } ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 gallery-item">
                    <?php $image_bottom_left = get_field( 'image-bottom-left' ); ?>
                    <?php if ( $image_bottom_left ) { ?>
                        <a href="<?php echo $image_bottom_left['url']; ?>" title="Architecture" class="img-zoom">
                        <?php } ?>
                            <div class="gallery-box">
                            <?php $image_bottom_left = get_field( 'image-bottom-left' ); ?>
                            <?php if ( $image_bottom_left ) { ?>
                                <div class="gallery-img"> <img src="<?php echo $image_bottom_left['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                                <?php } ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 gallery-item">
                    <?php $image_bottom_right = get_field( 'image-bottom-right' ); ?>
                    <?php if ( $image_bottom_right ) { ?>
                        <a href="<?php echo $image_bottom_right['url']; ?>" title="Architecture" class="img-zoom">
                        <?php } ?>
                            <div class="gallery-box">
                            <?php $image_bottom_right = get_field( 'image-bottom-right' ); ?>
                            <?php if ( $image_bottom_right ) { ?>
                                <div class="gallery-img"> <img src="<?php echo $image_bottom_right['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                                <?php } ?>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Prev-Next Projects -->
        <section class="projects-prev-next">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="projects-prev-next-left">
                                <a href="<?php the_field( 'button-next' ); ?>"> <i class="ti-arrow-left"></i> Previous Project</a>
                            </div> <a href="projects.html"><i class="ti-layout-grid3-alt"></i></a>
                            <div class="projects-prev-next-right"> <a href="<?php the_field( 'button-next' ); ?>">Next Project <i class="ti-arrow-right"></i></a> </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php get_footer();?>