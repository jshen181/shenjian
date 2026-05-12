<?php
// 克隆小工具
if ( zm_get_option( 'clone_widgets' ) ) {
	add_action( 'wp_ajax_be_clone_widget', 'be_clone_widget_ajax' );
	add_action( 'admin_head', 'be_clone_widgets_script' );

	function be_clone_widget_ajax() {
		check_ajax_referer( 'be-clone-widget-nonce', 'nonce' );

		$widget_id  = isset( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
		$sidebar_id = isset( $_POST['sidebar_id'] ) ? sanitize_text_field( $_POST['sidebar_id'] ) : '';

		if ( empty( $widget_id ) || empty( $sidebar_id ) ) {
			wp_send_json_error( 'Invalid parameters' );
		}

		$parts = explode( '-', $widget_id );
		if ( count( $parts ) < 2 ) {
			wp_send_json_error( 'Invalid widget ID' );
		}

		$id_base       = implode( '-', array_slice( $parts, 0, -1 ) );
		$widget_number = end( $parts );

		global $wp_registered_widget_controls;
		$control = isset( $wp_registered_widget_controls[ $widget_id ] ) ? $wp_registered_widget_controls[ $widget_id ] : false;

		if ( ! $control || ! isset( $control['callback'] ) ) {
			wp_send_json_error( 'Widget control not found' );
		}

		$widget_obj  = $control['callback'][0];
		$option_name = $widget_obj->option_name;
		$settings    = get_option( $option_name, array() );

		if ( ! isset( $settings[ $widget_number ] ) ) {
			wp_send_json_error( 'Widget settings not found' );
		}

		$current_settings = $settings[ $widget_number ];

		$max_number = 0;
		foreach ( $settings as $num => $opts ) {
			if ( is_numeric( $num ) && $num > $max_number ) {
				$max_number = $num;
			}
		}
		$new_widget_number = $max_number + 1;

		$settings[ $new_widget_number ] = $current_settings;
		update_option( $option_name, $settings );

		$new_widget_id = $id_base . '-' . $new_widget_number;

		$sidebars = get_option( 'sidebars_widgets', array() );

		if ( isset( $sidebars[ $sidebar_id ] ) && is_array( $sidebars[ $sidebar_id ] ) ) {
			$widget_positions  = $sidebars[ $sidebar_id ];
			$original_position = array_search( $widget_id, $widget_positions );

			if ( $original_position !== false ) {
				$new_positions           = array_slice( $widget_positions, 0, $original_position + 1 );
				$new_positions[]         = $new_widget_id;
				$new_positions           = array_merge( $new_positions, array_slice( $widget_positions, $original_position + 1 ) );
				$sidebars[ $sidebar_id ] = $new_positions;
			} else {
				$sidebars[ $sidebar_id ][] = $new_widget_id;
			}
		} else {
			$sidebars[ $sidebar_id ] = array( $new_widget_id );
		}

		wp_set_sidebars_widgets( $sidebars );

		wp_send_json_success(
			array(
				'new_widget_id'     => $new_widget_id,
				'new_widget_number' => $new_widget_number,
				'sidebar_id'        => $sidebar_id,
			)
		);
	}

	function be_clone_widgets_script() {
		global $pagenow;
		if ( $pagenow != 'widgets.php' ) {
			return;
		}
		wp_enqueue_script( 'clone_widgets', get_template_directory_uri() . '/inc/assets/js/clone-widgets.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'clone_widgets',
			'be_clone_widgets',
			array(
				'text'  => '复制',
				'nonce' => wp_create_nonce( 'be-clone-widget-nonce' ),
			)
		);
	}
}
