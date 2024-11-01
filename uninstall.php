<?php

// Check that file was called from WordPress admin
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wp_storymap_pro_drop_table( $prefix ) {
	global $wpdb;
	$wpdb->query( 'DROP TABLE ' . $prefix . 'storymap_pro_' );
}

// Get access to global WordPress wpdb class
global $wpdb;

$table_points = $wpdb->prefix . 'storymap_pro_points';
$wpdb->query( "DROP TABLE IF EXISTS $table_points" );

$table_stories = $wpdb->prefix . 'storymap_pro_my_stories';
$wpdb->query( "DROP TABLE IF EXISTS $table_stories" );
