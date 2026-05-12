<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 图文
function be_text_img_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title' => '',
				'text'  => '',
				'img'   => '',
				'align' => 'left',
			),
			$atts
		)
	);

	if ( isset( $atts['align'] ) && $atts['align'] === 'left' ) {
		$html          = '<div class="mixed-item mixed-r">';
			$html     .= '<div class="mixed-img-area mixed-box">';
				$html .= '<img alt="' . $title . '" class="mixed-img" src="' . $img . '">';
			$html     .= '</div>';

			$html .= '<div class="mixed-area mixed-box">';
		if ( ! empty( $atts['title'] ) ) {
			$html .= '<div class="mixed-title">' . $title . '</div>';
		}
				$html .= '<div class="mixed-text">' . $text . '</div>';
			$html     .= '</div>';

		$html .= '</div>';
	} else {
		$html          = '<div class="mixed-item mixed-l">';
				$html .= '<div class="mixed-area mixed-box">';
		if ( ! empty( $atts['title'] ) ) {
			$html .= '<div class="mixed-title">' . $title . '</div>';
		}
					$html .= '<div class="mixed-text">' . $text . '</div>';
				$html     .= '</div>';
				$html     .= '<div class="mixed-img-area mixed-box">';
					$html .= '<img alt="' . $title . '" class="mixed-img" src="' . $img . '">';
				$html     .= '</div>';
		$html             .= '</div>';
	}
	return $html;
}
add_shortcode( 'be_text_img', 'be_text_img_shortcode' );