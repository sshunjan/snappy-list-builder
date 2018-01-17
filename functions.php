<?php
function cptui_register_my_cpts() {

	/**
	 * Post Type: Subscribers.
	 */

	$labels = array(
		"name" => __( "Subscribers", "twentyseventeen" ),
		"singular_name" => __( "Subscriber", "twentyseventeen" ),
	);

	$args = array(
		"label" => __( "Subscribers", "twentyseventeen" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slb_subscriber", "with_front" => false ),
		"query_var" => true,
		"supports" => false,
	);

	register_post_type( "slb_subscriber", $args );

	/**
	 * Post Type: Lists.
	 */

	$labels = array(
		"name" => __( "Lists", "twentyseventeen" ),
		"singular_name" => __( "List", "twentyseventeen" ),
	);

	$args = array(
		"label" => __( "Lists", "twentyseventeen" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slb_list", "with_front" => false ),
		"query_var" => true,
		"supports" => array( "title" ),
	);

	register_post_type( "slb_list", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
?>
