<?php

// Fire our meta box setup function on the post editor screen.
add_action( 'load-post.php', 'tinyevents_meta_boxes_setup' );
add_action( 'load-post-new.php', 'tinyevents_meta_boxes_setup' );

// Meta box setup function.
function tinyevents_meta_boxes_setup() {

  // Add meta boxes on the 'add_meta_boxes' hook.
  add_action( 'add_meta_boxes', 'tinyevents_add_meta_boxes' );

  // Save post meta on the 'save_post' hook.
  add_action( 'save_post', 'tinyevents_save_meta', 10, 2 );
}


// Create one or more meta boxes to be displayed on the post editor screen.
function tinyevents_add_meta_boxes() {

  add_meta_box(
    'tinyevent-dates',      // Unique ID
    esc_html__( 'Event Details', 'tinyevents' ),    // Title
    'tinyevents_metabox',   // Callback function
    'tinyevent',         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
}


// Display the post meta box.
function tinyevents_metabox( $post ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'tinyevents_dates_nonce' ); ?>

  <div><small>Date formats must be in YYYY-MM-DD format.</small></div>

  <p class="tinyevents-startdate">
    <label for="tinyevent-startdate">
    	<strong><?php _e( "Start Date", 'tinyevents' ); ?></strong>
    </label><br>
    <input class="datepicker" type="text" name="tinyevent-startdate" id="tinyevent-startdate" value="<?php echo esc_attr( get_post_meta( $post->ID, '_tinyevents_startdate', true ) ); ?>" size="15" /><br>
    <small><?php _e("You should pick a start date, or your event won't display.") ?></small>
  </p>
  <p class="tinyevents-enddate">
    <label for="tinyevent-enddate">
    	<?php _e( "End Date", 'tinyevents' ); ?>
    </label><br>
    <input class="datepicker" type="text" name="tinyevent-enddate" id="tinyevent-enddate" value="<?php echo esc_attr( get_post_meta( $post->ID, '_tinyevents_enddate', true ) ); ?>" size="15" /><br>
    <small><?php _e('Optional.  Leave blank for one-day events.', 'tinyevents')?></small>
  </p>
  <p class="tinyevents-times">
    <label for="tinyevent-times">
    	<?php _e( "Times", 'tinyevents' ); ?>
    </label><br>
    <input class="widefat" type="text" name="tinyevent-times" id="tinyevent-times" value="<?php echo esc_attr( get_post_meta( $post->ID, '_tinyevents_times', true ) ); ?>" size="30" /><br>
    <small><?php _e('Write anything you need to here, eg. &ldquo;2-4pm&rdquo;, or &ldquo;7pm for a 7:30pm start&rdquo;.', 'tinyevents')?></small>
  </p>
  <p class="tinyevents-venuename">
    <label for="tinyevent-venuename">
      <?php _e( "Venue Name", 'tinyevents' ); ?>
    </label><br>
    <input class="widefat" type="text" name="tinyevent-venuename" id="tinyevent-venuename" value="<?php echo esc_attr( get_post_meta( $post->ID, '_tinyevents_venuename', true ) ); ?>" size="30" />
  </p>
  <p class="tinyevents-address">
    <label for="tinyevent-address">
      <?php _e( "Venue Address", 'tinyevents' ); ?>
    </label><br>
    <input class="widefat" type="text" name="tinyevent-address" id="tinyevent-address" value="<?php echo esc_attr( get_post_meta( $post->ID, '_tinyevents_address', true ) ); ?>" size="30" /><br>
    <small><?php _e("Write the full address here, for the most accurate map.", 'tinyevent')?></small>
  </p>
  <p class="tinyevents-showmap">
    <?php
      $showmap = get_post_meta( $post->ID, '_tinyevents_showmap', true );
      // Tick the checkbox by default if this is a new post, or tick it if it's been set as ticked.
      $checked = ( $showmap == 1 || strpos($_SERVER['REQUEST_URI'], 'post-new.php')) ? ' checked="checked"' : '';
    ?>
    <input type="checkbox" name="tinyevent-showmap" id="tinyevent-showmap" value="1"<?php echo $checked; ?>>
    <label for="tinyevent-showmap">
      <?php _e( "Show Map for Address", 'tinyevents' ); ?>
    </label>
  </p>
<?php }


// Save the meta box's post metadata.
function tinyevents_save_meta( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( !isset( $_POST['tinyevents_dates_nonce'] ) || !wp_verify_nonce( $_POST['tinyevents_dates_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );

	// Check if the current user has permission to edit the post.
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;


	$new_startdate = ( isset( $_POST['tinyevent-startdate'] ) ? sanitize_text_field( $_POST['tinyevent-startdate'] ) : '' );

	$startdate = get_post_meta( $post_id, '_tinyevents_startdate', true );

	if ( $new_startdate && '' == $startdate )
		add_post_meta( $post_id, '_tinyevents_startdate', $new_startdate, true );

	elseif ( $new_startdate && $new_startdate != $startdate )
		update_post_meta( $post_id, '_tinyevents_startdate', $new_startdate );

	elseif ( '' == $new_startdate && $startdate )
		delete_post_meta( $post_id, '_tinyevents_startdate', $startdate );


	$new_enddate = ( isset( $_POST['tinyevent-enddate'] ) ? sanitize_text_field( $_POST['tinyevent-enddate'] ) : '' );

	$enddate = get_post_meta( $post_id, '_tinyevents_enddate', true );

	if ( $new_enddate && '' == $enddate )
		add_post_meta( $post_id, '_tinyevents_enddate', $new_enddate, true );

	elseif ( $new_enddate && $new_enddate != $enddate )
		update_post_meta( $post_id, '_tinyevents_enddate', $new_enddate );

	elseif ( '' == $new_enddate && $enddate )
		delete_post_meta( $post_id, '_tinyevents_enddate', $enddate );


	$new_times = ( isset( $_POST['tinyevent-times'] ) ? sanitize_text_field( $_POST['tinyevent-times'] ) : '' );

	$times = get_post_meta( $post_id, '_tinyevents_times', true );

	if ( $new_times && '' == $times )
		add_post_meta( $post_id, '_tinyevents_times', $new_times, true );

	elseif ( $new_times && $new_times != $times )
		update_post_meta( $post_id, '_tinyevents_times', $new_times );

	elseif ( '' == $new_times && $times )
		delete_post_meta( $post_id, '_tinyevents_times', $times );


  $new_venuename = ( isset( $_POST['tinyevent-venuename'] ) ? sanitize_text_field( $_POST['tinyevent-venuename'] ) : '' );

  $venuename = get_post_meta( $post_id, '_tinyevents_venuename', true );

  if ( $new_venuename && '' == $venuename )
    add_post_meta( $post_id, '_tinyevents_venuename', $new_venuename, true );

  elseif ( $new_venuename && $new_venuename != $venuename )
    update_post_meta( $post_id, '_tinyevents_venuename', $new_venuename );

  elseif ( '' == $new_venuename && $venuename )
    delete_post_meta( $post_id, '_tinyevents_venuename', $venuename );


  $new_address = ( isset( $_POST['tinyevent-address'] ) ? sanitize_text_field( $_POST['tinyevent-address'] ) : '' );

  $address = get_post_meta( $post_id, '_tinyevents_address', true );

  if ( $new_address && '' == $address )
    add_post_meta( $post_id, '_tinyevents_address', $new_address, true );

  elseif ( $new_address && $new_address != $address )
    update_post_meta( $post_id, '_tinyevents_address', $new_address );

  elseif ( '' == $new_address && $address )
    delete_post_meta( $post_id, '_tinyevents_address', $address );



  $new_showmap = ( isset( $_POST['tinyevent-showmap'] ) ? filter_var( $_POST['tinyevent-showmap'], FILTER_SANITIZE_NUMBER_INT ) : '' );

  $showmap = get_post_meta( $post_id, '_tinyevents_showmap', true );

  if ( $new_showmap && '' == $showmap )
    add_post_meta( $post_id, '_tinyevents_showmap', $new_showmap, true );

  elseif ( $new_showmap && $new_showmap != $showmap )
    update_post_meta( $post_id, '_tinyevents_showmap', $new_showmap );

  elseif ( '' == $new_showmap && $showmap )
    delete_post_meta( $post_id, '_tinyevents_showmap', $showmap );
}