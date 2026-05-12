<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 图片滑块
function baslider_zb_sanitize_xss_offset( $input ) {
	$output = str_replace( '})});alert(/XSS-offset/)//', '', $input );
	return $output;
}

function baslider_shortcode_init( $atts ) {
	$atts = shortcode_atts(
		array(
			'img1'      => '',
			'img2'      => '',
			'offset'    => '0.5',
			'direction' => 'horizontal',
			'width'     => '',
			'align'     => '',
			'before'    => '',
			'after'     => '',
			'hover'     => 'false',
			'external'  => 'false',
		),
		$atts,
		'be-baslider'
	);

	static $i = 1;

	$beID = 'be-baslider-' . $i;

	$isVertical    = '';
	$data_vertical = '';
	$isCenter      = '';
	$isLeft        = '';
	$isRight       = '';

	if ( esc_attr( $atts['align'] ) == 'center' ) {
		$isCenter = ' margin: 0 auto;';
		if ( empty( $atts['width'] ) ) {
			$atts['width'] = 'width: 50%;'; }
	}

	if ( esc_attr( $atts['align'] ) == 'right' ) {
		$isRight = ' float: right; margin-left: 20px;';
		if ( empty( $atts['width'] ) ) {
			$atts['width'] = 'width: 50%;'; }
	}

	if ( esc_attr( $atts['align'] ) == 'left' ) {
		$isLeft = ' float: left; margin-right: 20px;';
		if ( empty( $atts['width'] ) ) {
			$atts['width'] = 'width: 50%;'; }
	}

	if ( is_numeric( $atts['width'] ) ) {
		if ( empty( $atts['width'] ) ) {
			$atts['width'] = 'width: 100% !important; clear: both;';
		} else {
			$atts['width'] = 'width: ' . $atts['width'] . '%;';
		}
	} else {
		$atts['width'] = 'width: 100% !important; clear: both;';
	}

	if ( $atts['direction'] == 'vertical' ) {
		$isVertical    = ' data-orientation=vertical';
		$data_vertical = ", orientation: 'vertical'";
	}
	if ( $atts['hover'] === 'true' ) {
		$isHover  = ',move_slider_on_hover: true';
		$yesHover = 'ba-hover';
	} else {
		$isHover  = '';
		$yesHover = '';
	}

	$script = '';
	if ( ! empty( $atts['img1'] ) && ! empty( $atts['img2'] ) ) {
		$img1_alt = get_post_meta( $atts['img1'], '_wp_attachment_image_alt', true );
		$img2_alt = get_post_meta( $atts['img2'], '_wp_attachment_image_alt', true );

		$img1_alt_attr = $img1_alt ? ' alt="' . esc_attr( $img1_alt ) . '" title="' . esc_attr( $img1_alt ) . '"' : '';
		$img2_alt_attr = $img2_alt ? ' alt="' . esc_attr( $img2_alt ) . '" title="' . esc_attr( $img2_alt ) . '"' : '';

		$output  = '<div id="' . esc_attr( $beID ) . '" class="be-baslider" style="' . esc_attr( $atts['width'] . $isLeft . $isRight . $isCenter ) . '">';
		$output .= '<div class="baslider-container ' . esc_attr( $beID . ' ' . $yesHover ) . '"' . esc_attr( $isVertical ) . '>';
		if ( $atts['external'] === 'true' ) {
			$output .= '<img src="' . $atts['img1'] . '" />';
			$output .= '<img src="' . $atts['img2'] . '" />';
		} else {
			$output .= '<img src="' . esc_url( wp_get_attachment_url( $atts['img1'] ) ) . '" alt="' . esc_attr( $img1_alt ) . '" />';
			$output .= '<img src="' . esc_url( wp_get_attachment_url( $atts['img2'] ) ) . '" alt="' . esc_attr( $img2_alt ) . '" />';
		}
		$output .= '</div></div>';
		$script .= '<script>jQuery( document ).ready(function($ ) {';

		if ( $atts['direction'] == 'vertical' ) {
			$direc   = "[data-orientation='vertical']";
			$script .= '$(".baslider-container.' . esc_js( $beID ) . $direc . '").baslider({default_offset_pct: ' . esc_js( $atts['offset'] . $isHover ) . $data_vertical . '});';
		} else {
			$direc   = "[data-orientation!='vertical']";
			$script .= '$(".baslider-container.' . esc_js( $beID ) . $direc . '").baslider({default_offset_pct: ' . esc_js( $atts['offset'] . $isHover ) . '});';
		}

		if ( $atts['before'] ) {
			$script .= '$(".' . baslider_zb_sanitize_xss_offset( esc_js( $beID ) ) . ' .baslider-before-label").html("' . baslider_zb_sanitize_xss_offset( esc_js( $atts['before'] ) ) . '");';
		} else {
			$script .= '$(".' . baslider_zb_sanitize_xss_offset( esc_js( $beID ) ) . ' .baslider-overlay").hide();';
		}
		if ( $atts['after'] ) {
			$script .= '$(".' . baslider_zb_sanitize_xss_offset( esc_js( $beID ) ) . ' .baslider-after-label").html("' . baslider_zb_sanitize_xss_offset( esc_js( $atts['after'] ) ) . '");';
		} else {
			$script .= '$(".' . baslider_zb_sanitize_xss_offset( esc_js( $beID ) ) . ' .baslider-overlay").hide();';
		}
		$script .= '});</script>';

	} else {
		$output = '<div class="be-baslider" style="background: var(--be-bg-pink-fd);color: #685545;margin: 15px 0;padding: 15px;' . esc_attr( $atts['width'] . $isLeft . $isRight . $isCenter ) . '">必须选择两张图片，才能显示图片滑块！</div>';
	}

	++$i;
	add_action(
		'wp_footer',
		function () use ( $script ) {
			echo $script;
		},
		20
	);
	return $output;
}
add_shortcode( 'moveslider', 'baslider_shortcode_init' );