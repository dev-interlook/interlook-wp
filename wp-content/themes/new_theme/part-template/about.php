<?php



/**



 Template Name: about



 */

get_header();

?>

<style>
    .navbar {
        background: #000;
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
        width: 40%;
        float: left;
    }
    .our-team .team-name {
        width: 50%;
        float: left;
        margin-left: 10%;
    }
    .our-team .team-description {
        width: 100%;
        float: left;
        margin: 15px 0px 15px 0px;
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
    
    @media screen and (max-width: 480px) {
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
    }
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
    <div class="section-1 mt-50 row">
        <h2 class="col-md-6">
            <?php the_field( 'section_1_title' ); ?>
        </h2>

        <div class="col-md-6">
            <h6><?php the_field( 'section_1_description' ); ?></h6>
        </div>
    </div>
    
    <!-- Image Divider -->
    <div class="image-divider mt-50">
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
    <div class="section-2 mt-50 row">
        <h2 class="col-md-6">
            <?php the_field( 'section_2_title' ); ?>
        </h2>

        <div class="col-md-6">
            <h6><?php the_field( 'section_2_description' ); ?></h6>
        </div>
    </div>

    <!-- Web View -->
    <div class="team-web-view">
        <!-- Our Team -->
        <div class="our-team mt-50">
            <div class="row">
                <h2 class="col-sm-12">Our Team</h2>

                <?php if ( have_rows( 'teams' ) ) : ?>
                    <?php while ( have_rows( 'teams' ) ) : the_row(); ?>
                        <div class="col-md-4">
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

    <!-- Mobile View -->
    <div class="team-mobile-view">
        <!-- Our Team -->
        <div class="our-team mt-50">
            <div class="row">
                <h2 class="col-sm-12">Our Team</h2>

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
    </div>


<script>
    let slideIndex = 0;
    showSlides();

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