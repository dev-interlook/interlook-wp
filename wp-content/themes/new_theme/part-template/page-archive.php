<?php



/**



 Template Name: Archive



 */



?>

<?php

get_header(); ?>
<br><br><br><br>
<br><br>
<?php

wp_get_archives( [
    'type'            => 'monthly',
    'year'            => is_year() ? get_query_var( 'year' ) : date('Y'),
    'format'          => 'html',
    'show_post_count' => 0,
] ); ?>
<br><br>
<?php
    $start = $_GET["startdate"];
	$end = $_GET["enddate"];
	$monthname = $_GET["monthname"];
	// gets info from URL
 
	query_posts(array(
	   'post_type' => 'events', //query “events”
	   'posts_per_page' => 10,
	   'paged' => $paged,
	   'meta_key' => 'event_date',
	   'orderby' => 'meta_value',
	   'order' => 'ASC', //sort in ascending order
		 'meta_query' => array(
			 array(
				 'key' => 'event_date',
				 'value' => array($start, $end),
				 'compare' => 'BETWEEN',
				 'type' => 'DATE'
			 )
		 ) // meta query between dates
	));
	?>
<?php
	if (have_posts()) :
	while (have_posts()) : the_post(); ?>
	<div class="item"> 
<div class="post-img">
	<?php $image01 = get_field( 'image01' ); ?>
	<?php if ( $image01 ) { ?>
	<a href="<?php the_permalink() ?>"> <img src="<?php echo $image01['url']; ?>" alt=""> </a>
	<?php } ?>
</div>
<div class="post-cont"> <a href="blog.html"><span class="tag">Architecture</span></a> <i>|</i> <span
		class="date"><?php echo get_the_date("d F y"); ?></span>
	<h5>
		<a href="<?php the_permalink() ?>"><?php the_field( 'title01' ); ?></a>
	</h5>
	<p><?php the_field( 'description01' ); ?></p>
</div>
</div>
<?php endwhile; endif; ?>



<?php get_footer(); ?>