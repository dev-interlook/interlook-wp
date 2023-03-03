<?php



/**



 Template Name: bloglist



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
        <!-- Header Banner -->
        <section class="banner-header banner-img valign bg-img bg-fixed" data-overlay-darkgray="5" data-background="<?php the_field( 'background' ); ?>"></section>
         <!-- Blog  -->
        <section class="bauen-blog3 section-padding2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<br>
						<h2 class="section-title"><?php the_field( 'title' ); ?></h2> </div>
				</div>
				
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-12">

							<?php $currentPage = get_query_var('paged'); ?>

							<?php $latest = new WP_Query(array('cat' => 3 ,'posts_per_page' => 4, 'paged' => $currentPage));?>

							<?php if(have_posts()) :?>  <?php while($latest->have_posts()) : $latest->the_post();?>
								<div class="item">
									<div class="post-img">
                                   	<a href="<?php the_permalink() ?>"> <img src="<?php the_field( 'image' ); ?>" alt=""> </a>
                                   </div>
									<div class="post-cont"> <a href="#"><span class="tag">Architecture</span></a> <i>|</i> <span class="date"><?php 	
$post_date = get_the_date( 'D , j M  Y' ); echo $post_date; ?></span>
										<h5>
                                        <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
                                    </h5>
										<p><?php echo substr(get_field( 'sort_content' ),6); ?></p>
									</div>
								</div>
								<?php endwhile; endif; ?>
							</div>
						</div>
					</div>
				
					<div class="col-md-4">
						<div class="blog-sidebar row">
							<div class="col-md-12">
								<div class="widget search">
									<form action="/search/" method="post">
										<input type="text" name="search" placeholder="Type here ...">
										<button type="submit"><i class="ti-search" aria-hidden="true"></i></button>
									</form>
								</div>
							</div>
							<div class="col-md-12">
								<div class="widget">
									<div class="widget-title">
										<h6>Recent Posts</h6> </div>
									<ul class="recent">

									<?php $currentPage = get_query_var('paged'); ?>

							<?php $latest = new WP_Query(array('cat' => 3 ,'posts_per_page' => 4));?>

							<?php if(have_posts()) :while($latest->have_posts()) : $latest->the_post();?>
							
									<div class="post-img">
                                    	<div class="thum"> <img src="<?php the_field( 'image' ); ?>" alt=""> </div> <a href="<?php the_permalink() ?>"><?php the_title(); ?></a> </li>
                                    </div>
								
								
								<?php endwhile; endif; ?>

										<!-- <li>
											<div class="thum"> <img src="img/projects/1.jpg" alt=""> </div> <a href="post.html">Modern Architectural Structures</a> </li>
										<li>
											<div class="thum"> <img src="img/projects/2.jpg" alt=""> </div> <a href="post2.html">Modernism in Architecture</a> </li>
										<li>
											<div class="thum"> <img src="img/projects/3.jpg" alt=""> </div> <a href="post3.html">Postmodern Architecture</a> </li> -->
									</ul>
								</div>
							</div>
							<div class="col-md-12">
								<div class="widget">
									<div class="widget-title">
										<h6>Archives</h6> </div>
										
									<ul>
										
<div class="archives-by-month-section">
  <p><li><a href= "<?php wp_get_archives( array( 'type' => 'monthly', 'format' => 'option', 'show_post_count' => 1 ) ); ?>"></a><?php wp_get_archives( array( 'type' => 'monthly', 'format' => 'option', 'show_post_count' => 1 ) ); ?></li></p>
</div>

									<!-- <option value=""><?php echo esc_attr( __( 'Select Month' ) ); ?></option>  -->
									<!-- <a href="<?php echo get_the_date( 'Y/m' ); ?>"> -->
									<!-- <?php wp_get_archives( array( 'type' => 'monthly', 'format' => 'option', 'show_post_count' => 1 ) ); ?></a> -->
										<!-- <li><a href="#"><?php echo date("M"); ?> <?php echo date("Y"); ?></a></li> -->
										<!-- <li><a href="#">November 2022</a></li>
										<li><a href="#">October 2022</a></li> -->
										<!-- <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
    <option value=""><?php esc_attr( _e( 'Select Month', 'textdomain' ) ); ?></option> 
    <?php wp_get_archives( array( 'type' => 'monthly', 'format' => 'option', 'show_post_count' => 1 ) ); ?>
</select> -->
									</ul>
								</div>
							</div>
							<div class="col-md-12">
								<div class="widget">
									<div class="widget-title">
										<h6>Categories</h6> </div>

										<?php
// Get the current queried object
$term    = get_queried_object();
$term_id = ( isset( $term->term_id ) ) ? (int) $term->term_id : 0;

$categories = get_categories( array(
    'taxonomy'   => 'category', // Taxonomy to retrieve terms for. We want 'category'. Note that this parameter is default to 'category', so you can omit it
    'orderby'    => 'name',
    'parent'     => 0,
    'hide_empty' => 0, // change to 1 to hide categores not having a single post
) );
?>

<ul>
    <?php
    foreach ( $categories as $category ) 
    {
        $cat_ID        = (int) $category->term_id;
        $category_name = $category->name;

        // When viewing a particular category, give it an [active] class
        $cat_class = ( $cat_ID == $term_id ) ? 'active' : 'not-active';

        // I don't like showing the [uncategoirzed] category
        if ( strtolower( $category_name ) != 'uncategorized' )
        {
            printf( '%3$s',
                esc_attr( $cat_class ),
                esc_url( get_category_link( $category->term_id ) ),
                esc_html( $category->name )
            );
        }
    }
    ?>
</ul>
										

									<!-- <ul>
										<li><a href="post.html"><i class="ti-angle-right"></i>Architecture</a></li>
										<li><a href="post.html"><i class="ti-angle-right"></i>Interior</a></li>
										<li><a href="post.html"><i class="ti-angle-right"></i>Exterior</a></li>
									</ul> -->
								</div>
							</div>
							<div class="col-md-12">
								<div class="widget">
									<div class="widget-title">
										<h6>Tags</h6> </div>
									<ul class="tags">
									<?php
    $tags = get_tags();
    if ($tags) {
?><ul class="tags"><?php
    foreach ($tags as $tag) {
        echo '<li><a href="' . get_tag_link( $tag->term_id ) . '" 
              title="' . sprintf( __( "View all posts in %s" ), $tag->name ) . '" ' . '>' . $tag->name.'</a></li>';
    }
   ?></ul>
<?php }?>   
										<!-- <li><a href="#">Architecture</a></li>
										<li><a href="#">Interior</a></li>
										<li><a href="#">Exterior</a></li>
										<li><a href="#">Project</a></li>
										<li><a href="#">3D Modelling</a></li>
										<li><a href="#">Design</a></li> -->
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<!-- Pagination -->

						<?php 





$pages = paginate_links( array(

		'current' => max( 1, get_query_var('paged') ),

		'total' => $latest->max_num_pages,

		'type'  => 'array',

		'prev_next' => true,

		'prev_text' => __('<i class="ti-angle-left"></i>'),

		'next_text' => __('<i class="ti-angle-right"></i>'),

	) );

	if( is_array( $pages ) ) {

		$paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');

		echo '<ul class="bauen-pagination-wrap align-center mb-30 mt-30">';

		foreach ( $pages as $page ) {

				echo '<li class="page-item">'.$page.'</li>';

		}

	   echo '</ul>';

		}



?>

					
					</div>
				</div>
			</div>
		</section>
        <?php get_footer();?>