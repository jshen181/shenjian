<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 关注公众号
function wechat_key( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'key'   => null,
				'reply' => null,
			),
			$atts
		)
	);

	$error_msg = '';
	if ( isset( $_POST['wechat_key'] ) ) {
		if ( empty( $_POST['wechat_key'] ) ) {
			$error_msg = sprintf( __( '验证码不能为空', 'begin' ) );
		} elseif ( $_POST['wechat_key'] != $key ) {
			$error_msg = sprintf( __( '验证码错误，请重试', 'begin' ) );
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
	<div class="secret-content-box secret-wechat-box" style="background-image: url(' . get_secret_img() . ');">
		<form class="post-password-form wechat-key-form" action="' . get_permalink() . '" method="post">
			<div class="secret-text-tip wechat-left">
				<div class="post-secret"><i class="be be-edit"></i>
				' . ( empty( $error_msg ) ? sprintf( __( '输入验证码查看隐藏内容', 'begin' ) ) : $error_msg ) . '
				</div>
				<input id="wpbox" type="password" size="20" name="wechat_key" autocomplete="new-password">
				<input type="submit" value="' . sprintf( __( '提交', 'begin' ) ) . '" name="Submit">
				<div class="wechat-secret">
					<div class="wechat-follow">扫描二维码关注本站微信公众号 <span class="wechat-w">' . zm_get_option( 'wechat_fans' ) . '</span></div>
					<div class="wechat-follow">或者在微信里搜索 <span class="wechat-w">' . zm_get_option( 'wechat_fans' ) . '</span></div>
					<div class="wechat-follow">回复 <span class="wechat-w">' . $reply . '</span> 获取验证码</div>
				</div>
			</div>
			<div class="wechat-right">
				<img src="' . zm_get_option( 'wechat_qr' ) . '" alt="wechat">
				<span class="wechat-t">' . zm_get_option( 'wechat_fans' ) . '</span>
			</div>
			<div class="clear"></div>
		</form>
	</div>';
}