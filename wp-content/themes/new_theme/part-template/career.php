<?php



/**



 Template Name: career



 */



?>

<style>
    .career {
        margin: 150px 10% 50px 10% !important;
    }
    .career-intro {
        margin-top: 70px;
    }
    @media screen and (max-width: 480px) {
        .career {
            margin: 150px 0px 50px 0px !important;
        }
        .career-intro {
            margin-top: 0px;
        }
    }
</style>

<?php get_header();?>
<!-- Content -->
<div class="content-wrapper">
    <!-- Career -->
    <section>
        <div class="container mt-50">
            <div class="row career">
                <div class="col-md-4">
                    <h1>Career</h1>
                </div>
                <div class="col-md-8">
                    <div class="career-intro" style="line-height: 1.3;">
                        <?php echo nl2br(get_field('career')); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php if ( have_rows( 'position_available' ) ) : ?>
                    <?php while ( have_rows( 'position_available' ) ) : the_row(); ?>
                        <?php if ( have_rows( 'position' ) ) : ?>
                            <?php while ( have_rows( 'position' ) ) : the_row(); ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php the_sub_field( 'title' ); ?></h5>
                                            <p class="card-text"><?php the_sub_field( 'description' ); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php get_footer();?>