<?php
/**
 * Plugin Name: Contributors
 * Plugin URI: http://vaibhavpatel.in/
 * Description: We can display List of authors name on the post.
 * Version: 1.1.1
 * Author: Vaibhav Patel
 * Author URI: http://vaibhavpatel.in/
 * Text Domain: http://vaibhavpatel.in/
 * License: GPL2
 */

/*
 * Copyright 2014 - 2015 Vaibhav Patel (email : patelvaibhavr@yahoo.com)
 */
defined ( 'ABSPATH' ) or die ( "No script kiddies please!" );

add_action ( 'admin_menu', 'add_contributors' );
function add_contributors() {
	global $post;
	add_action ( 'add_meta_boxes', 'add_contributors_meta_box_add' );
	add_action ( 'save_post', 'add_contributors_meta_box_save' );
}

add_action ( 'wp_enqueue_scripts', 'contributors_script' );
function contributors_script() {	
	wp_enqueue_style ( 'contributors_style', plugins_url ( 'lib/css/style.css', __FILE__ ), array (), null, 'all' );
}


// Add Option in Setting Menu
function contributors_menu() {
	echo "<div class='warp'><h1>Contributors Settings</h1><hr/></div>";
}

// Add Metabox of Contributor's List
function add_contributors_meta_box_add() {
	add_meta_box ( 'add_contributors_meta_box', 'List of Contributors', 'add_contributors_meta_box_content', 'post', 'normal', 'high' );
}

// Contents of Contributor's List Metabox
function add_contributors_meta_box_content() {
	global $post;
	
	$contributors1 = get_post_meta ( $post->ID, 'contributors' );
	if ($contributors1 [0] != "") {
		$contributors = explode ( ',', $contributors1 [0] );
	}
	
	$login_user_id = get_current_user_id ();
	$blogusers = get_users ();
	
	foreach ( $blogusers as $user ) {
		
		$flag = 0;
		if (!empty($contributors)) {
			if (in_array ( $user->ID, $contributors )) {
				$flag = 1;
			}
		} else {
			if ($user->ID == $login_user_id) {
				$flag = 1;
			}
		}
		
		echo "<input type='checkbox' name='ch" . esc_html ( $user->ID ) . "' value='" . esc_html ( $user->ID ) . "' title='" . esc_html ( $user->display_name ) . "'";
		if ($flag == 1) {
			echo " checked ";
		}
		echo "/>" . esc_html ( $user->display_name ) . "<br/>";
	}
}

// Save Post with meta box
function add_contributors_meta_box_save($post_id) {
	$blogusers = get_users ();
	$contributors = "";
	
	foreach ( $blogusers as $user ) {
		if (isset ( $_POST ['ch' . esc_html ( $user->ID )] )) {
			$contributors .= $_POST ['ch' . esc_html ( $user->ID )] . ',';
		}
	}
	update_post_meta ( $post_id, 'contributors', $contributors );
}

// Add list of selected contributors at the end of post
add_filter ( 'the_content', 'add_after_post_content' );

// Add list of selected contributors at the end of post
function add_after_post_content($content) {
	global $post;
	
	$contributors = get_post_meta ( $post->ID, 'contributors' );
	$contributors = explode ( ',', $contributors [0] );
	
	$blogusers = get_users ();
	
	$content .= "<br/><div class='display-contributors'> <label><strong> List Of Contributors : </strong></label>";
	
	foreach ( $blogusers as $user ) {
		$flag = 0;
		
		if (in_array ( $user->ID, $contributors )) {
			$flag = 1;
		}
		
		if ($flag == 1) {
			$content .= "<span class='author vcard display-contributor'><a class='url fn n'  href='" . get_author_posts_url ( $user->ID ) . "' title='View all posts by " . esc_html ( $user->display_name ) . "' > " . get_avatar ( $user->ID, 32 ) . " " . esc_html ( $user->display_name ) . "</a></span> ";
		}
	}
	
	$content .= "</div>";
	
	return $content;
}
?>