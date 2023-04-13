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
  <!-- Content -->
  <div class="content-wrapper">
        <!-- Lines -->
        <section class="content-lines-wrapper">
            <div class="content-lines-inner">
                <div class="content-lines"></div>
            </div>
        </section>
        <!-- Header Banner -->
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php the_field( 'cover' ); ?>">
         </section>
        <!-- Post -->
        <section class="full-width-section    ">
            <div class="block-wrapper  section-padding2 ">
                <div class="section-wrapper container ">
                    <div class="row-layout  row ">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner">
                                <div class="wpb_wrapper">
                                    <div class="vc_row wpb_row vc_inner vc_row-fluid ">
                                        <div class="wpb_column vc_column_container vc_col-sm-12">
                                            <div class="vc_column-inner"><div class="wpb_wrapper">
                                                <div class="sec-heading  ">
                                                    <h2 class="section-title2"><?php the_title(); ?></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="vc_row wpb_row vc_inner vc_row-fluid Content ">
                                    <div class="wpb_column vc_column_container vc_col-sm-8">
                                        <div class="vc_column-inner"><div class="wpb_wrapper">
                                            <div class="sec-context "><p></p>
                                            <?php the_field( 'content' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ( have_rows( 'info' ) ) : ?>
	<?php while ( have_rows( 'info' ) ) : the_row(); ?>
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="vc_column-inner"><div class="wpb_wrapper">
                                    <div class="sec-list ">
                                        <p><span class="text-color-red"> Year : </span> <?php the_sub_field( 'year' ); ?></p>
                                    </div>
                                    <div class="sec-list "><p>
                                        <span class="text-color-red"> Company : </span> <?php the_sub_field( 'company' ); ?>
                                    </p>
                                </div>
                                <div class="sec-list ">
                                    <p><span class="text-color-red"> Project Name : </span> <?php the_title(); ?> </p>
                                </div>
                                <div class="sec-list ">
                                    <p><span class="text-color-red"> Location : </span> <?php the_sub_field( 'location' ); ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="vc_row wpb_row vc_inner vc_row-fluid mt-30">
                    <div class="wpb_column vc_column_container vc_col-sm-6">
                        <div class="vc_column-inner">
                            
                            <div class="wpb_wrapper gallery">

                                
<?php if ( have_rows( 'gallery' ) ) : ?>
	<?php while ( have_rows( 'gallery' ) ) : the_row(); ?>

                                <div class="sec-gallery ">
                                    <div class="gallery-item">
                                        <a href="<?php the_sub_field( 'image' ); ?>" class="img-zoom">
                                            <div class="gallery-box">
                                                <div class="gallery-img">
                                                    <img src="<?php the_sub_field( 'image' ); ?>" title="1" alt="1" class="img-fluid mx-auto d-block">
                                                </div>
                                            </div>
                                        </a>
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
                    
                
        </section>
        

                <?php get_footer();?>