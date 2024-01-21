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
    .content_img {
        /* padding: 0px 10px 10px 0px; */
        margin-bottom: 10px;
    }
    @media screen and (max-width: 768px) {
        .content_img {
            /* padding: 0px 15px 15px 0px; */
            margin-bottom: 15px;
        }
    }
    @media screen and (max-width: 480px) {
        .content_img {
            padding: 0px 15px 15px 15px;
        }
    }

    /* Child Text Container */
    .content_img div {
        width: 100%;
        height: 100%;
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

    .content_wrap {
        display: flex;
    }
    .content_wrap h6 {
        margin: 0 0 20px 20px;
        align-self: flex-end;
    }


    .left_side {
        width: 25%;
        float: left;
        z-index: 100;
    }
    .nav_category {
        height: 70vh;
        display: flex;
        flex-direction: column;
    }
    .nav_category a {
        width: 100%;
    }

    .right_side {
        width: 75%;
        height: 77vh;
        overflow-y: scroll;
        margin-left: 30%;
    }

    @media screen and (max-width: 480px) {
        .left_side {
            width: 100%;
            float: left;
            min-height: 10vh;
            padding: 0px 15px 15px 15px;
        }
        .nav_category {
            height: auto;
        }

        .right_side {
            width: 100%;
            margin-left: 0;
            height: auto;
            overflow-y: auto;
        }
    }
</style>

<!-- Content -->
<div class="content-wrapper">
    <!-- Lines -->
    <!-- <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section> -->
    
    <!-- Project Page -->
    <section class="section-padding2 mt-50">
        <div class="container">
            <br><br>
            <div class="left_side">
                <div>
                    <h2 class="section-title2">Our Project</h2>
                </div>
                <div class="nav_category">
                    <div style="margin-top: auto;">
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
                    <?php
                        endforeach;
                    endif;
                    ?>
                    </div>
                </div>
            </div>

            <div class="right_side">
                <div class="row" style="margin: 0px 0px 0px 0px;">
                <?php
                if (is_array($project_results) && count($project_results)) :
                    foreach ($project_results as $row) :
                        $postmeta = getPostmetaData($row->ID);
                ?>
                    <div class="col-md-6 col-lg-4 content_img">
                        <a href="<?=get_site_url()?>/projects/<?=$row->post_name?>">
                            <img src="<?=wp_get_attachment_image_url($postmeta['cover'])?>" alt="Project Cover">
                            <div class="content_wrap">
                                <h6><?=$row->post_title?></h6>
                            </div>
                        </a>
                    </div>
                <?php
                    endforeach;
                endif;
                ?>
                </div>
            </div>
            <br><br>
        </div>
    </section>
<?php get_footer();?>