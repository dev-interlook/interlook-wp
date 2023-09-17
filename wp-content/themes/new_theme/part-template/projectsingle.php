<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 * @version 1.0
 */

get_header(); ?>

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

    /* Parent Container for Image Hover */
    .content_img{
        padding: 0px 2px 2px 2px;
    }

    /* Child Text Container */
    .content_img div{
        width: 100%;
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        background: black;
        color: white;
        font-family: sans-serif;
        opacity: 0;
        visibility: hidden;
        -webkit-transition: visibility 0s, opacity 0.5s linear;
        transition: visibility 0s, opacity 0.5s linear;
    }

    /* Hover on Parent Container */
    .content_img:hover{
        cursor: pointer;
    }

    .content_img:hover div{
        padding: 8px 15px;
        visibility: visible;
        opacity: 0.7;
    }

    .first-item {
        width: 100%;
    }
    .flex-container {
        display: flex;
        align-items: stretch;
    }
    .gallery-item {
        padding-top: 10px;
        padding-right: 10px;
    }
    .more-item {
        position: absolute;
        background-color: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .more-item span {
        position: absolute;
        color: white;
        font-size: 30px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .hidden-item {
        width: 0;
        padding: 0;
        margin: 0;
    }
</style>

<!-- Counting gallery item -->
<?php
$totalImg = 0;
if ( have_rows( 'gallery' ) ) :
    while ( have_rows( 'gallery' ) ) :
        the_row();
        $totalImg++;
    endwhile;
endif;

$moreImg = $totalImg - 6;
?>

<!-- Content -->
<div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- Header Banner -->
    <!-- <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php the_field( 'cover' ); ?>">
    </section> -->
    <!-- Post -->
    <section class="full-width-section mt-80">
        <div class="block-wrapper section-padding2">
            <div class="section-wrapper container">
                <div class="row-layout row">
                    <div class="col-md-3">
                        <div class="col-md-12">
                            <h2 class="section-title2"><?=the_title()?></h2>
                        </div>
                        <div class="col-md-12">
                            <?php if ( have_rows( 'info' ) ) : ?>
                                <?php while ( have_rows( 'info' ) ) : the_row(); ?>
                                    <span class="text-color-red">Type : </span>
                                    &nbsp;<?php the_sub_field( 'type' ); ?><br>

                                    <span class="text-color-red">Area : </span>
                                    &nbsp;<?php the_sub_field( 'area' ); ?><br>

                                    <span class="text-color-red">Year : </span>
                                    &nbsp;<?php the_sub_field( 'year' ); ?><br>

                                    <span class="text-color-red">Architect : </span>
                                    &nbsp;<?php the_sub_field( 'architect' ); ?><br>

                                    <span class="text-color-red">City : </span>
                                    &nbsp;<?php the_sub_field( 'city' ); ?><br>

                                    <span class="text-color-red">Country : </span>
                                    &nbsp;<?php the_sub_field( 'country' ); ?><br>

                                    <span class="text-color-red">Status : </span>
                                    &nbsp;<?php the_sub_field( 'status' ); ?><br>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="first-item">
                        <?php if ( have_rows( 'gallery' ) ) : ?>
                            <?php while ( have_rows( 'gallery' ) ) : the_row(); ?>
                                <div class="gallery-item">
                                    <a href="<?php the_sub_field( 'image' ); ?>" class="img-zoom">
                                        <div class="gallery-box">
                                            <div class="gallery-img">
                                                <img src="<?php the_sub_field( 'image' ); ?>" title="1" alt="1" class="img-fluid mx-auto d-block">
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php break; ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <?php // no rows found ?>
                        <?php endif; ?>
                        </div>

                        <div class="flex-container">
                        <?php if ( have_rows( 'gallery' ) ) : ?>
                            <?php while ( have_rows( 'gallery' ) ) : the_row(); ?>
                                <?php if (get_row_index() == 1) continue; ?>
                                
                                <?php if (get_row_index() < 8) : ?>
                                <div class="gallery-item">
                                <?php else : ?>
                                <div class="hidden-item">
                                <?php endif; ?>

                                    <a href="<?php the_sub_field( 'image' ); ?>" class="img-zoom">
                                        <div class="gallery-box">
                                            <div class="gallery-img">
                                                <img src="<?php the_sub_field( 'image' ); ?>" title="1" alt="1" class="img-fluid mx-auto d-block">

                                                <?php if (get_row_index() == 7) : ?>
                                                    <div class="more-item">
                                                        <span><?=$moreImg?>+</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <?php // no rows found ?>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vc_row wpb_row vc_inner vc_row-fluid mt-30">
                <div class="wpb_column vc_column_container vc_col-sm-6">
                    <div class="vc_column-inner">
                    </div>
                </div>
            </div>
        </section>

<?php get_footer();?>