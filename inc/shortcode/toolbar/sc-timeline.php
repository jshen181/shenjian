<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
// 时间轴
function time_line( $atts, $content = null ) {
	global $wpdb, $post;
	$can   = get_post_meta( get_the_ID(), 'show_line', true );
	$clean = remove_shortcode_markup( $content );
	return '<div class="timeline ' . $can . '">' . do_shortcode( $clean ) . '</div>';
}