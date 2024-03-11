<?php



/**



 Template Name: publication



 */

get_header();

?>

<style>
    .section-padding2 {
        min-height: 90vh;
    }

    .section-title2 {
        color: #000;
        font-weight: 600;
    }

    .container-publication {
        column-count: 4;
        column-gap: 20px;
    }
    @media screen and (max-width: 480px) {
        .container-publication {
            column-count: 2;
        }
    }

    img {
        max-width: 100%;
        display: block;
    }

    figure {
        margin: 0;
        display: grid;
        grid-template-rows: 1fr auto;
        margin-bottom: 10px;
        break-inside: avoid;
    }
    figure a {
        color: black;
        text-decoration: none;
    }
    figure > a > img {
        grid-row: 1 / -1;
        grid-column: 1;
    }

    figcaption {
        grid-row: 2;
        grid-column: 1;
        background-color: rgba(255,255,255,.5);
        justify-self: start;
    }
    figcaption h6 {
        margin: 5px 0px 0px 0px;
    }
    figcaption p {
        color: rgba(51,51,51,.7);
        margin: 0px 0px 15px 0px;
    }
</style>

<!-- Content -->
<div class="content-wrapper">

    <!-- Publication Page -->
    <section class="section-padding2 mt-100">
        <div class="container">
            <h2 class="section-title2">Publication</h2>

            <div class="container-publication">
                <!-- List of publications -->
                <div>
                    <?php if ( have_rows( 'publications' ) ) : ?>
                        <?php while ( have_rows( 'publications' ) ) : the_row(); ?>
                            <figure>
                                <a href="<?php the_sub_field( 'related_url' ); ?>" target="_blank">
                                    <?php if ( get_sub_field( 'image' ) ) { ?>
                                        <img src="<?php the_sub_field( 'image' ); ?>" alt="A windmill" />
                                    <?php } ?>
                                    <figcaption>
                                        <h6><?php the_sub_field( 'title' ); ?></h6>
                                        <p><?php the_sub_field( 'sub_title' ); ?></p>
                                    </figcaption>
                                </a>
                            </figure>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php // no rows found ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer();?>