<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 浏览计数
function views_span() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '<span class="views"><i class="be be-eye ri"></i>', '</span>' );
	}
}

function be_views_inf() {
	if ( zm_get_option( 'post_views' ) ) {
		return be_the_views( false, '<span class="views">', '</span>' );
	}
}

function be_views_ico_inf() {
	if ( zm_get_option( 'post_views' ) ) {
		return be_the_views( false, '<span class="views"><i class="be be-eye ri"></i>', '</span>' );
	}
}

function views_li() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '<li class="views"><i class="be be-eye ri"></i>', '</li>' );
	}
}

function views_qa() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '<span class="qa-meta qa-views"><i>' . sprintf( __( '浏览', 'begin' ) ) . '</i>', '</span>' );
	}
}

function views_tao() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '', '件' );
	}
}

function views_videos() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '' . sprintf( __( '观看', 'begin' ) ) . '： ', '' );
	}
}

function views_video() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '' . sprintf( __( '观看', 'begin' ) ) . '：', ' ' . sprintf( __( '次', 'begin' ) ) . '' );
	}
}

function views_print() {
	if ( zm_get_option( 'post_views' ) ) {
		print '';
		be_the_views();
		print ''; }
}

function views_group() {
	if ( zm_get_option( 'post_views' ) ) {
		be_the_views( true, '<div class="group-views"><i class="be be-eye ri"></i>', '</div>' ); }
}