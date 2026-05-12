<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 角色查看
function be_user_role_visible( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'   => '',
				'explain' => '',
				'tip'     => '',
				'point'   => '',
				'role'    => '',
			),
			$atts
		)
	);

	$html  = '<div class="secret-content-box" style="background-image: url(' . get_secret_img() . ');">';
	$html .= '<div class="secret-text-tip">';
	$html .= '<div class="post-secret">';
	if ( ! empty( $atts['title'] ) ) {
		$html .= '<div class="read-secret-title"><i class="be be-edit"></i>' . $title . '</div>';
	}
	if ( ! empty( $atts['explain'] ) ) {
		$html .= '<div class="read-secret-text">' . $explain . '</div>';
	}
	$html .= '</div>';
	if ( zm_get_option( 'user_l' ) ) {
		$html .= '<div class="secret-login-btn secret-login-url"><a href="' . zm_get_option( 'user_l' ) . '" rel="external nofollow" target="_blank"><div class="read-btn read-btn-login"><i class="read-btn-ico"></i>' . sprintf( __( '登录', 'begin' ) ) . '</a></div>';
	} else {
		$html .= '<div class="secret-login-btn show-layer cur">' . sprintf( __( '登录', 'begin' ) ) . '</div>';
	}
	$html .= '</div>';
	$html .= '</div>';

	$output  = '<div class="secret-content-box" style="background-image: url(' . get_secret_img() . ');">';
	$output .= '<div class="secret-text-tip">';
	$output .= '<div class="post-secret">';
	if ( ! empty( $atts['point'] ) ) {
		$output .= '<div class="read-secret-title"><i class="be be-edit"></i>' . $point . '</div>';
	}

	if ( ! empty( $atts['tip'] ) ) {
		$output .= '<div class="read-secret-text">' . $tip . '</div>';
	}
	$output .= '</div>';
	$output .= '</div>';
	$output .= '</div>';

	global $current_user;
	if ( in_array( $role, $current_user->roles ) || $current_user->ID == get_the_author_meta( 'ID' ) ) {
		$unique_id = uniqid( 'secret_' );
		return '
		<div class="expose-content-box">
			<div class="show-secret-btn-area" style="background-image: url(' . get_secret_img() . ');">
				<div class="secret-text-tip">
					<div class="post-secret">' . $current_user->display_name . sprintf( __( '已登录，点击下方按钮查看内容', 'begin' ) ) . '</div>
					<div class="show-secret-btn secret-btn" data-target="' . $unique_id . '"><span class="secret-btn-text"><i class="be be-arrowdown"></i>' . sprintf( __( '显示内容', 'begin' ) ) . '</span></div>
					<div class="hide-secret-btn secret-btn" data-target="' . $unique_id . '" style="display:none;"><span class="secret-btn-text"><i class="be be-arrowup"></i>' . sprintf( __( '隐藏内容', 'begin' ) ) . '</span></div>
				</div>
			</div>
			<div id="' . $unique_id . '" class="secret-content" style="display:none;">' . do_shortcode( $content ) . '</div>
		</div>';
	} elseif ( ! is_user_logged_in() ) {
			return $html;
	} else {
		return $output;
	}
}
