<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 登录查看
function login_to_read( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'   => '',
				'explain' => '',
			),
			$atts
		)
	);

	$html = '
	<div class="secret-content-box" style="background-image: url(' . get_secret_img() . ');">
		<div class="secret-text-tip">
			<div class="post-secret">
				<div class="read-secret-title"><i class="be be-edit"></i>' . ( ! empty( $atts['title'] ) ? $title : zm_get_option( 'login_read_t' ) ) . '</div>
				<div class="read-secret-text">' . ( ! empty( $atts['explain'] ) ? $explain : zm_get_option( 'login_read_c' ) ) . '</div>
			</div>'
				. ( zm_get_option( 'user_l' )
					? '<div class="secret-login-btn secret-login-url"><a href="' . esc_url( zm_get_option( 'user_l' ) ) . '" rel="external nofollow" target="_blank">' . esc_html( __( '登录', 'begin' ) ) . '</div></a>'
					: '<div class="secret-login-btn show-layer cur">' . esc_html( __( '登录', 'begin' ) ) . '</div>'
				) . '
		</div>
	</div>';

	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$unique_id    = uniqid( 'secret_' );
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
	} else {
		return $html;
	}
}
