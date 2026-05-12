<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'be_get_archive_post_types' ) ) {
	function be_get_archive_post_types() {
		$types = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'names'
		);
		// 排除的文章类型
		$exclude_types   = array( 'attachment', 'revision', 'nav_menu_item', 'surl' );
		$exclude_setting = zm_get_option( 'archive_exclude' );
		if ( $exclude_setting && is_array( $exclude_setting ) ) {
			$exclude_types = array_merge( $exclude_types, $exclude_setting );
		}
		$archive_types = array_diff( $types, $exclude_types );
		return $archive_types;
	}
}

add_action( 'wp_ajax_be_get_archives_posts', 'be_get_archives_posts' );
add_action( 'wp_ajax_nopriv_be_get_archives_posts', 'be_get_archives_posts' );

function be_get_archives_posts() {
	$year  = intval( $_POST['year'] );
	$month = intval( $_POST['month'] );

	if ( ! $year || ! $month ) {
		wp_die();
	}

	$post_types = be_get_archive_post_types();

	$the_query = new WP_Query(
		array(
			'year'                   => $year,
			'monthnum'               => $month,
			'post_status'            => 'publish',
			'post_type'              => $post_types,
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$output = '';
	while ( $the_query->have_posts() ) : $the_query->the_post();
		$post_type     = get_post_type();
		$post_type_obj = get_post_type_object( $post_type );
		// 文章类型标签
		// $type_label    = $post_type !== 'post' ? ' <span class="archive-type-label">[' . $post_type_obj->labels->singular_name . ']</span>' : '';
		$output .= '<li><time datetime="' . get_the_date( 'Y-m-d' ) . ' ' . get_the_time( 'H:i:s' ) . '">' . get_the_time( 'd' ) . sprintf( __( '日', 'begin' ) ) . '</time><a href="' . get_permalink() . '" target="_blank">' . get_the_title() . '</a></li>';
	endwhile;
	wp_reset_postdata();

	echo $output;
	wp_die();
}
