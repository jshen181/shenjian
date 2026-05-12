<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// TAB
function start_tab_shortcode( $atts, $content = '' ) {
	return '<div class="tab-single-wrap">';
}

function tabs_shortcode( $atts, $content = '' ) {
	global $tab_count, $tab_nav, $tab_content;
	static $tab_count = 1;
	if ( ! isset( $tab_nav ) ) {
		$tab_nav = '<div class="tab-single-menu">';
	}

	$active_class = ( $tab_count == 1 ) ? ' active' : '';
	$tab_nav     .= '<div class="tab-single-menu-item"><a href="#tab' . $tab_count . '" class="tab-single-btn' . $active_class . '">' . $atts['title'] . '</a></div>';
	$tab_content .= '<div id="tab' . $tab_count++ . '" class="tab-single-item' . $active_class . '">' . do_shortcode( $content ) . '</div>';
	return '';
}

function end_tab_shortcode( $atts, $content = '' ) {
	global $tab_nav, $tab_content;
	$out         = '';
	$out        .= $tab_nav . '</div><div class="clear"></div>';
	$out        .= '<div class="tab-single-content">';
	$out        .= $tab_content;
	$out        .= '</div>';
	$tab_nav     = '';
	$tab_content = '';
	$tab_nav     = '<div class="tab-single-menu">';
	$tab_content = '';
	return $out . '</div>';
}