<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// 关注
function be_user_follow() {
	if ( ! isset($_POST['user'] ) || ! is_numeric( $_POST['user'] ) ) {
		exit( json_encode( array( 'status' => 'error', 'msg' => __( '注册登录后关注', 'begin' ) ) ) );
	}

	$user_id = intval( $_POST['user'] );

	if ( $user_id == get_current_user_id() ) {
		exit( json_encode( array( 'status' => 'error', 'msg' => __( '不能关注自己', 'begin' ) ) ) );
	}

	$current_user_id = get_current_user_id();
	$follow = array_filter( explode( ',', get_user_meta( $current_user_id, 'follow', true ) ) );
	$fans   = array_filter( explode( ',', get_user_meta( $user_id, 'fans', true ) ) );

	// 检查是否已经关注
	if ( in_array( $user_id, $follow ) ) {
		$follow  = array_diff( $follow, [$user_id] );
		$fans    = array_diff( $fans, [$current_user_id] );
		update_user_meta( $current_user_id, 'follow', implode( ",", $follow ) );
		update_user_meta( $user_id, 'fans', implode( ",", $fans ) );
		exit( json_encode( array( 'status' => 'unfollow', 'msg' => __( '已取消关注', 'begin' ) ) ) );
	} else {
		$follow[]  = $user_id;
		$fans[]    = $current_user_id;
		update_user_meta( $current_user_id, 'follow', implode( ",", $follow ) );
		update_user_meta( $user_id, 'fans', implode( ",", $fans ) );
		exit( json_encode( array( 'status' => 'follow', 'msg' => __( '已关注', 'begin' ) ) ) );
	}
}

add_action( 'wp_ajax_follow', 'be_user_follow' );
add_action( 'wp_ajax_nopriv_follow', 'be_user_follow' );

function be_follow_btn( $btn ){
	switch( $btn ){
		case 'main':
			$meta     = get_user_meta( get_current_user_id(), 'follow', true );
			$authorid = get_the_author_meta( 'ID' );
		break;
		case 'author';
			$meta     = get_user_meta( get_current_user_id(), 'follow', true );
			$authorid = get_query_var( 'author' );
		break;
		case 'team';
			global $user;
			$meta     = get_user_meta( get_current_user_id(), 'follow', true );
			$authorid =  get_the_author_meta( 'ID', $user->ID );
		break;
	}

	if ( $meta ) {
		$follow = explode( ',', $meta );
	} else {
		$follow = array();
	}

	$follow_text   = __( '关注', 'begin' );
	$followed_text = __( '已关注', 'begin' );

	if ( is_user_logged_in() ) {
		echo '<div class="addfollow-btn">';
			if ( in_array( $authorid, $follow ) ) {
				echo '<a href="javascript:;" user="' . $authorid . '" class="addfollow be-followed ease" data-follow-text="' . esc_attr( $follow_text ) . '" data-followed-text="' . esc_attr( $followed_text ) . '">' . esc_html( $followed_text ) . '</a>';
			} else {
				echo '<a href="javascript:;" user="' . $authorid . '" class="addfollow be-follow ease" data-follow-text="' . esc_attr( $follow_text ) . '" data-followed-text="' . esc_attr( $followed_text ) . '">' . esc_html( $follow_text ) . '</a>';
			}
		echo '</div>';
	} else {
		echo '<a href="javascript:;" class="show-layer addfollow be-follow">' . esc_html( $follow_text ) . '</a>';
	}
}

// 关注数量
function get_follow_count( $authorID ) {
	$follow = explode( ',', get_user_meta( $authorID, 'follow', true ) );
	return count( array_filter( $follow ) );
}

// 粉丝数量
function get_fans_count( $authorID ) {
$fans = explode( ',', get_user_meta( $authorID, 'fans', true ) );
	return count( array_filter( $fans ) );
}
