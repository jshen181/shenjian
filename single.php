<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template_map = array(
	'single_layout_qa'      => 'single-beqa',
	'novel_layout'          => 'single-novel',
	'single_layout_down'    => 'single-download',
	'single_layout_vip'     => 'single-vip',
	'single_layout_display' => 'single-display',
	'single_layout_full'    => 'single-full',
	'single_layout_note'    => 'single-note',
	'single_layout_bio'     => 'single-bio',
	'single_layout_notes'   => 'single-notes',
	'single_layout_goods'   => 'single-goods',
);

$template = 'single-default';

foreach ( $template_map as $option => $template_name ) {
	if ( in_category( explode( ',', zm_get_option( $option ) ) ) ) {
		$template = $template_name;
		break;
	}
}

get_template_part( $template );
