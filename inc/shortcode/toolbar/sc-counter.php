<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 计数器
function be_counter( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'id'    => '1',
				'title' => '网站访问量',
				'value' => '123456',
				'n'     => all_view(),
				'speed' => '40000',
				'ico'   => 'be be-eye',
			),
			$atts
		)
	);

	if ( ! empty( $atts['ico'] ) ) {
		$c = 'be-counter-main-l';
	} else {
		$c = 'be-counter-main-c';
	}
	$html = '<div class="be-counter-main be_count_' . $id . '">';
	if ( ! empty( $atts['ico'] ) ) {
		$html .= '<div class="counters-icon">';
		$html .= '<i class="' . $ico . '"></i>';
		$html .= '</div>';
	}
	$html .= '<div class="counters-item">';
	$html .= '<div class="counters">';
	if ( empty( $atts['n'] ) ) {
		$html .= '<div class="counter" data-TargetNum="' . $n . '" data-Speed="' . $speed . '">0</div><span class="counter-unit">+</span>';
	} else {
		$html .= '<div class="counter" data-TargetNum="' . $value . '" data-Speed="' . $speed . '">0</div><span class="counter-unit">+</span>';
	}
	$html .= '</div>';
	$html .= '<div class="counter-title">' . $title . '</div>';
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}
add_shortcode( 'counter', 'be_counter' );