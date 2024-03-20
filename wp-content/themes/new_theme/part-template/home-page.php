<?php



/**



 Template Name: home-page



 */



?>

<?php get_header();?>

<!-- Preloader -->
<?php if (!isset($_GET['sec'])) : ?>
<div id="bumper">
    <video playsinline autoplay muted>
        <source src="<?php echo get_template_directory_uri(); ?>/img/bumper_putih.mp4" type="video/mp4">
    </video>
</div>
<?php endif; ?>

<!-- Slider -->
<header id="home" class="header slider-fade" data-scroll-index="0">
    <div class="owl-carousel owl-theme">
        <!-- The opacity on the image is made with "data-overlay-dark="number". You can change it using the numbers 0-9. -->
        <?php if ( have_rows( 'slider' ) ) : ?>
	        <?php while ( have_rows( 'slider' ) ) : the_row(); ?>
                <div class="text-left item bg-img" data-overlay-dark="3" data-background="<?php the_sub_field( 'background' ); ?>">
                    <div class="v-middle caption mt-30">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <!-- <h1><?php the_sub_field( 'title' ); ?></h1> -->
                                    <img class="monogram-logo-banner" src="<?php echo get_template_directory_uri(); ?>/img/interlook-logo.png" alt="Interlook Monogram Logo">
                                    <p><?php the_sub_field( 'description' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <?php // no rows found ?>
        <?php endif; ?>
    </div>
</header>
<!-- Content -->
<div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper" id="about">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- About -->
    <?php if ( have_rows( 'section_about' ) ) : ?>
        <?php while ( have_rows( 'section_about' ) ) : the_row(); ?>
            <section id="about" class="about section-padding" data-scroll-index="1">
                <div style="margin: 0px 10% 0px 10%;">
                    <div class="card" style="background-color: #f4f4f4; border: none;">
                        <div class="row animate-box card-body" data-animate-effect="fadeInUp">
                            <div class="col-md-4 col-sm-12 px-5 py-2">
                                <h2 class="section-title"><?php the_sub_field( 'title' ); ?></h2>
                            </div>
                            <div class="col-md-8 col-sm-12 px-5 py-2" style="text-align: left; margin-left: -5px;">
                                <?php the_sub_field( 'description' ); ?>
                            </div>
                        </div>
                        <!-- <div class="col-md-6 animate-box" data-animate-effect="fadeInUp">
                            <div class="about-img">
                
                                <div class="img"> <img src="<?php the_sub_field( 'image_about' ); ?>" class="img-fluid" alt=""> </div>
                                <div class="about-img-2 about-buro"><?php the_sub_field( 'name_image' ); ?></div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <!-- Projects -->
    <?php if ( have_rows( 'section_projects' ) ) : ?>
        <?php while ( have_rows( 'section_projects' ) ) : the_row(); ?>
            <section id="projects" class="projects" data-scroll-index="2">
                <div style="margin: 0px 10% 5% 10%;">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="section-sub-title"><?php the_sub_field( 'title' ); ?></h5>

                            <div class="owl-carousel owl-theme">
                            <?php if ( have_rows( 'projects' ) ) : ?>
                                <?php while ( have_rows( 'projects' ) ) : the_row(); ?>
                                <div class="item">
                                    <div class="position-re o-hidden"> <img src="<?php the_sub_field( 'image' ); ?>" alt=""> </div>
                                        <div class="con">
                                            <h6><?php the_sub_field( 'kategori' ); ?></h6>
                                            <h5><?php the_sub_field( 'title' ); ?></h5>
                                            <div class="line"></div> <a href="<?php the_sub_field( 'url' ); ?>"><i class="ti-arrow-right"></i></a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <?php // no rows found ?>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
<!-- 
    <?php if ( have_rows( 'section_services' ) ) : ?>
        <?php while ( have_rows( 'section_services' ) ) : the_row(); ?>

        <section id="services" class="services section-padding" data-scroll-index="3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title"><?php the_sub_field( 'title' ); ?></span></h2>
                    </div>
                </div>
                <div class="row">
                <?php if ( have_rows( 'service' ) ) : ?>
			        <?php $no=1; while ( have_rows( 'service' ) ) : the_row(); ?>
                        <div class="col-md-4">
                            <div class="item">
                                <a href="<?php the_sub_field( 'link' ); ?>"> <img src="<?php the_sub_field( 'icon' ); ?>" alt="">
                                    <h5><?php the_sub_field( 'title' ); ?></h5>
                                    <div class="line"></div>
                                    <p><?php the_sub_field( 'description' ); ?></p>
                                    <div class="numb"><?php if($no>9){
                                        echo $no;
                                    }else{
                                        echo "0".$no;
                                    } ?></div>
                                </a>
                            </div>
                        </div>
                    <?php $no++; endwhile; ?>
                <?php else : ?>
                    <?php // no rows found ?>
                <?php endif; ?>

                </div>
            </div>
        </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <section id="blog" class="bauen-blog section-padding" data-scroll-index="4">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                <?php if ( have_rows( 'section_news' ) ) : ?>
	                <?php while ( have_rows( 'section_news' ) ) : the_row(); ?>
                        <h2 class="section-title"><?php the_sub_field( 'title' ); ?></h2>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="owl-carousel owl-theme">
                    <?php $latest = new WP_Query(array('cat' => 3 ));?>
                    <?php if(have_posts()) :?>  <?php while($latest->have_posts()) : $latest->the_post();?>  
                        <div class="item">
                            <div class="position-re o-hidden"> <img src="<?php the_field( 'image' ); ?>" alt=""> </div>
                            <div class="con"> 
                                <span class="category">
                                    <a href="#">Architecture </a> |  <?php $post_date = get_the_date( 'D , j M  Y' ); echo $post_date; ?>
                                </span>
                                <h5><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h5>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>
    <?php if ( have_posts() ) : while( have_posts()  ) : the_post(); ?>
        <?php if ( have_rows( 'section_contact_us' ) ) : ?>
	        <?php while ( have_rows( 'section_contact_us' ) ) : the_row(); ?>
                <section id="contact" class="section-padding" data-scroll-index="5">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 animate-box" data-animate-effect="fadeInUp">
                                <h2 class="section-title"><?php the_sub_field( 'title' ); ?></h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-30 animate-box" data-animate-effect="fadeInUp">
                                    <?php the_sub_field( 'description' ); ?>
                            </div>
                            <div class="col-md-4 mb-30 animate-box" data-animate-effect="fadeInUp">
                                <p><b>Phone :</b> <?php the_sub_field( 'phone' ); ?></p>
                                <p><b>Email :</b> <?php the_sub_field( 'email' ); ?></p>
                                <p><b>Address :</b> <?php the_sub_field( 'address' ); ?></p>
                            </div>
                            <div class="col-md-4 animate-box" data-animate-effect="fadeInUp">    	
                            <?php echo do_shortcode('[contact-form-7 id="534" title="Contact"]'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endwhile; ?>
        <?php endif; ?>
    <?php endwhile; endif; ?>
     -->
<?php if ( have_rows( 'section_promo' ) ) : ?>
    <?php while ( have_rows( 'section_promo' ) ) : the_row(); ?>

        <section class="testimonials">
          <div class="background bg-img bg-fixed section-padding pb-0" data-background="<?php the_sub_field( 'background' ); ?>" data-overlay-dark="3">
                <div class="container">
                    <div class="row" style="height: 50vh;">
                        <!--
                        <div class="col-md-6">
                            <div class="vid-area">
                                <div class="vid-icon">
                                    <a class="play-button vid" href="<?php the_sub_field( 'link_video' ); ?>">
                                        <svg class="circle-fill">
                                            <circle cx="43" cy="43" r="39" stroke="#fff" stroke-width=".5"></circle>
                                        </svg>
                                        <svg class="circle-track">
                                            <circle cx="43" cy="43" r="39" stroke="none" stroke-width="1" fill="none"></circle>
                                        </svg> <span class="polygon">
                                            <i class="ti-control-play"></i>
                                        </span> </a>
                                </div>
                                <div class="cont mt-15 mb-30">
                                    <h5><?php the_sub_field( 'title_promo' ); ?></h5>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ( have_rows( 'review' ) ) : ?>
                        <?php while ( have_rows( 'review' ) ) : the_row(); ?>
                        <div class="col-md-5 offset-md-1">
                            <div class="testimonials-box animate-box" data-animate-effect="fadeInUp">
                                <div class="head-box">
                                    <h4><?php the_sub_field( 'title' ); ?></h4>
                                </div>
                                <div class="owl-carousel owl-theme">
                                <?php if ( have_rows( 'say' ) ) : ?>
                                    <?php while ( have_rows( 'say' ) ) : the_row(); ?>
                                    <div class="item"> <span class="quote">
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/quot.png" alt="">
                                    </span>
                                        <p><?php the_sub_field( 'comment' ); ?></p>
                                        <div class="info">
                                            <div class="author-img"> <img src="<?php the_sub_field( 'foto' ); ?>" alt=""> </div>
                                            <div class="cont">
                                                <h6><?php the_sub_field( 'name' ); ?></h6> <span><?php the_sub_field( 'job' ); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <?php // no rows found ?>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                        -->
                    </div>
                </div>
            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>
       <!-- <section class="clients">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 owl-carousel owl-theme">
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/1.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/2.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/3.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/4.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/5.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="<?php echo get_template_directory_uri(); ?>img/clients/6.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>  -->
        <?php get_footer();?>