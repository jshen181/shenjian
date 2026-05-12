<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 文字折叠
function show_more( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title' => '',
			),
			$atts
		)
	);
	if ( strtolower( $title ) === '' ) {
		$title = '<span class="show-more-tip"><span class="tip-k fd">' . sprintf( __( '展开', 'begin' ) ) . '</span></span><span class="show-more-tip"><span class="tip-s fd">' . sprintf( __( '收缩', 'begin' ) ) . '</span></span>';
	} else {
		$title = $title;
	}
	$html  = '<div class="show-more more-c sup' . cur() . '">';
	$html .= $title;
	$html .= '</div>';
	return $html;
}

function section_content( $atts, $content = null ) {
	$clean = remove_shortcode_markup( $content );
	return '<div class="section-content show-area">' . do_shortcode( $clean ) . '</p></div><p>';
}