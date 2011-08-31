<?php
/*
Plugin Name: Easy Repeatable Input Fields
Plugin URI: http://theportraitofageek.com/blog/?p=967
Description: Repeatable and sortable input fields for WordPress
Version: 1.0
Author: The CSSigniter Team
Author URI: http://www.cssigniter.com/


Copyright 2010  The CSSigniter Team (email : info@cssigniter.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if (!defined('erif_version'))
	define('erif_version', '1.0');

if (!defined('erif_field'))
	define('erif_field', 'series');

// the scripts 
add_action('admin_menu', 'erif_scripts');
function erif_scripts() {
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');		
	wp_enqueue_script('mf-scripts', plugin_dir_url( __FILE__ ) . 'easy-repeatable-input-fields.js', array( 'jquery' ) );
}

// the styles
add_action('init', 'erif_styles');
function erif_styles() {
	wp_enqueue_style('erif-css', plugin_dir_url( __FILE__ ) . 'easy-repeatable-input-fields.css', true, erif_version , 'all' );
}

add_action('admin_menu', 'erif_box');
function erif_box() {
	$erif_post_types = _erif_get_post_types();
	foreach ($erif_post_types as $key=>$value)
	{
		add_meta_box( 'mf-meta-boxes', 'Easy Repeatable Input Fields', 'erif_fields', $key, 'normal','high' );
	}
}

function _erif_get_post_types()
{
	// Get the post types available
	$types = array();
	$types = get_post_types($args = array(
		'public'   => true
	), 'objects');

	unset($types['attachment']);
	return $types;
}

function erif_fields() {
	global $post_ID;
	
	echo '<a href="#" id="mf-add-field">Add Field</a>';
	$erif_fields = get_post_meta($post_ID, erif_field, true);
	if (!empty($erif_fields)) {
		foreach ($erif_fields as $key=>$value):
			echo '<p class="mf-field"><input type="text" name="series[]" value="'. $value .'" /> <a href="#" class="mf-remove">Remove me</a></p>';
		endforeach;
	}
	else {
		echo '<p class="mf-field"><input type="text" name="series[]" /> <a href="#" class="mf-remove">Remove me</a></p>';
	}

	echo '<input type="hidden" name="erif_noncename" id="erif_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
}

// shortcode [erif]
add_shortcode('erif', 'erif_short');
function erif_short() {
	global $post;
	$series = get_post_meta($post->ID, erif_field, true);
	$erif_list = "<ul id='erif-list' class='erif-list'>";
	foreach($series as $item):
	  $erif_list .= "<li>" .  $item . "</li>";
	endforeach;
	$erif_list .= "</ul>";
	return $erif_list;
}

// save values
add_action('save_post', 'erif_save');
function erif_save() {	
	global $post_ID;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if (!isset($_POST['erif_noncename'])) return;
	if (!wp_verify_nonce( $_POST['erif_noncename'], plugin_basename(__FILE__))) return;
	if (!current_user_can( 'edit_post', $post_ID ) ) return;
	
	$value = $_POST['series'];
	$id = $_POST['post_ID'];
	update_post_meta($id, erif_field, $value);
}
?>