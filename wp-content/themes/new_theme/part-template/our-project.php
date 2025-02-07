<?php



/**



 Template Name: our-project



 */

global $wpdb;
$current_url = explode('?',$_SERVER['REQUEST_URI'])[0];

// Category Project
# Project Built
$category_built = "
    SELECT t.* FROM wp0k_terms t
    JOIN wp0k_term_taxonomy tt ON tt.term_id = t.term_id
    WHERE tt.parent IN (
        SELECT sub_t.term_id FROM wp0k_terms sub_t
        WHERE sub_t.slug = 'project'
    )
    ORDER BY t.name ASC
";
$cat_built_results = $wpdb->get_results($category_built);

# Design Project
$category_design = "
    SELECT t.* FROM wp0k_terms t
    JOIN wp0k_term_taxonomy tt ON tt.term_id = t.term_id
    WHERE tt.parent IN (
        SELECT sub_t.term_id FROM wp0k_terms sub_t
        WHERE sub_t.slug = 'design-project'
    )
    ORDER BY t.name ASC
";
$cat_design_results = $wpdb->get_results($category_design);

// Latest 3 project
$cat_built_parent = $wpdb->get_results("SELECT sub_t.term_id FROM wp0k_terms sub_t WHERE sub_t.slug = 'project'")[0]->term_id;
$q_latest_built = "
    SELECT p.* FROM wp0k_posts p
    JOIN wp0k_term_relationships tr ON tr.object_id = p.ID
    JOIN wp0k_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
    WHERE tt.term_id = $cat_built_parent
    GROUP BY p.ID
    ORDER BY p.post_date_gmt DESC
    LIMIT 3
";
$r_latest_built = $wpdb->get_results($q_latest_built);

$cat_design_parent = $wpdb->get_results("SELECT sub_t.term_id FROM wp0k_terms sub_t WHERE sub_t.slug = 'design-project'")[0]->term_id;
$q_latest_design = "
    SELECT p.* FROM wp0k_posts p
    JOIN wp0k_term_relationships tr ON tr.object_id = p.ID
    JOIN wp0k_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
    WHERE tt.term_id = $cat_design_parent
    GROUP BY p.ID
    ORDER BY p.post_date_gmt DESC
    LIMIT 3
";
$r_latest_design = $wpdb->get_results($q_latest_design);

// List Project
$category = "";
$selected_category = 0;
$project_results = [];
if (isset($_GET['c'])) {
    $selected_category = $_GET['c'];

    $q_where = "WHERE 1=1";
    if ($selected_category == $cat_built_parent || $selected_category == $cat_design_parent)
        $q_where = "WHERE tt.parent = $selected_category";
    else
        $q_where = "WHERE tt.term_id = $selected_category";

    $project_query = "
        SELECT p.*, t.name as category FROM wp0k_posts p
        JOIN wp0k_term_relationships tr ON tr.object_id = p.ID
        JOIN wp0k_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        JOIN wp0k_terms t ON t.term_id = tt.term_id
        $q_where
        GROUP BY p.ID
        ORDER BY t.name ASC, p.post_date_gmt DESC
    ";
    $project_results = $wpdb->get_results($project_query);
}

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
    .section-padding2 {
        min-height: 90vh;
    }

    .section-title2 {
        color: #000;
        font-weight: 600;
    }

    .force-fit-img {
        position: relative;
        width: 100%;
        padding-top: 100% !important; /* Creates 1:1 aspect ratio */
        display: block;
        overflow: hidden;
    }
    .force-fit-img img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* This maintains image quality while forcing 1:1 */
    }

    /* Parent Container for Image Hover */
    .content_img {
        margin-bottom: 30px;
    }
    @media screen and (max-width: 768px) {
        .content_img {
            margin-bottom: 30px;
        }
    }
    @media screen and (max-width: 480px) {
        .content_img {
            padding: 0px 15px 15px 15px;
        }
    }

    /* Child Text Container */
    .content_img div {
        width: auto;
        height: fit-content;
        position: absolute;
        bottom: 0;
        right: 15px;
        left: 15px;
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
        padding-top: 15px;
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


    .left-side {
        width: 25%;
        float: left;
        z-index: 100;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;     /* Firefox */
        overflow-y: scroll;        /* Enable vertical scroll */
    }
    /* Chrome, Safari and Opera */
    .left-side::-webkit-scrollbar {
        display: none;
        width: 0;
    }
    .left-side.mobile-only {
        display: none;
    }
    .left-side.mobile-only.show {
        display: block;
    }

    .nav-category {
        width: 100%;
        height: 35vh;
        display: flex;
        flex-direction: column;
        overflow-y: scroll;
        padding-bottom: 15px;
    }
    .nav-category a {
        width: 100%;
        padding-left: 15px;
    }

    .projects-wrapper {
        width: 100%;
        height: 35vh;
        display: flex;
        flex-direction: column;
        overflow-y: scroll;
        padding-bottom: 15px;
    }
    .projects-wrapper a {
        width: 100%;
        padding-left: 15px;
    }
    
    #categorized-project a {
        width: 100%;
        padding-left: 15px;
    }

    .right-side {
        width: 75%;
        height: 77vh;
        margin-left: 30%;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;     /* Firefox */
        overflow-y: scroll;        /* Enable vertical scroll */
    }
    /* Chrome, Safari and Opera */
    .right-side::-webkit-scrollbar {
        display: none;
        width: 0;
    }
    .title-lates {
        display: none;
    }

    .rotate-down {
        transform: rotate(90deg);
        transition: transform 0.3s ease;
    }
    .toggle-arrow {
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    @media screen and (max-width: 480px) {
        .left-side {
            width: 100%;
            float: left;
            min-height: 10vh;
            padding: 0px 15px 15px 15px;
        }
        .nav-category {
            display: none;
        }

        .projects-wrapper {
            height: auto;
        }

        .right-side {
            width: 100%;
            margin-left: 0;
            height: auto;
            overflow-y: auto;
        }
        .title-lates {
            display: block;
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
            <div class="left-side">
                <div>
                    <h2 class="section-title2">Our Project</h2>
                </div>
                <!-- Project Built -->
                <div class="nav-category">
                    <h5 style="margin-bottom: 5px;">
                        <a href="<?=$current_url?>?c=<?=$cat_built_parent?>" style="padding: 0;">
                        <?php if ($selected_category == $cat_built_parent) : ?>
                            <span style="color: #1e73be; text-decoration: underline;">Project Built</span>
                        <?php else : ?>
                            <span>Project Built</span>
                        <?php endif; ?>
                        </a>
                    </h5>

                    <div>
                    <?php
                    if (is_array($cat_built_results) && count($cat_built_results)) :
                        foreach ($cat_built_results as $row) :
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
                <hr style="border-top: 1px black solid; margin-bottom: 15px;">
                <!-- Design Project -->
                <div class="nav-category">
                    <h5 style="margin-bottom: 5px;">
                        <a href="<?=$current_url?>?c=<?=$cat_design_parent?>" style="padding: 0;">
                        <?php if ($selected_category == $cat_design_parent) : ?>
                            <span style="color: #1e73be; text-decoration: underline;">Design Project</span>
                        <?php else : ?>
                            <span>Design Project</span>
                        <?php endif; ?>
                        </a>
                    </h5>

                    <div>
                    <?php
                    if (is_array($cat_design_results) && count($cat_design_results)) :
                        foreach ($cat_design_results as $row) :
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

            <div class="right-side">
                <div id="latest-project" style="margin-top: 65px; overflow-y: unset;">
                    <!-- Project Built -->
                    <div class="projects-wrapper">
                        <div class="row" style="margin: 0;">
                            <h5 class="col-md-12 title-lates">
                                Project Built
                                <i class="fas fa-angle-right toggle-arrow"></i>
                            </h5>
                            <!-- sub category only for mobile -->
                            <div class="left-side mobile-only">
                            <?php
                            if (is_array($cat_built_results) && count($cat_built_results)) :
                                foreach ($cat_built_results as $row) :
                            ?>
                                <a href="<?=$current_url?>?c=<?=$row->term_id?>&p=Project Built">>
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

                            <?php
                            if (is_array($r_latest_built) && count($r_latest_built)) :
                                foreach ($r_latest_built as $row) :
                                    $postmeta = getPostmetaData($row->ID);
                            ?>
                                    <div class="col-md-4 content_img">
                                        <a href="<?=get_site_url()?>/projects/<?=$row->post_name?>" class="force-fit-img" style="padding: 0;">
                                            <img src="<?=wp_get_attachment_image_url($postmeta['cover'], 'full')?>" alt="Project Cover">
                                            <div class="content_wrap">
                                                <h6><?=$row->post_title?></h6>
                                            </div>
                                        </a>
                                    </div>
                            <?php
                                endforeach;
                            ?>
                                <a href="<?=$current_url?>?c=<?=$cat_built_parent?>" style="color: #1e73be; margin-top: -35px; height: min-content;" class="col-md-12 text-right">more...</a>
                            <?php
                            else:
                                echo "<div class='col-md-12'><p>There is no project related to this category yet.</p></div>";
                            endif;
                            ?>
                        </div>
                    </div>
                    <hr style="border-top: 1px black solid; margin-bottom: 15px;">
                    <!-- Design Project -->
                    <div class="projects-wrapper">
                        <div class="row" style="margin: 0;">
                            <h5 class="col-md-12 title-lates">
                                Design Project
                                <i class="fas fa-angle-right toggle-arrow"></i>
                            </h5>
                            <!-- sub category only for mobile -->
                            <div class="left-side mobile-only">
                            <?php
                            if (is_array($cat_built_results) && count($cat_built_results)) :
                                foreach ($cat_built_results as $row) :
                            ?>
                                <a href="<?=$current_url?>?c=<?=$row->term_id?>&p=Design Project">
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
                            
                            <?php
                            if (is_array($r_latest_design) && count($r_latest_design)) :
                                foreach ($r_latest_design as $row) :
                                    $postmeta = getPostmetaData($row->ID);
                            ?>
                                    <div class="col-md-4 content_img">
                                        <a href="<?=get_site_url()?>/projects/<?=$row->post_name?>" class="force-fit-img" style="padding: 0;">
                                            <img src="<?=wp_get_attachment_image_url($postmeta['cover'], 'full')?>" alt="Project Cover">
                                            <div class="content_wrap">
                                                <h6><?=$row->post_title?></h6>
                                            </div>
                                        </a>
                                    </div>
                            <?php
                                endforeach;
                            ?>
                                <a href="<?=$current_url?>?c=<?=$cat_design_parent?>" style="color: #1e73be; margin-top: -35px; height: min-content;" class="col-md-12 text-right">more...</a>
                            <?php
                            else:
                                echo "<div class='col-md-12'><p>There is no project related to this category yet.</p></div>";
                            endif;
                            ?>
                        </div>
                    </div>
                </div>

                <div id="categorized-project" class="row" style="margin: 0;">
                    <h5 class="col-md-12 title-lates">
                        <?=$_GET['p']?>
                        <i class="fas fa-angle-right toggle-arrow"></i>
                    </h5>
                    <!-- sub category only for mobile -->
                    <div class="left-side mobile-only">
                    <?php
                    if (is_array($cat_built_results) && count($cat_built_results)) :
                        foreach ($cat_built_results as $row) :
                    ?>
                        <a href="<?=$current_url?>?c=<?=$row->term_id?>&p=<?=$_GET['p']?>">
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
                    <small>
                        <a href="<?=$current_url?>" style="color: #1e73be;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </small>
                    </div>


                    <?php
                    if (is_array($project_results) && count($project_results)) :
                        foreach ($project_results as $row) :
                            $postmeta = getPostmetaData($row->ID);
                    ?>
                        <?php if ($category != $row->category): ?>
                        <div class="col-md-12">
                            <h6><?= $row->category; ?></h6>
                        </div>
                        <?php $category = $row->category; ?>
                        <?php endif; ?>
                        
                        <div class="col-md-6 col-lg-4 content_img">
                            <a href="<?=get_site_url()?>/projects/<?=$row->post_name?>" class="force-fit-img">
                                <img src="<?=wp_get_attachment_image_url($postmeta['cover'], 'full')?>" alt="Project Cover">
                                <div class="content_wrap">
                                    <h6><?=$row->post_title?></h6>
                                </div>
                            </a>
                        </div>
                    <?php
                        endforeach;
                    else:
                        echo "<div class='col-md-12'><p>There is no project related to this category yet.</p></div>";
                    endif;
                    ?>
                </div>
            </div>
            <br><br>
        </div>
    </section>

<?php get_footer();?>

<script>
    const searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('c')) {
        $("#latest-project").hide();
        $("#categorized-project").show();
        $(".right-side").css({
            overflowY: "scroll"
        });

    } else {
        $("#latest-project").show();
        $("#categorized-project").hide();
        $(".right-side").css({
            overflowY: "unset"
        });
    }

    const titles = document.querySelectorAll('.title-lates');
    const mobileSubCategory = document.querySelectorAll('.left-side.mobile-only');
    titles.forEach((title, index) => {
        title.style.cursor = 'pointer';
        title.addEventListener('click', function(event) {
            const arrow = this.querySelector('.toggle-arrow');
            arrow.classList.toggle('rotate-down');
            if (mobileSubCategory[index].classList.contains('show')) {
                mobileSubCategory[index].classList.remove('show');
            } else {
                mobileSubCategory[index].classList.add('show');
            }
        });
    });

</script>