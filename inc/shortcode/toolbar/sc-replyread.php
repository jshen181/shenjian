<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 评论查看
function reply_read( $atts, $content = null ) {
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
				<div class="read-secret-title"><i class="be be-edit"></i>' . ( ! empty( $atts['title'] ) ? $title : zm_get_option( 'reply_read_t' ) ) . '</div>
				<div class="read-secret-text">' . ( ! empty( $atts['explain'] ) ? $explain : zm_get_option( 'reply_read_c' ) ) . '</div>
			</div>
			<div class="secret-read-btn">' . sprintf( __( '发表评论', 'begin' ) ) . '</div>
		</div>
	</div>';

	if ( is_user_logged_in() && ( current_user_can( 'administrator' ) || current_user_can( 'author' ) ) ) {
		$unique_id = uniqid( 'secret_' );
		return '
		<div id="expose-read-box" class="expose-content-box">
			<div class="show-secret-btn-area" style="background-image: url(' . get_secret_img() . ');">
				<div class="secret-text-tip">
					<div class="post-secret">' . sprintf( __( '验证通过，点击下方按钮查看内容', 'begin' ) ) . '</div>
					<div class="show-secret-btn secret-btn" data-target="' . $unique_id . '">
						<span class="secret-btn-text"><i class="be be-arrowdown"></i>' . sprintf( __( '显示内容', 'begin' ) ) . '</span>
					</div>
					<div class="hide-secret-btn secret-btn" data-target="' . $unique_id . '" style="display:none;">
						<span class="secret-btn-text"><i class="be be-arrowup"></i>' . sprintf( __( '隐藏内容', 'begin' ) ) . '</span>
					</div>
				</div>
			</div>
			<div id="' . $unique_id . '" class="secret-content" style="display:none;">' . do_shortcode( $content ) . '</div>
		</div>';
	}

	$email = null;
	// 优先获取登录用户的email
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$email        = $current_user->user_email;
	}

	// 如果未登录，尝试从cookie获取email
	elseif ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		$email = str_replace( '%40', '@', $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] );
	} else {
		return $html;
	}

	if ( empty( $email ) ) {
		return $html;
	}

	global $wpdb;
	$post_id   = get_the_ID();
	$unique_id = uniqid( 'secret_' );
	$query     = $wpdb->prepare( "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`=%d and `comment_approved`='1' and `comment_author_email`=%s LIMIT 1", $post_id, $email );
	if ( $wpdb->get_results( $query ) ) {
		$unique_id = uniqid( 'secret_' );
		return '
		<div id="expose-read-box" class="expose-content-box">
			<div class="show-secret-btn-area" style="background-image: url(' . get_secret_img() . ');">
				<div class="secret-text-tip">
					<div class="post-secret">' . sprintf( __( '验证通过，点击下方按钮查看内容', 'begin' ) ) . '</div>
					<div class="show-secret-btn secret-btn" data-target="' . $unique_id . '">
						<span class="secret-btn-text"><i class="be be-arrowdown"></i>' . sprintf( __( '显示内容', 'begin' ) ) . '</span>
					</div>
					<div class="hide-secret-btn secret-btn" data-target="' . $unique_id . '" style="display:none;">
						<span class="secret-btn-text"><i class="be be-arrowup"></i>' . sprintf( __( '隐藏内容', 'begin' ) ) . '</span>
					</div>
				</div>
			</div>
			<div id="' . $unique_id . '" class="secret-content" style="display:none;">' . do_shortcode( $content ) . '</div>
		</div>';
	} else {
		return $html;
	}
}
