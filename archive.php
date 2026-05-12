<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template_map = array(
	'cat_layout_img'       => 'category-img',
	'cat_layout_img_s'     => 'category-img-s',
	'cat_layout_noside'    => 'archive-noside',
	'cat_layout_grid'      => 'category-grid',
	'cat_layout_fall'      => 'category-fall',
	'cat_layout_play'      => 'category-play',
	'cat_layout_full'      => 'category-full',
	'cat_layout_list'      => 'category-list',
	'cat_layout_be_qa'     => 'category-be-qa',
	'cat_layout_novel'     => 'category-novel',
	'cat_child_novel'      => 'category-child-novel',
	'cat_layout_note'      => 'category-note',
	'cat_layout_notes'     => 'category-notes',
	'cat_layout_assets'    => 'category-assets',
	'cat_layout_square'    => 'category-square',
	'cat_layout_line'      => 'category-line',
	'cat_child_cover'      => 'category-child-cover',
	'cat_child_nav'        => 'category-child-nav',
	'cat_child_portfolio'  => 'category-child-portfolio',
	'cat_child_hot'        => 'category-child-hot',
	'cat_layout_child'     => 'category-child-nosidebar',
	'cat_layout_child_img' => 'category-child-img',
	'cat_layout_child_tdk' => 'category-child-tdk',
	'cat_layout_display'   => 'category-display',
	'ajax_layout_code_a'   => 'category-code-a',
	'ajax_layout_code_b'   => 'category-code-b',
	'ajax_layout_code_c'   => 'category-code-c',
	'ajax_layout_code_d'   => 'category-code-d',
	'ajax_layout_code_e'   => 'category-code-e',
	'ajax_layout_code_f'   => 'category-code-f',
	'ajax_layout_code_g'   => 'category-code-g',
	'ajax_layout_code_h'   => 'category-code-h',
	'cat_layout_default'   => 'archive-default',
	'cat_layout_goods'     => 'category-goods',
);

$template = 'archive-default';

foreach ( $template_map as $option => $template_name ) {
	if ( is_category( explode( ',', zm_get_option( $option ) ) ) || is_tag( explode( ',', zm_get_option( $option ) ) ) ) {
		$template = $template_name;
		break;
	}
}

if ( $template === 'archive-default' ) {
	if ( is_category() ) {
		$default_template = zm_get_option( 'default_cat_template' );
		if ( $default_template ) {
			$template = $default_template;
		}
	} elseif ( is_tag() ) {
		$default_template = zm_get_option( 'default_tag_template' );
		if ( $default_template ) {
			$template = $default_template;
		}
	}
}

get_template_part( $template );
