<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 密码保护
function secret( $atts, $content = null ) {
	extract( shortcode_atts( array( 'key' => null ), $atts ) );

	$error_msg = '';
	if ( isset( $_POST['secret_key'] ) ) {
		if ( empty( $_POST['secret_key'] ) ) {
			$error_msg = sprintf( __( '密码不能为空', 'begin' ) );
		} elseif ( $_POST['secret_key'] != $key ) {
			$error_msg = sprintf( __( '密码错误，请重试', 'begin' ) );
		} else {
			$unique_id = uniqid( 'secret_' );
			return '
			<div class="expose-content-box">
				<div class="show-secret-btn-area" style="background-image: url(' . get_secret_img() . ');">
					<div class="secret-text-tip">
						<div class="post-secret">' . sprintf( __( '验证通过，点击下方按钮查看内容', 'begin' ) ) . '</div>
						<div class="show-secret-btn secret-btn" data-target="' . $unique_id . '"><span class="secret-btn-text"><i class="be be-arrowdown"></i>' . sprintf( __( '显示内容', 'begin' ) ) . '</span></div>
						<div class="hide-secret-btn secret-btn" data-target="' . $unique_id . '" style="display:none;"><span class="secret-btn-text"><i class="be be-arrowup"></i>' . sprintf( __( '隐藏内容', 'begin' ) ) . '</span></div>
					</div>
				</div>
				<div id="' . $unique_id . '" class="secret-content" style="display:none;">' . do_shortcode( $content ) . '</div>
			</div>';
		}
	}

	return '
	<div class="secret-content-box" style="background-image: url(' . get_secret_img() . ');">
		<form class="post-password-form" action="' . get_permalink() . '" method="post" autocomplete="off">
			<div class="secret-text-tip">
				<div class="post-secret"><i class="be be-edit"></i>
				' . ( empty( $error_msg ) ? sprintf( __( '输入密码查看隐藏内容', 'begin' ) ) : $error_msg ) . '
				</div>
				<input id="pwbox" type="password" size="20" name="secret_key" autocomplete="new-password">
				<input type="submit" value="' . sprintf( __( '提交', 'begin' ) ) . '" name="Submit">
			</div>
		</form>
	</div>';
}