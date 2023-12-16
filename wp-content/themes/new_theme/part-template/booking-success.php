<?php



/**



 Template Name: booking-success



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

    .banner-header {
        width: 100%;
        height: auto;
        min-height: 50vh;
        position: relative;
        background-color: black;
    }
    .banner-header > h1 {
        text-align: center;
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);

        /* invert the color if it is in the same color */
        color: black;
        filter: invert(1);
        mix-blend-mode: difference;
    }
    @media screen and (max-width: 480px) {
        .banner-header {
            margin-top: 40px;
        }
        .banner-header h1 {
            font-size: 25px;
        }
    }
</style>

<!-- Content -->
<div class="content-wrapper">

    <!-- Banner Header -->
    <div class="banner-header">
        <?php if ( get_field( 'image_banner' ) ) { ?>
            <img src="<?php the_field( 'image_banner' ); ?>" />
        <?php } ?>
        
        <h1><?php the_field( 'text_banner' ); ?></h1>
    </div>

<?php get_footer();?>