<?php 
add_theme_support( 'title-tag' );

function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );
function custom_woocommerce_product_add_to_cart_text() {
	global $product;
	
	$product_type = $product->product_type;
	
	switch ( $product_type ) {
		case 'external':
			return __( 'Buy Product', 'woocommerce' );
		break;
		case 'grouped':
			return __( 'View Products', 'woocommerce' );
		break;
		case 'simple':
			return __( 'Add Cart', 'woocommerce' );
		break;
		case 'variable':
			return __( 'Select Options', 'woocommerce' );
		break;
		default:
			return __( 'Read more', 'woocommerce' );
	}
	
}

function wp_get_attachment( $attachment_id ) {

    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}

add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => ' &#47; ',
            'wrap_before' => '<ul class="breadcrumb justify-content-lg-end">',
            'wrap_after'  => '</ul>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
        );
}

function bittersweet_pagination() {

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) return; 

$big = 999999999; // need an unlikely integer

$pages = paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'type'  => 'array',
        'prev_next' => true,
		'prev_text' => __('Previous'),
		'next_text' => __('Next'),
    ) );
    if( is_array( $pages ) ) {
        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
        echo '<nav class="pagination-artikel" aria-label="Page navigation example">
        <ul class="pagination">';
        foreach ( $pages as $page ) {
                echo '<li class="page-item artikel v1">'.$page.'</li>';
        }
       echo ' </ul>
            </nav>';
        }
}


function ps_selectbox() {
    $per_page = filter_input(INPUT_GET, 'perpage', FILTER_SANITIZE_NUMBER_INT);     
    echo '<div class="dropdown bootstrap-select bs-select"> <span style="margin-bottom : 10px;">Product per page :</span>
				<select id="sorting"  onchange="if (this.value) window.location.href=this.value" class="bs-select" tabindex="-98">';
    $orderby_options = array(
        '4' => '4',
        '8' => '8',
        '12' => '12',
        '16' => '16'
    );
    echo '<option value="">Select Count Products</option>';
foreach( $orderby_options as $value => $label ) {
    echo "<option ".selected( $per_page, $value )." value='?perpage=$value'>$label</option>";
}
echo '</select>
          </div>';
}


add_action( 'pre_get_posts', 'ps_pre_get_products_query' );
function ps_pre_get_products_query( $query ) {
    $per_page = filter_input(INPUT_GET, 'perpage', FILTER_SANITIZE_NUMBER_INT);
    if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'product' ) ) {
        $query->set( 'posts_per_page', $per_page );
    }
}

add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'left-sidebar', 'theme-slug' ),
        'id' => 'left-sidebar',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'theme-slug' ),
        'before_widget' => '<li style="margin-top : 20px;" id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h6 class="text-uppercase">',
	'after_title'   => '</h6>',
    ) );
}

// Customize mce editor font sizes
if ( ! function_exists( 'wpex_mce_text_sizes' ) ) {
	function wpex_mce_text_sizes( $initArray ){
		$initArray['fontsize_formats'] = "8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 26pt 27pt 28pt 29pt 30pt 31pt 32pt 33pt 34pt 35pt 36pt 37pt 38pt 39pt 40pt";
		return $initArray;
	}
}
add_filter( 'tiny_mce_before_init', 'wpex_mce_text_sizes' );




// add eksonindo

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Theme Header Settings',
		'menu_title'	=> 'Header',
		'menu_slug' 	=> 'theme-header-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));	
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'menu_slug' 	=> 'theme-footer-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	
}

// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Projects', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Project', 'text_domain' ),
		'name_admin_bar'        => __( 'Project', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'labels'                => $labels,
		'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 4,
        'supports'           => array( 'title' ),
        'taxonomies'          => array( 'category' ),
	);
	register_post_type( 'projects', $args );

}
add_action( 'init', 'custom_post_type', 0 );



/* newinterlook.demo-wit.id theme functions */





/* newinterlook.demo-wit.id theme functions */

/* New_theme Theme Template Loader */


