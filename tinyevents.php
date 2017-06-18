<?php
/*
Plugin Name: Tiny Events
Plugin URI: https://github.com/m9n/tinyevents
Description: A minimal events plugin for WordPress. Does the basics with gusto. Packed with ideas for extending it "the WordPress way", to perfectly suit your needs.
Version: 0.1
Author: Mina Nielsen
Author URI: http://minanielsen.net
License: GPL V2 or higher
License URI: https://github.com/m9n/tinyevents/blob/master/LICENSE
*/

require 'inc/post-types.php';
require 'inc/fields.php';

function tinyevents_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tinyevents_activate' );

function tinyevents_enqueue_styles_and_scripts() {
    wp_enqueue_style( 'tinyevents', plugin_dir_url( __FILE__ ) . 'css/tinyevents.css' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'tinyevents-js', plugin_dir_url( __FILE__ ) . 'js/tinyevents.js' );
}
add_action( 'admin_enqueue_scripts', 'tinyevents_enqueue_styles_and_scripts' );
