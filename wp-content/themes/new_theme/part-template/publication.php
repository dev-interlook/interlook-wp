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

    .youtube-preview { position: relative; display: block; }
    .youtube-preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    .youtube-preview:hover .youtube-preview-overlay { opacity: 0; }
    .youtube-preview iframe {
        z-index: 2;
    }
</style>

<?php
    function get_youtube_id_from_url($url) {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        preg_match($pattern, $url, $matches);
        return isset($matches[1]) ? $matches[1] : false;
    }
?>

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
                        <?php while ( have_rows( 'publications' ) ) : the_row(); 
                            $related_url = get_sub_field('related_url');
                            $youtube_id = get_youtube_id_from_url($related_url);
                        ?>
                            <figure>
                                <a href="<?php echo esc_url($related_url); ?>" target="_blank" <?php echo $youtube_id ? 'class="youtube-preview" data-video-id="' . esc_attr($youtube_id) . '"' : ''; ?>>
                                    <?php if ($youtube_id): ?>
                                        <img src="https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/maxresdefault.jpg" alt="<?php the_sub_field('title'); ?>" />
                                    <?php elseif (get_sub_field('image')): ?>
                                        <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('title'); ?>" />
                                    <?php endif; ?>
                                    <figcaption>
                                        <h6><?php the_sub_field('title'); ?></h6>
                                        <p><?php the_sub_field('sub_title'); ?></p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previews = document.querySelectorAll('.youtube-preview');
    
    previews.forEach(preview => {
        let previewTimer;
        
        preview.addEventListener('mouseenter', function(e) {
            const videoId = this.getAttribute('data-video-id');
            
            previewTimer = setTimeout(() => {
                const iframe = document.createElement('iframe');
                iframe.setAttribute('src', `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1`);
                iframe.style.position = 'absolute';
                iframe.style.top = '0';
                iframe.style.left = '0';
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.border = 'none';
                iframe.style.pointerEvents = 'none'; // This prevents iframe from capturing clicks
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                
                this.style.position = 'relative';
                this.insertBefore(iframe, this.firstChild);
            }, 1000);
        });
        
        preview.addEventListener('mouseleave', function() {
            clearTimeout(previewTimer);
            const iframe = this.querySelector('iframe');
            if (iframe) iframe.remove();
        });
    });
});
</script>
