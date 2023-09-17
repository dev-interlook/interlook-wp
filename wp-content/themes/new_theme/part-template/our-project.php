<?php



/**



 Template Name: our-project



 */

global $wpdb;
$current_url = explode('?',$_SERVER['REQUEST_URI'])[0];

// Category Project
$category_query = "
    SELECT t.* FROM wp0k_terms t
    JOIN wp0k_term_taxonomy tt ON tt.term_id = t.term_id
    WHERE tt.parent IN (
        SELECT sub_t.term_id FROM wp0k_terms sub_t
        WHERE sub_t.slug = 'project'
    )
";
$category_results = $wpdb->get_results($category_query);

// List Project
$selected_category = count($category_results) ? $category_results[0]->term_id : 0;
if (isset($_GET['c'])) {
    $selected_category = $_GET['c'];
}
$project_query = "
    SELECT p.* FROM wp0k_posts p
    JOIN wp0k_term_relationships tr ON tr.object_id = p.ID
    JOIN wp0k_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
    WHERE tt.term_id = $selected_category
    GROUP BY p.ID
";
$project_results = $wpdb->get_results($project_query);

function getPostmetaData($post_id) {
    global $wpdb;

    $query = "
        SELECT * FROM wp0k_postmeta
        WHERE post_id = $post_id
        AND meta_key NOT LIKE '\\_%'
    ";
    $results = $wpdb->get_results($query);

    $postmeta = [];
    foreach ($results as $row) {
        $postmeta[$row->meta_key] = $row->meta_value;
    }

    return $postmeta;
}

?>

<?php get_header();?>

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
</style>

<!-- Content -->
<div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- Header Banner -->
    <?php $bg_image = get_field( 'bg-image' ); ?>
    <?php if ( $bg_image ) { ?>
    <!-- <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php echo $bg_image['url']; ?>"> -->
    <?php } ?>
    <!-- </section> -->
    <!-- Project Page -->
    <section class="section-padding2 mt-80">
        <div class="container">
            <br><br>
            <div class="row">
                <div class="col-md-3">
                    <div class="col-md-12">
                        <h2 class="section-title2">Our Project</h2>
                    </div>
                    <div class="col-md-12">
                    <?php
                    if (is_array($category_results) && count($category_results)) :
                        foreach ($category_results as $row) :
                    ?>
                        <a href="<?=$current_url?>?c=<?=$row->term_id?>">
                            <?php if ($selected_category == $row->term_id) : ?>
                            <span style="color: #1e73be; text-decoration: underline;"><?=$row->name?></span>
                            <?php else : ?>
                            <span><?=$row->name?></span>
                            <?php endif; ?>
                        </a>
                        <br>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="row">
                    <?php
                    if (is_array($project_results) && count($project_results)) :
                        foreach ($project_results as $row) :
                            $postmeta = getPostmetaData($row->ID);
                    ?>
                        <div class="col-md-4 content_img">
                            <a href="<?=get_site_url()?>/projects/<?=$row->post_name?>">
                                <img src="<?=wp_get_attachment_image_url($postmeta['cover'])?>" alt="Project Cover">
                                <div>
                                    <span><?=$row->post_title?></span>
                                </div>
                            </a>
                        </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    </div>
                </div>
            </div>
            <br><br>

            <!-- <div class="row">
                <div class="col-md-12">
                    <h2 class="section-title2">Cotton House</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <p><?php the_field( 'description-paragraph01' ); ?></p>
                    <p><?php the_field( 'description-paragraph02' ); ?></p>
                </div>
                <div class="col-md-4">
                    <p><b>Year : </b> <?php the_field( 'year' ); ?></p>
                    <p><b>Company : </b> <?php the_field( 'company' ); ?></p>
                    <p><b>Project Name : </b> <?php the_field( 'project-name' ); ?></p>
                    <p><b>Location : </b> <?php the_field( 'location' ); ?></p>
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-md-6 gallery-item">
                <?php $image_left_top = get_field( 'image-left-top' ); ?>
                <?php if ( $image_left_top ) { ?>
                    <a href="<?php echo $image_left_top['url']; ?>" title="Architecture" class="img-zoom">
                    <?php } ?>
                        <div class="gallery-box">
                        <?php $image_left_top = get_field( 'image-left-top' ); ?>
                        <?php if ( $image_left_top ) { ?>
                            <div class="gallery-img"> <img src="<?php echo $image_left_top['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                            <?php } ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 gallery-item">
                <?php $image_right_top = get_field( 'image-right-top' ); ?>
                <?php if ( $image_right_top ) { ?>
                    <a href="<?php echo $image_right_top['url']; ?>" title="Architecture" class="img-zoom">
                    <?php } ?>
                        <div class="gallery-box">
                        <?php $image_right_top = get_field( 'image-right-top' ); ?>
                        <?php if ( $image_right_top ) { ?>
                            <div class="gallery-img"> <img src="<?php echo $image_right_top['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                            <?php } ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 gallery-item">
                <?php $image_bottom_left = get_field( 'image-bottom-left' ); ?>
                <?php if ( $image_bottom_left ) { ?>
                    <a href="<?php echo $image_bottom_left['url']; ?>" title="Architecture" class="img-zoom">
                    <?php } ?>
                        <div class="gallery-box">
                        <?php $image_bottom_left = get_field( 'image-bottom-left' ); ?>
                        <?php if ( $image_bottom_left ) { ?>
                            <div class="gallery-img"> <img src="<?php echo $image_bottom_left['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                            <?php } ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 gallery-item">
                <?php $image_bottom_right = get_field( 'image-bottom-right' ); ?>
                <?php if ( $image_bottom_right ) { ?>
                    <a href="<?php echo $image_bottom_right['url']; ?>" title="Architecture" class="img-zoom">
                    <?php } ?>
                        <div class="gallery-box">
                        <?php $image_bottom_right = get_field( 'image-bottom-right' ); ?>
                        <?php if ( $image_bottom_right ) { ?>
                            <div class="gallery-img"> <img src="<?php echo $image_bottom_right['url']; ?>" class="img-fluid mx-auto d-block" alt="work-img"> </div>
                            <?php } ?>
                        </div>
                    </a>
                </div>
            </div> -->
        </div>
    </section>
    <!-- Prev-Next Projects -->
    <!-- <section class="projects-prev-next">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="projects-prev-next-left">
                            <a href="<?php the_field( 'button-next' ); ?>"> <i class="ti-arrow-left"></i> Previous Project</a>
                        </div> <a href="projects.html"><i class="ti-layout-grid3-alt"></i></a>
                        <div class="projects-prev-next-right"> <a href="<?php the_field( 'button-next' ); ?>">Next Project <i class="ti-arrow-right"></i></a> </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
<?php get_footer();?>