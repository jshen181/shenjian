<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 隐藏后台菜单
add_action( 'admin_menu', 'hide_remove_menu' );
function hide_remove_menu() {
	$hide_wp_menu = cx_get_option( 'hide_wp_menu' );
	$hide_be_menu = cx_get_option( 'hide_be_menu' );

	$menu_items = array(
		'wp' => array(
			'link'      => 'link-manager.php',
			'page'      => 'edit.php?post_type=page',
			'comments'  => 'edit-comments.php',
			'themes'    => 'themes.php',
			'customize' => 'customize.php?return=' . rawurlencode( $_SERVER['REQUEST_URI'] ),
			'plugins'   => 'plugins.php',
			'users'     => 'users.php',
			'tools'     => 'tools.php',
			'options'   => 'options-general.php',
		),
		'be' => array(
			'bulletin' => 'edit.php?post_type=bulletin',
			'picture'  => 'edit.php?post_type=picture',
			'video'    => 'edit.php?post_type=video',
			'tao'      => 'edit.php?post_type=tao',
			'sites'    => 'edit.php?post_type=sites',
			'show'     => 'edit.php?post_type=show',
			'playlist' => 'edit.php?post_type=ai_playlist',
			'surl'     => 'edit.php?post_type=surl',
			'invite'   => 'be_invitation_code',
		),
	);

	foreach ( $menu_items['wp'] as $key => $page ) {
		if ( is_array( $hide_wp_menu ) && in_array( $key, $hide_wp_menu ) ) {
			$key === 'customize' ? remove_submenu_page( 'themes.php', $page ) : remove_menu_page( $page );
		}
	}

	foreach ( $menu_items['be'] as $key => $page ) {
		if ( is_array( $hide_be_menu ) && in_array( $key, $hide_be_menu ) ) {
			remove_menu_page( $page );
		}
	}
}
