<?php

// Reference: https://codex.wordpress.org/Adding_Administration_Menus

add_action( 'admin_menu', 'tinyevents_menu' );
function tinyevents_menu() {
	add_options_page( 'Tiny Events Options', 'Tiny Events', 'manage_options', 'tinyevents-options', 'tinyevents_options' );
}

function tinyevents_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>

		<div class="wrap">
			<h1>Tiny Events Options</h1>
			<p>Coming soon!</p>
		</div>

	<?php
}