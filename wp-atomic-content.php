<?php
/*
Plugin Name: WP atomic content
Description: Add possibility to create content blocks to add in posts, pages, ...
Version: 1.0.0
Author: David Duquenoy
*/


// The custom post type
function registerAtomicContentCpt() {

	$labels = array(
		'name'                  => _x( 'Blocks', 'Post Type General Name', 'atomic-content' ),
		'singular_name'         => _x( 'Block', 'Post Type Singular Name', 'atomic-content' ),
		'menu_name'             => __( 'Blocks', 'atomic-content' ),
		'name_admin_bar'        => __( 'Post Type', 'atomic-content' ),
		'archives'              => __( 'Item Archives', 'atomic-content' ),
		'parent_item_colon'     => __( 'Parent Item:', 'atomic-content' ),
		'all_items'             => __( 'All Items', 'atomic-content' ),
		'add_new_item'          => __( 'Add New Item', 'atomic-content' ),
		'add_new'               => __( 'Add New', 'atomic-content' ),
		'new_item'              => __( 'New Item', 'atomic-content' ),
		'edit_item'             => __( 'Edit Item', 'atomic-content' ),
		'update_item'           => __( 'Update Item', 'atomic-content' ),
		'view_item'             => __( 'View Item', 'atomic-content' ),
		'search_items'          => __( 'Search Item', 'atomic-content' ),
		'not_found'             => __( 'Not found', 'atomic-content' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'atomic-content' ),
		'featured_image'        => __( 'Featured Image', 'atomic-content' ),
		'set_featured_image'    => __( 'Set featured image', 'atomic-content' ),
		'remove_featured_image' => __( 'Remove featured image', 'atomic-content' ),
		'use_featured_image'    => __( 'Use as featured image', 'atomic-content' ),
		'insert_into_item'      => __( 'Insert into item', 'atomic-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'atomic-content' ),
		'items_list'            => __( 'Items list', 'atomic-content' ),
		'items_list_navigation' => __( 'Items list navigation', 'atomic-content' ),
		'filter_items_list'     => __( 'Filter items list', 'atomic-content' ),
	);
	$args = array(
		'label'                 => __( 'Block', 'atomic-content' ),
		'description'           => __( 'Add possibility to create content blocks to add in posts, pages, ...', 'atomic-content' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'author', 'revisions', 'custom-fields', ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-exerpt-view',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'atomic_block', $args );

}
add_action( 'init', 'registerAtomicContentCpt', 0 );

// The shortcode function
function atomicContent($id, $classes = array(), $spaceBefore = null, $spaceAfter = null, $display = true) {
	$criterias = array(
		"post_type" => "atomic_block",
		"name" => $id
	);
	$query = new WP_Query($criterias);
	if (!$query->have_posts()) return ""; // display nothing if the block is not found
	while ($query->have_posts()) {
		$query->the_post();
		$post = array(
			"id" => $query->post->ID,
			//"permalink" => get_permalink($query->post->ID),
			"title" => get_the_title(),
			"content" => get_the_content()
		);
	}
	wp_reset_postdata();

	array_unshift($classes, "atomic-content-block");
	$onlineStyle = array();
	if ($spaceBefore !== null) $onlineStyle[] = "margin-top: $spaceBefore;";
	if ($spaceAfter !== null) $onlineStyle[] = "margin-bottom: $spaceAfter;";

	$result = "<div"
		. (count($classes) ? " class=\"" . implode(" ", $classes) . "\"" : "")
		. (count($onlineStyle) ? " style=\"" . implode("", $onlineStyle) . "\"" : "") 
		. ">";
	//$result .= do_shortcode($post["content"]);
	$result .= apply_filters("the_content", $post["content"]);
	$result .= "</div>";

	if ($display)
		echo $result;
	else
		return $result;
}

// The shortcode
function atomicContentShortcode($attributes, $content) {
	extract(shortcode_atts(array(
		"id" => null,
		"class" => null,
		"spacebefore" => null,
		"spaceafter" => null
	), $attributes));
	$classes = array();
	if ($class) $classes = explode(" ", $class);

	return atomicContent($id, $classes, $spacebefore, $spaceafter, false);
}
add_shortcode("block", "atomicContentShortcode");


// Add the shortcode preview in a blocks list column
function atomicContentAddColumns($columns) {
	$columns["shortcode"] = __("Shortcode", "atomic-content");
	return $columns;
}
add_filter( 'manage_edit-atomic_block_columns', 'atomicContentAddColumns' ) ;

add_action( 'manage_atomic_block_posts_custom_column', 'atomicContentManageColumns', 10, 2 );

function atomicContentManageColumns( $column, $post_id ) {

	switch( $column ) {
		case 'shortcode' :
			echo "[block id=\"" . get_post_field("post_name", $post_id) . "\"]";
			break;
		default :
			break;
	}
}

// Add a meta box to display the shortcode
function atomicContentAddMetaBox() {
	add_meta_box("atomicContentMetaBox", "Shortcode", "atomicContentMetaBoxContent", "atomic_block", "side", "high");
}
add_action("add_meta_boxes","atomicContentAddMetaBox");

function atomicContentMetaBoxContent($post) {
	echo "<p>" . __("Shortcode to include in pages, posts and other post types:", "atomic-content") . "</p>";
	echo "[block id=\"" . $post->post_name . "\"]";
}



?>