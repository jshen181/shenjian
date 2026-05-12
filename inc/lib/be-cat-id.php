<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 分类ID显示函数
 *
 * @param string $taxonomy 分类法名称
 * @param string $empty_msg 空分类提示信息
 */
function show_taxonomy_id( $taxonomy, $empty_msg = '暂无分类' ) {
	$categories = get_categories(
		array(
			'taxonomy'   => array( $taxonomy ),
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'hide_empty' => 0,
		)
	);

	if ( $categories ) {
		foreach ( $categories as $cat ) {
			echo '<ol class="show-id">' . $cat->cat_name . '<span>' . $cat->cat_ID . '</span></ol>';
		}
	} else {
		echo '<ol class="show-id">' . $empty_msg . '</ol>';
	}
}

function special_show_id() {
	$options_pages = get_pages(
		array(
			'meta_key'     => 'special',
			'hierarchical' => 0,
			'sort_column'  => 'ID',
		)
	);

	foreach ( $options_pages as $page ) {
		$output = '<ol class="show-id">' . $page->post_title . '<span>' . $page->ID . '</span></ol>';
		echo $output;
	}

	if ( ! $options_pages ) {
		$output = '<ol class="show-id">暂无专题</ol>';
		echo $output;
	}
}

function show_id() {
	show_taxonomy_id( 'category' );
}

function notice_show_id() {
	show_taxonomy_id( 'notice' );
}

function gallery_show_id() {
	show_taxonomy_id( 'gallery' );
}

function videos_show_id() {
	show_taxonomy_id( 'videos' );
}

function taobao_show_id() {
	show_taxonomy_id( 'taobao' );
}

function products_show_id() {
	show_taxonomy_id( 'products' );
}

function favorites_show_id() {
	show_taxonomy_id( 'favorites' );
}

function product_show_id() {
	show_taxonomy_id( 'product_cat' );
}

function column_show_id() {
	show_taxonomy_id( 'special', '暂无专栏' );
}
