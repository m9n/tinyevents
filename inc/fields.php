<?php

// Fire our meta box setup function on the post editor screen.
add_action( 'load-post.php', 'tinyevent_meta_boxes_setup' );
add_action( 'load-post-new.php', 'tinyevent_meta_boxes_setup' );

// Meta box setup function.
function tinyevent_meta_boxes_setup() {

  // Add meta boxes on the 'add_meta_boxes' hook.
  add_action( 'add_meta_boxes', 'tinyevent_add_meta_boxes' );

  // Save post meta on the 'save_post' hook.
  add_action( 'save_post', 'tinyevent_save_meta', 10, 2 );
}


// Create one or more meta boxes to be displayed on the post editor screen.
function tinyevent_add_meta_boxes() {

  add_meta_box(
    'tinyevent-dates',      // Unique ID
    esc_html__( 'Event Dates/Times', 'tinyevent' ),    // Title
    'tinyevent_dates_meta_box',   // Callback function
    'tinyevent',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}


// Display the post meta box.
function tinyevent_dates_meta_box( $post ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'tinyevent_dates_nonce' ); ?>

  <small>Date formats must be in YYYY-MM-DD format.</small>

  <p>
    <label for="tinyevent-startdate">
    	<?php _e( "Start Date", 'tinyevent' ); ?>
    </label><br>
    <input class="datepicker" type="text" name="tinyevent-startdate" id="tinyevent-startdate" value="<?php echo esc_attr( get_post_meta( $post->ID, 'tinyevent_startdate', true ) ); ?>" size="15" />
  </p>
  <p>
    <label for="tinyevent-enddate">
    	<?php _e( "End Date", 'tinyevent' ); ?>
    </label><br>
    <input class="datepicker" type="text" name="tinyevent-enddate" id="tinyevent-enddate" value="<?php echo esc_attr( get_post_meta( $post->ID, 'tinyevent_enddate', true ) ); ?>" size="15" />
  </p>
  <p>
    <label for="tinyevent-times">
    	<?php _e( "Times", 'tinyevent' ); ?>
    </label><br>
    <input class="widefat" type="text" name="tinyevent-times" id="tinyevent-times" value="<?php echo esc_attr( get_post_meta( $post->ID, 'tinyevent_times', true ) ); ?>" size="30" /><br>
    <small>Write anything you need to here, eg. &ldquo;2-4pm&rdquo;, or &ldquo;7pm for a 7:30pm start&rdquo;.</small>
  </p>
<?php }


// Save the meta box's post metadata.
function tinyevent_save_meta( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( !isset( $_POST['tinyevent_dates_nonce'] ) || !wp_verify_nonce( $_POST['tinyevent_dates_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );

	// Check if the current user has permission to edit the post.
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;


	$new_startdate = ( isset( $_POST['tinyevent-startdate'] ) ? sanitize_text_field( $_POST['tinyevent-startdate'] ) : '' );

	$startdate = get_post_meta( $post_id, 'tinyevent_startdate', true );

	if ( $new_startdate && '' == $startdate )
		add_post_meta( $post_id, 'tinyevent_startdate', $new_startdate, true );

	elseif ( $new_startdate && $new_startdate != $startdate )
		update_post_meta( $post_id, 'tinyevent_startdate', $new_startdate );

	elseif ( '' == $new_startdate && $startdate )
		delete_post_meta( $post_id, 'tinyevent_startdate', $startdate );


	$new_enddate = ( isset( $_POST['tinyevent-enddate'] ) ? sanitize_text_field( $_POST['tinyevent-enddate'] ) : '' );

	$enddate = get_post_meta( $post_id, 'tinyevent_enddate', true );

	if ( $new_enddate && '' == $enddate )
		add_post_meta( $post_id, 'tinyevent_enddate', $new_enddate, true );

	elseif ( $new_enddate && $new_enddate != $enddate )
		update_post_meta( $post_id, 'tinyevent_enddate', $new_enddate );

	elseif ( '' == $new_enddate && $enddate )
		delete_post_meta( $post_id, 'tinyevent_enddate', $enddate );


	$new_times = ( isset( $_POST['tinyevent-times'] ) ? sanitize_text_field( $_POST['tinyevent-times'] ) : '' );

	$times = get_post_meta( $post_id, 'tinyevent_times', true );

	if ( $new_times && '' == $times )
		add_post_meta( $post_id, 'tinyevent_times', $new_times, true );

	elseif ( $new_times && $new_times != $times )
		update_post_meta( $post_id, 'tinyevent_times', $new_times );

	elseif ( '' == $new_times && $times )
		delete_post_meta( $post_id, 'tinyevent_times', $times );
}