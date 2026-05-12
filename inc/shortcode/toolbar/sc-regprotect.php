<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
// 注册保护
function password_view( $atts, $content = null ) {
	extract( shortcode_atts( array( 'key' => null ), $atts ) );

	if ( isset( $_POST['secret_key'] ) && $_POST['secret_key'] == $key ) {
		return '<div class="shide-content">' . do_shortcode( $content ) . '</div>';
	} else {
		return '
		<form class="password-view post-password-form" action="' . get_permalink() . '" method="post">
			<div class="reg-protect-tip">' . sprintf( __( '输入密码', 'begin' ) ) . '</div>
			<p>
				<input id="pwbox" type="password" size="20" name="secret_key">
				<input type="submit" value="' . sprintf( __( '提交', 'begin' ) ) . '" name="submit">
			</p>
		</form>';
	}
}

add_shortcode( 'bepassword', 'password_view' );