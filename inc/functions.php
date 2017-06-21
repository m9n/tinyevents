<?php

if ( !function_exists('tinyevents_single_content') ) :
function tinyevents_single_content($content) {

    if ( is_singular( 'tinyevent' ) && is_main_query() ) {

        global $post;

        $meta = tinyevents_get_event_meta($post->ID);
		$dates = tinyevents_format_dates( $meta['starts'], $meta['ends'], 'page' );
		$map = ( empty($meta['address']) ) ? '' : tinyevents_gmap($meta['address']) ;

		$output = '';
		if ( !empty( $dates ) ) {
			$output .= sprintf('<h2 class="tinyevents tinyevents-single date">%s</h2>', $dates);
		}
		if ( !empty( $meta['times'] ) ) {
			$output .= sprintf('<h3 class="tinyevents tinyevents-single time">%s</h3>', $meta['times']);
		}
		$output .= $content;
		if ( !empty($meta['venuename']) || !empty($meta['address']) ) {
			$output .= '<div class="tinyevents tinyevents-single venue">';
			$output .= '<h3>Location</h3>';
			$output .= '<p class="tinyevents tinyevents-single venue-details">';
			$output .= ( !empty($meta['venuename']) ) ? '<strong>'.$meta['venuename'].'</strong>' : '' ;
			$output .= ( !empty($meta['venuename']) && !empty($meta['address']) ) ? ' <br> ' : '' ;
			if ( !empty($meta['address'] ) ) {
				$address_parts = explode(',', $meta['address']);
				$address_parts = array_filter(array_map('trim', $address_parts));
				if ( $address_parts[0] == $meta['venuename'] ) array_shift($address_parts);
				$output .= implode(',<br>', $address_parts);
			}
			$output .= '</p>';
			if ( !empty($meta['address']) || $meta['showmap'] == 1 ) {
				$output .= tinyevents_gmap( urlencode( $meta['address'] ) );
			}
			$output .= '</div>';
		}

		return $output;

    }

    else return $content;
}
add_filter( 'the_content', 'tinyevents_single_content' );
endif;


if ( !function_exists('tinyevents_event_query') ) :
function tinyevents_event_query($format, $limit=10, $paginate=1) {

	$output = '';

	$today = date('Y-m-d');

	$format = sanitize_html_class($format);
	if ( !is_numeric($limit) )
		$limit = 10; // If $limit isn't a number, set it back to 10.
	if ( $paginate != 0 && $paginate != 1 )
		$paginate = 1; // If $paginate isn't 0 or 1, set it to 1.

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' => 'tinyevent',
		'post_status' => 'publish',
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key'     => '_tinyevents_startdate',
				'value'   => $today,
				'type'    => 'DATE',
				'compare' => '>=',
			),
			array(
				'key'     => '_tinyevents_enddate',
				'value'   => $today,
				'type'    => 'DATE',
				'compare' => '>=',
			),
		),
		'orderby'  => array( 'meta_value' => 'ASC', 'title' => 'ASC' ),
		'meta_key' => '_tinyevents_startdate',
		'posts_per_page' => $limit,
		'paged' => $paged
	);

	$tinyevents_query = new WP_Query( $args );

	if ( $tinyevents_query->have_posts() ) {
		$output .= '<ul class="tinyevents tinyevents-list ' . $format . '">';
		while ( $tinyevents_query->have_posts() ) {
			$tinyevents_query->the_post();
			$meta = tinyevents_get_event_meta( get_the_ID() );
			$excerpt = tinyevents_excerpt( get_the_excerpt() );
			$event = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'permalink' => get_permalink(),
				'starts' => $meta['starts'],
				'ends' => $meta['ends'],
				'thumbnail' => get_the_post_thumbnail(get_the_ID(), 'thumbnail'),
				'excerpt' => $excerpt
			);
			$output .= tinyevents_event_list_item($event, $format);
		}
		$output .= '</ul>';

		$big = 999999999; // need an unlikely integer  			
		$pagination =  paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $tinyevents_query->max_num_pages,
		) ); 
        
		if ( !empty($pagination) && $paginate == 1 ) {
			$output .= sprintf('<nav class="tinyevents pagination">%s</nav>', $pagination);
		}
	}

	wp_reset_postdata();
	return $output;

}
endif;


if ( !function_exists('tinyevents_event_list_item') ) :
function tinyevents_event_list_item($event, $format) {

	$output = '';

	$output .= sprintf( '<li><a href="%s">', $event['permalink'] );
	//$output .= $event['thumbnail'];
	$output .= ( $format == 'page' ) ?
		tinyevents_format_dates( $event['starts'], $event['ends'], 'page' ) :
		tinyevents_format_dates( $event['starts'], $event['ends'], 'widget' ) ;
	$output .= sprintf( '<h3>%s</h3>', $event['title'] );
	$output .= ( $format == 'page') ? '<p class="excerpt">'.$event['excerpt'].'</p>' : '';
	$output .= '</a></li>';

	return $output;

}
endif;


if ( !function_exists('tinyevents_format_date') ) :
function tinyevents_format_dates($startdate, $enddate, $format='widget') {

	$order        = ( get_bloginfo("language") == 'en-US' ) ? 'MDY' : 'DMY';
	$format_date  = 'j';
	$format_month = ( $format == 'page' ) ? 'F' : 'M' ;
	$format_year  = 'Y';

	$starts      = strtotime($startdate);
	$start_date  = date($format_date, $starts);
 	$start_month = date($format_month, $starts);
 	$start_year  = date($format_year, $starts);

 	if ( !empty($enddate) ) {
		$ends      = strtotime($enddate);
		$end_date  = date($format_date, $ends);
	 	$end_month = date($format_month, $ends);
	 	$end_year  = date($format_year, $ends);
 		
 	}

 	$this_year = date($format_year);

 	if ( !empty($ends) ) {
 		if ($start_date == $end_date && $start_month == $end_month && $start_year == $end_year) {
 			if ( $start_year == $this_year && $end_year == $this_year)
 				$start_year = $end_year = '';
 			$format = ($order=='DMY') ?
	 			'<div class="date">%1$s %2$s %3$s</div>' :
	 			'<div class="date">%2$s %1$s %3$s</div>' ;
	 		$output = sprintf($format, $start_date, $start_month, $start_year);
 		}
 		elseif ($start_month == $end_month && $start_year == $end_year) {
 			if ( $start_year == $this_year && $end_year == $this_year)
 				$start_year = $end_year = '';
 			$format = ($order=='DMY') ?
	 			'<div class="date">%1$s&ndash;%2$s %3$s %4$s</div>' :
	 			'<div class="date">%3$s %1$s&ndash;%2$s %4$s</div>' ;
	 		$output = sprintf($format, $start_date, $end_date, $start_month, $start_year);
 		}
 		elseif ($start_year == $end_year) {
 			if ( $start_year == $this_year && $end_year == $this_year)
 				$start_year = $end_year = '';
 			$format = ($order=='DMY') ?
	 			'<div class="date">%1$s %2$s&ndash;%3$s %4$s %5$s</div>' :
	 			'<div class="date">%2$s %1$s&ndash;%4$s %3$s %5$s</div>' ;
	 		$output = sprintf($format, $start_date, $start_month, $end_date, $end_month, $start_year);
 		}
 		else {
 			if ( $start_year == $this_year && $end_year == $this_year)
 				$start_year = $end_year = '';
 			$format = ($order=='DMY') ?
	 			'<div class="date">%1$s %2$s %3$s&ndash;%4$s %5$s %6$s</div>' :
	 			'<div class="date">%2$s %1$s %3$s&ndash;%5$s %4$s %6$s</div>' ;
	 		$output = sprintf($format, $start_date, $start_month, $start_year, $end_date, $end_month, $end_year);
 		}
 	} else {
 		$start_year = ($start_year == $this_year) ? '' : $start_year;
 		$format = ($order=='DMY') ?
 			'<div class="date">%1$s %2$s %3$s</div>' :
 			'<div class="date">%2$s %1$s %3$s</div>' ;
 		$output = sprintf($format, $start_date, $start_month, $start_year);
 	}

 	return $output;


}
endif;

function tinyevents_excerpt($excerpt) {
	$excerpt_parts = explode('<a', $excerpt);
	$output = $excerpt_parts[0];
	return $output;
}

function tinyevents_get_event_meta($id) {
	$meta = array();
	$meta['starts']    = get_post_meta($id, '_tinyevents_startdate', TRUE);
	$meta['ends']      = get_post_meta($id, '_tinyevents_enddate', TRUE);
	$meta['times']     = get_post_meta($id, '_tinyevents_times', TRUE);
	$meta['venuename'] = get_post_meta($id, '_tinyevents_venuename', TRUE);
	$meta['address']   = get_post_meta($id, '_tinyevents_address', TRUE);
	$meta['showmap']   = get_post_meta($id, '_tinyevents_showmap', TRUE);
	return $meta;
}

if (!function_exists('tinyevents_gmap')) {
	function tinyevents_gmap($address) {
		return '<iframe class="tinyevents-gmap"'
					. 'frameborder="0" scrolling="no" marginheight="0" marginwidth="0"'
					. 'src="https://maps.google.com/maps?q='
					. $address
					. ', &t=&z=15&ie=UTF8&iwloc=&output=embed"></iframe>';
	}
} 