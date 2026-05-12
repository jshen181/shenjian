<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 加载所有小工具文件
function be_load_widgets() {
	$widget_path = get_template_directory() . '/inc/widgets';

	if ( ! is_dir( $widget_path ) ) {
		return;
	}

	$widget_files = glob( $widget_path . '/widget-*.php' );

	if ( $widget_files ) {
		foreach ( $widget_files as $file ) {
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	}
}

// 加载小工具
add_action( 'after_setup_theme', 'be_load_widgets' );
