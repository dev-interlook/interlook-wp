<?php



/**



 Template Name: about



 */

get_header();

?>

<style>
    b {
        font-weight: bolder;
    }

    .section-padding2 {
        min-height: 90vh;
    }

    .section-title2 {
        color: #000;
        font-weight: 600;
    }

    .tagline-header {
        height: 50vh;
        margin-top: 90px;
    }
    .tagline-header > h1 {
        font-size: 1.5rem;
        text-align: center;
        margin: 0;
        position: relative;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);

        /* invert the color if it is in the same color */
        color: black;
        filter: invert(1);
        mix-blend-mode: difference;
    }
    @media screen and (min-width: 1280px) {
        .tagline-header > h1 {
            font-size: 2.25rem;
            line-height: 2.5rem;
        }
    }

    .banner-header {
        width: 100%;
        height: auto;
        position: relative;
    }
    @media screen and (max-width: 480px) {
        .banner-header {
            margin-top: 40px;
        }
        .banner-header h1 {
            font-size: 25px;
        }
    }

    .section-1 {
        width: 80%;
        margin: auto;
    }

    .image-divider {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;

        padding-left: 15px
    }
    .image-divider > img {
        width: 33.333333%;
        list-style: none;
        flex: 0 0 33.333333%;
        object-fit: cover;

        padding-right: 15px;
    }
    @media screen and (max-width: 768px) {
        .image-divider {
            flex-direction: column;
        }
        .image-divider > img {
            width: 100%;
            flex: 0 0 0;
            /* height: 100px; */
            object-fit: cover;

            padding: 0px 15px 15px 0px;
        }
    }

    .section-2 {
        width: 80%;
        margin: auto;
    }

    .our-team {
        width: 100%;
        padding: 50px 0px 50px 0px;
        background-color: #f0f0f0;
    }
    .our-team > div {
        width: 80%;
        margin: auto;
    }
    .our-team h5 {
        margin: 0;
    }
    .our-team .team-image {
        width: 80%;
    }
    .our-team .ceo-position {
        margin: 0;
    }
    .our-team .team-name {
        margin: 15px 0;
    }
    .our-team .team-summary {
        /* position: absolute;
        bottom: 0; */
        font-size: 14px;
        margin: 0;
        padding: 0;
    }
    
    .our-team .team-leaders {
        margin: 50px 0;
    }
    .team-leaders .team-position {
        margin: 15px 0;
    }
    .team-leaders .team-name {
        margin: 5px 0;
    }
    @media screen and (max-width: 480px) {
        .our-team .ceo-position {
            margin: 15px 0 0;
        }
        .our-team .team-name {
            margin: 10px 0;
        }
        .our-team .team-summary {
            position: unset;
        }

        .team-leaders .team-name {
            margin: 0;
        }
    }

    /* Style for Team Slider */
    .mySlides {display: none;}

    /* Slideshow container */
    .slideshow-container {
        position: relative;
        margin: auto;
    }

    .active {
        background-color: #717171;
    }

    /* Fading animation */
    .fade {
        animation-name: fade;
        animation-duration: 4.5s;
    }

    @keyframes fade {
        from {opacity: .4}
        to {opacity: 1}
    }
    
    /* @media screen and (max-width: 480px) {
        .team-web-view {
            display: none;
        }
        .team-mobile-view {
            display: block;
        }
    }
    @media screen and (min-width: 481px) {
        .team-web-view {
            display: block;
        }
        .team-mobile-view {
            display: none;
        }
    } */
</style>

<!-- Content -->
<div class="content-wrapper">
    <div class="tagline-header">
        <h1><?php the_field( 'tag_line' ); ?></h1>
    </div>

    <!-- Banner Header -->
    <div class="banner-header">
        <?php if ( get_field( 'banner' ) ) { ?>
            <img src="<?php the_field( 'banner' ); ?>" />
        <?php } ?>
    </div>

    <!-- Section 1 -->
    <div class="section-1 mt-100 mb-100 row">
        <h3 class="col-md-6">
            <?php the_field( 'section_1_title' ); ?>
        </h3>

        <div class="col-md-6">
            <h6><?php the_field( 'section_1_description' ); ?></h6>
        </div>
    </div>
    
    <!-- Image Divider -->
    <div class="image-divider">
        <?php if ( have_rows( 'image_separator' ) ) : ?>
            <?php while ( have_rows( 'image_separator' ) ) : the_row(); ?>
                <?php if ( get_sub_field( 'image_1' ) ) { ?>
                    <img src="<?php the_sub_field( 'image_1' ); ?>" />
                <?php } ?>
                <?php if ( get_sub_field( 'image_2' ) ) { ?>
                    <img src="<?php the_sub_field( 'image_2' ); ?>" />
                <?php } ?>
                <?php if ( get_sub_field( 'image_3' ) ) { ?>
                    <img src="<?php the_sub_field( 'image_3' ); ?>" />
                <?php } ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Section 2 -->
    <div class="section-2 mt-100 mb-100 row">
        <h3 class="col-md-6">
            <?php the_field( 'section_2_title' ); ?>
        </h3>

        <div class="col-md-6">
            <h6><?php the_field( 'section_2_description' ); ?></h6>
        </div>
    </div>

    <!-- Web View -->
    <div class="team-web-view">
        <!-- Our Team -->
        <div class="our-team">
            <div class="row" style="justify-content: center;">
                <h2 class="col-sm-12 mb-50">meet the team</h2>

                <!-- CEO -->
                <?php if ( have_rows( 'ceo' ) ) : ?>
                    <?php while ( have_rows( 'ceo' ) ) : the_row(); ?>
                        <div class="col-md-3 col-sm-12">
                            <div class="team-image">
                                <?php if ( get_sub_field( 'photo' ) ) { ?>
                                    <img src="<?php the_sub_field( 'photo' ); ?>" />
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <h5 class="ceo-position"><?php the_sub_field( 'position' ); ?></h5>
                            <p class="team-name"><b><?php the_sub_field( 'name' ); ?></b></p>
                            <p class="team-summary"><?php the_sub_field( 'summary' ); ?></p>
                        </div>
                        <div class="col-md-3 col-sm-12"></div>
                        <div class="col-md-3 col-sm-12"></div>
                    <?php endwhile; ?>
                <?php endif; ?>

                <!-- Team Leaders -->
                <div class="row team-leaders">
                    <?php if ( have_rows( 'team_leaders' ) ) : ?>
                        <?php while ( have_rows( 'team_leaders' ) ) : the_row(); ?>
                            <div class="col-md-3 col-sm-12 mt-20">
                                <div class="team-image">
                                    <?php if ( get_sub_field( 'photo' ) ) { ?>
                                        <img src="<?php the_sub_field( 'photo' ); ?>" />
                                    <?php } ?>
                                    <p class="team-position"><?php the_sub_field( 'job_position' ); ?></p>
                                    <p class="team-name"><?php the_sub_field( 'name' ); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php // no rows found ?>
                    <?php endif; ?>
                </div>

                <!-- Team Divisions -->
                <!-- <?php if ( have_rows( 'team_divisions' ) ) : ?>
                    <?php while ( have_rows( 'team_divisions' ) ) : the_row(); ?>
                        <div class="col-md-3 col-sm-12 mt-20" style="flex: 0 0 fit-content;">
                            <p class="team-position"><b><?php the_sub_field( 'division' ); ?></b></p>

                            <div class="mt-10">
                                <?php if ( have_rows( 'teams' ) ) : ?>
                                    <?php while ( have_rows( 'teams' ) ) : the_row(); ?>
                                        <p style="margin-bottom: 0;"><?php the_sub_field( 'name' ); ?></p>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <?php // no rows found ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <?php // no rows found ?>
                <?php endif; ?> -->
            </div>
        </div>
    </div>

    <!-- Mobile View -->
    <!-- <div class="team-mobile-view">
        <div class="our-team mt-50">
            <div class="row">
                <h2 class="col-sm-12">meet the team</h2>

                <div class="slideshow-container">
                    <?php if ( have_rows( 'teams' ) ) : ?>
                        <?php while ( have_rows( 'teams' ) ) : the_row(); ?>
                        <div class="mySlides fade">
                            <div class="team-image">
                                <?php if ( get_sub_field( 'photo' ) ) { ?>
                                    <img src="<?php the_sub_field( 'photo' ); ?>" />
                                <?php } ?>
                            </div>
                            <div class="team-name">
                                <h5><?php the_sub_field( 'name' ); ?></h5>
                                <small><?php the_sub_field( 'job_position' ); ?></small>
                            </div>
                            <div class="team-description">
                                <h6><?php the_sub_field( 'summary' ); ?></h6>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php // no rows found ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> -->


<script>
    let slideIndex = 0;
    // showSlides();

    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        slides[slideIndex-1].style.display = "block";
        setTimeout(showSlides, 5000);
    }
</script>
<?php get_footer();?>