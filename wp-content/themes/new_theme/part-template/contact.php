<?php



/**



 Template Name: contact



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
    <?php if ( have_rows( 'banner' ) ) : ?>
        <?php while ( have_rows( 'banner' ) ) : the_row(); ?>
            <!-- Header Banner -->
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php the_sub_field( 'background' ); ?>"></section>
        <?php endwhile; ?>
    <?php endif; ?>
    <!-- Contact -->
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12 animate-box mt-50" data-animate-effect="fadeInUp">
                <?php if ( have_rows( 'banner' ) ) : ?>
                    <?php while ( have_rows( 'banner' ) ) : the_row(); ?>
                        <h5><?php the_sub_field( 'title_page' ); ?></h5>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>
            <div class="row mb-90 d-flex">
            <?php if ( have_rows( 'contact' ) ) : ?>
                <?php while ( have_rows( 'contact' ) ) : the_row(); ?>
                <table class="col-md-10 animate-box table-borderless" style="padding: 0;">
                    <tbody>
                        <tr>
                            <td width="5%" style="font-size: 16pt;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/maps.png" alt="Working Address">
                            </td>
                            <td colspan="3"><?php the_sub_field( 'address' ); ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td width="5%" style="font-size: 16pt;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/phone.png" alt="Working Phone">
                            </td>
                            <td width="30%"><?php the_sub_field( 'phone' ); ?></td>

                            <td width="5%" style="font-size: 16pt;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/email.png" alt="Working Email">
                            </td>
                            <td width="30%"><?php the_sub_field( 'email' ); ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td width="5%" style="font-size: 16pt;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/insta.png" alt="Instagram Account">
                            </td>
                            <td width="30%">
                                <a href="<?php the_sub_field( 'ig_link' ); ?>" target="_blank">
                                    <?php the_sub_field( 'ig_name' ); ?>
                                </a>
                            </td>

                            <td width="5%" style="font-size: 16pt;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/website.png" alt="Website Address">
                            </td>
                            <td width="30%">
                                <a href="<?php the_sub_field( 'website' ); ?>">
                                <?php the_sub_field( 'website' ); ?>
                                </a>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <?php endwhile; ?>
            <?php endif; ?>
            </div>
        </div>
        <div class="container" style="max-width: none; padding: 0;">
            <!-- Map Section -->
            <div class="row">
            <?php if ( have_rows( 'contact' ) ) : ?>
                <?php while ( have_rows( 'contact' ) ) : the_row(); ?>
                <div class="col-md-12 animate-box d-flex justify-content-center" data-animate-effect="fadeInUp">
                    <?php the_sub_field( 'embed_gmaps' ); ?>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
            </div>
        </div>
    </section>
    <?php get_footer();?>