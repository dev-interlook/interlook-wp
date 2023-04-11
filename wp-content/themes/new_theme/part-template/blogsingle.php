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
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php the_field( 'bg-image' ); ?>">
         </section>
        <!-- Post -->
        <section class="pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <br>
                        <img src="<?php the_field( 'image' ); ?>" class="mb-30" alt="">
                        <h2 class="section-title2"><?php the_title(); ?></h2>
                        <p><?php the_field( 'content' ); ?></p>
                        </div>
                </div>
            </div>
        </section>
                <?php get_footer();?>