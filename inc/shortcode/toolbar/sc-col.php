<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * 分栏盒子
 * [colstart col="分栏数2/3/4"][colitem]这是第一个内容块[/colitem][colitem]这是第二个内容块[/colitem][colend]
 */
function be_start_col_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'col' => '2',
			),
			$atts
		)
	);

	global $colvalue;
	$colvalue = $col;

	$html  = '<div class="col-short-main">';
	$html .= do_shortcode( $content );
	return $html;
}

function be_item_shortcode( $atts, $content = null ) {
	global $colvalue;

	extract(
		shortcode_atts(
			array(
				'col' => $colvalue,
			),
			$atts
		)
	);

	$html  = '<div class="col-short-item col-short-' . $col . '">';
	$html .= do_shortcode( $content );
	$html .= '</div>';

	return $html;
}

function be_end_col_shortcode() {
	$html = '</div>';
	return $html;
}

add_shortcode( 'colstart', 'be_start_col_shortcode' );
add_shortcode( 'colitem', 'be_item_shortcode' );
add_shortcode( 'colend', 'be_end_col_shortcode' );
