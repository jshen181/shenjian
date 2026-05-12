<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 下载回复查看
function pan_password( $atts, $content = null ) {
	if ( zm_get_option( 'login_down_key' ) ) {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>网盘密码</strong><span class="reply-prompt">登录可见</span>', 'begin' ) ) . '</div>' ), $atts ) );
	} else {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>网盘密码</strong><span class="reply-prompt">发表评论并刷新可见</span>', 'begin' ) ) . '</div>' ), $atts ) );
	}
	$email   = null;
	$user_ID = (int) wp_get_current_user()->ID;
	if ( $user_ID > 0 ) {
		$email = get_userdata( $user_ID )->user_email;
		if ( current_user_can( 'level_10' ) ) {
			return do_shortcode( $content );}
	} elseif ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		$email = str_replace( '%40', '@', $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] );
	} else {
		return $notice;
	}
	if ( empty( $email ) ) {
		return $notice;
	}
	global $wpdb;
	$post_id = get_the_ID();
	$query   = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
	if ( zm_get_option( 'login_down_key' ) ) {
		if ( is_user_logged_in() ) {
			return do_shortcode( do_shortcode( $content ) );
		} else {
			return $notice;
		}
	} elseif ( $wpdb->get_results( $query ) ) {
			return do_shortcode( do_shortcode( $content ) );
	} else {
		return $notice;
	}
}

function rar_password( $atts, $content = null ) {
	if ( zm_get_option( 'login_down_key' ) ) {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>解压密码</strong><span class="reply-prompt">登录可见</span>', 'begin' ) ) . '</div>' ), $atts ) );
	} else {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>解压密码</strong><span class="reply-prompt">发表评论并刷新可见</span>', 'begin' ) ) . '</div>' ), $atts ) );
	}
	$email   = null;
	$user_ID = (int) wp_get_current_user()->ID;
	if ( $user_ID > 0 ) {
		$email = get_userdata( $user_ID )->user_email;
		if ( current_user_can( 'level_10' ) ) {
			return do_shortcode( $content );}
	} elseif ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		$email = str_replace( '%40', '@', $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] );
	} else {
		return $notice;
	}
	if ( empty( $email ) ) {
		return $notice;
	}
	global $wpdb;
	$post_id = get_the_ID();
	$query   = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
	if ( zm_get_option( 'login_down_key' ) ) {
		if ( is_user_logged_in() ) {
			return do_shortcode( do_shortcode( $content ) );
		} else {
			return $notice;
		}
	} elseif ( $wpdb->get_results( $query ) ) {
			return do_shortcode( do_shortcode( $content ) );
	} else {
		return $notice;
	}
}

function down_password( $atts, $content = null ) {
	if ( zm_get_option( 'login_down_key' ) ) {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>下载地址</strong><span class="reply-prompt">登录可见</div>', 'begin' ) ) . '</span>' ), $atts ) );
	} else {
		extract( shortcode_atts( array( 'notice' => '<div class="reply-pass">' . sprintf( __( '<strong>下载地址</strong><span class="reply-prompt">发表评论并刷新可见</div>', 'begin' ) ) . '</span>' ), $atts ) );
	}
	$email   = null;
	$user_ID = (int) wp_get_current_user()->ID;
	if ( $user_ID > 0 ) {
		$email = get_userdata( $user_ID )->user_email;
		if ( current_user_can( 'level_10' ) ) {
			return do_shortcode( $content );}
	} elseif ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		$email = str_replace( '%40', '@', $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] );
	} else {
		return $notice;
	}
	if ( empty( $email ) ) {
		return $notice;
	}
	global $wpdb;
	$post_id = get_the_ID();
	$query   = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
	if ( zm_get_option( 'login_down_key' ) ) {
		if ( is_user_logged_in() ) {
			return do_shortcode( do_shortcode( $content ) );
		} else {
			return $notice;
		}
	} elseif ( $wpdb->get_results( $query ) ) {
			return do_shortcode( do_shortcode( $content ) );
	} else {
		return $notice;
	}
}