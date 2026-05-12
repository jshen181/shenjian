<?php
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}
// 日期
add_action( 'wp_ajax_nopriv_sort_date', 'sort_date_callback' );
add_action( 'wp_ajax_sort_date', 'sort_date_callback' );

function sort_date_callback() {
	$args  = array(
		'post_type'     => 'post',
		'orderby'       => 'date',
		'post_status'   => 'publish',
		'order'         => 'DESC',
		'no_found_rows' => true,
	);
	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}

// 更新
add_action( 'wp_ajax_nopriv_sort_modified', 'sort_modified_callback' );
add_action( 'wp_ajax_sort_modified', 'sort_modified_callback' );

function sort_modified_callback() {
	$args  = array(
		'post_type'     => 'post',
		'orderby'       => 'modified',
		'post_status'   => 'publish',
		'order'         => 'DESC',
		'no_found_rows' => true,
	);
	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}

// 随机
add_action( 'wp_ajax_nopriv_sort_random', 'sort_random_callback' );
add_action( 'wp_ajax_sort_random', 'sort_random_callback' );

function sort_random_callback() {
	$args  = array(
		'post_type'     => 'post',
		'orderby'       => 'rand',
		'post_status'   => 'publish',
		'no_found_rows' => true,
	);
	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}

// 评论
add_action( 'wp_ajax_nopriv_sort_comments', 'sort_comments_callback' );
add_action( 'wp_ajax_sort_comments', 'sort_comments_callback' );

function sort_comments_callback() {
	$args  = array(
		'post_type'     => 'post',
		'orderby'       => 'comment_count',
		'order'         => 'DESC',
		'post_status'   => 'publish',
		'no_found_rows' => true,
	);
	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}

// 热门
add_action( 'wp_ajax_nopriv_sort_views', 'sort_views_callback' );
add_action( 'wp_ajax_sort_views', 'sort_views_callback' );

function sort_views_callback() {
	$args = array(
		'post_type'     => 'post',
		'meta_key'      => 'views',
		'orderby'       => 'meta_value_num',
		'order'         => 'DESC',
		'post_status'   => 'publish',
		'no_found_rows' => true,
	);

	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}

// 点赞
add_action( 'wp_ajax_nopriv_sort_like', 'sort_like_callback' );
add_action( 'wp_ajax_sort_like', 'sort_like_callback' );

function sort_like_callback() {
	$args  = array(
		'post_type'     => 'post',
		'meta_key'      => 'zm_like',
		'orderby'       => 'meta_value_num',
		'order'         => 'DESC',
		'post_status'   => 'publish',
		'no_found_rows' => true,
	);
	$query = new WP_Query( $args );
	$posts = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template/content', get_post_format() );
		$posts[] = ob_get_clean();
	}
	wp_send_json_success( $posts );
	wp_die();
}
