<?php

if ( !function_exists('tinyevents_shortcode')) :
function tinyevents_shortcode( $atts, $content = null ) {
  // Easy access to Font Awesome icons in your content.
  // May want to replace raw size attribute with pre-determined sizes applied with a class, to allow responsive CSS.
  $a = shortcode_atts( array(
        'format' => 'page', // 'widget', or 'page'
        'limit' => ''
    ), $atts );

  $limit = filter_var( $a['limit'], FILTER_SANITIZE_NUMBER_INT );
  $format = sanitize_html_class($a['format']);
  if ( empty( $limit ) ) $limit = ( $format == 'widget' ) ? 5 : 10;
  
  $tinyevents_event_query = tinyevents_event_query($format, $limit);

  return $tinyevents_event_query;

}
add_shortcode( 'events', 'tinyevents_shortcode' );
endif;