<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$layout = be_get_option( 'layout' );
if ( ! $layout ) {
	$layout = 'blog';
}

$template_map = array(
	'blog'  => 'blog',
	'img'   => 'grid',
	'grid'  => 'grid-cat',
	'cms'   => 'cms',
	'group' => 'group',
);

if ( isset( $template_map[ $layout ] ) ) {
	get_template_part( 'template/' . $template_map[ $layout ] );
}
