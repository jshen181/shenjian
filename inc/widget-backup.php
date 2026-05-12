<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// 小工具备份
// 导出
function be_export_widgets_data() {
	$available_widgets = be_available_widgets();
	$widget_instances = array();
	foreach ( $available_widgets as $widget_data ) {
		$instances = get_option( 'widget_' . $widget_data['id_base'] );
		if ( ! empty( $instances ) ) {
			foreach ( $instances as $instance_id => $instance_data ) {
				if ( is_numeric( $instance_id ) ) {
					$unique_instance_id                    = $widget_data['id_base'] . '-' . $instance_id;
					$widget_instances[$unique_instance_id] = $instance_data;
				}
			}
		}
	}

	// 收集侧边栏及其小工具
	$sidebars_widgets          = get_option( 'sidebars_widgets' );
	$sidebars_widget_instances = array();
	foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {
		if ( 'wp_inactive_widgets' === $sidebar_id ) {
			continue;
		}

		if ( ! is_array($widget_ids ) || empty( $widget_ids ) ) {
			continue;
		}

		foreach ( $widget_ids as $widget_id ) {
			if ( isset( $widget_instances[$widget_id] ) ) {
				$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
			}
		}
	}

	$data = apply_filters( 'be_unencoded_export_data', $sidebars_widget_instances );
	$encoded_data = wp_json_encode( $data );
	return apply_filters( 'be_export_widgets_data', $encoded_data );
}

// 导出文件
function be_send_export_widgets_file() {
	if ( ! empty( $_GET['export'] ) ) {
		check_admin_referer( 'be_export', 'be_export_nonce' );

		$site_url  = site_url( '', 'http' );
		$site_url  = trim( $site_url, '/\\' );
		$filename  = str_replace( 'http://', '', $site_url );
		$filename  = str_replace( array('/', '\\'), '-', $filename );
		$filename .= '-widgets.wie';
		$filename  = apply_filters( 'be_export_filename', $filename );

		$file_contents = be_export_widgets_data();
		$filesize      = strlen( $file_contents );

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $filesize);

		@ob_end_clean();
		flush();
		echo $file_contents;
		exit;
	}
}

add_action( 'load-tools_page_widget-importer-exporter', 'be_send_export_widgets_file' );

// 导入
function be_process_submit() {
	if ( empty( $_POST ) ) {
		return;
	}

	check_admin_referer( 'be_import', 'be_import_nonce' );

	if ( ! empty( $_POST['be_import_data'] ) ) {
		be_submit_widget_import_data();
	} else {
		wp_die( '必须输入数据', '备份小工具 | 必须输入数据', array( 'back_link' => true ) );
    }
}

add_action( 'load-tools_page_widget-importer-exporter', 'be_process_submit' );

function be_submit_widget_import_data() {
	global $be_import_results;

	check_admin_referer( 'be_import', 'be_import_nonce' );

	if ( ! empty( $_POST['be_import_data'] ) ) {
		$content = '';
		$content = filter_var( wp_unslash( $_POST['be_import_data'] ), FILTER_DEFAULT );
		$content = trim( $content );

		$data               = json_decode($content);
		$be_import_results = be_import_data($data);
	}
}


// 导入小工具JSON数据
function be_import_data( $data ) {
	global $wp_registered_sidebars;

	if ( empty( $data ) || ! is_object( $data ) ) {
		wp_die( '导入的数据无效', '备份小工具 | 导入的数据无效', array( 'back_link' => true ) );
	}

	do_action( 'be_before_import' );
	$data = apply_filters( 'be_import_data', $data );

	$available_widgets = be_available_widgets();

	$widget_instances = array();
	foreach ( $available_widgets as $widget_data ) {
		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
	}

	$results = array();

	foreach ( $data as $sidebar_id => $widgets ) {
		if ( 'wp_inactive_widgets' === $sidebar_id ) {
			continue;
		}

		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
			$sidebar_available    = true;
			$use_sidebar_id       = $sidebar_id;
			$sidebar_message_type = 'success';
			$sidebar_message      = '';
		} else {
			$sidebar_available    = false;
			$use_sidebar_id       = 'wp_inactive_widgets';
			$sidebar_message_type = 'error';
			$sidebar_message      = '主题不存在此小工具';
		}

		$results[$sidebar_id]['name']         = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id;
		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
		$results[$sidebar_id]['message']      = $sidebar_message;
		$results[$sidebar_id]['widgets']      = array();

		foreach ( $widgets as $widget_instance_id => $widget ) {
			$fail = false;

			$id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
				$fail                = true;
				$widget_message_type = 'error';
				$widget_message      = '不支持小工具';
			}
			$widget = apply_filters( 'be_widget_settings', $widget );
			$widget = json_decode( wp_json_encode($widget), true );
			$widget = apply_filters( 'be_widget_settings_array', $widget );

			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {
				$sidebars_widgets = get_option( 'sidebars_widgets' );
				$sidebar_widgets  = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array();

				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
				foreach ( $single_widget_instances as $check_id => $check_widget ) {
					if ( in_array( "$id_base-$check_id", $sidebar_widgets, true ) && ( array ) $widget === $check_widget ) {
						$fail                = true;
						$widget_message_type = 'warning';
						$widget_message      = '已经存在';
						break;
					}
				}
			}

			if ( ! $fail ) {
				$single_widget_instances   = get_option( 'widget_' . $id_base );
				$single_widget_instances   = ! empty($single_widget_instances ) ? $single_widget_instances : array(
					'_multiwidget' => 1,
				);
				$single_widget_instances[] = $widget;

				end( $single_widget_instances );
				$new_instance_id_number = key( $single_widget_instances );

				if ( '0' === strval( $new_instance_id_number ) ) {
					$new_instance_id_number = 1;
					$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
					unset( $single_widget_instances[0] );
				}

				if ( isset( $single_widget_instances['_multiwidget'] ) ) {
					$multiwidget = $single_widget_instances['_multiwidget'];
					unset($single_widget_instances['_multiwidget']);
					$single_widget_instances['_multiwidget'] = $multiwidget;
				}

				update_option( 'widget_' . $id_base, $single_widget_instances );

				$sidebars_widgets = get_option( 'sidebars_widgets' );

				if ( ! $sidebars_widgets ) {
					$sidebars_widgets = array();
				}

				$new_instance_id = $id_base . '-' . $new_instance_id_number;

				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id;

				update_option( 'sidebars_widgets', $sidebars_widgets );

				$after_widget_import = array(
					'sidebar'           => $use_sidebar_id,
					'sidebar_old'       => $sidebar_id,
					'widget'            => $widget,
					'widget_type'       => $id_base,
					'widget_id'         => $new_instance_id,
					'widget_id_old'     => $widget_instance_id,
					'widget_id_num'     => $new_instance_id_number,
					'widget_id_num_old' => $instance_id_number,
				);
				do_action( 'be_after_widget_import', $after_widget_import );

				if ( $sidebar_available ) {
					$widget_message_type = 'success';
					$widget_message      = '导入成功';
				} else {
					$widget_message_type = '提示';
					$widget_message      = '导入到非活动状态';
				}
			}

			$results[$sidebar_id]['widgets'][$widget_instance_id]['name']         = isset( $available_widgets[$id_base]['name']) ? $available_widgets[$id_base]['name'] : $id_base;
			$results[$sidebar_id]['widgets'][$widget_instance_id]['title']        = ! empty( $widget['title'] ) ? $widget['title'] : '无标题';
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message']      = $widget_message;
		}
	}

	do_action( 'be_after_import' );

	return apply_filters( 'be_import_results', $results );
}

// 设置页面
function be_add_import_export_page() {

	$page_hook = add_management_page(
		'备份小工具',
		'<span class="bem"></span>备份小工具',
		'edit_theme_options',
		'widget-importer-exporter',
		'widget_import_export_page'
	);
}

add_action('admin_menu', 'be_add_import_export_page');

function widget_import_export_page() {
	global $be_import_results;
	?>
	<div class="wrap">

		<h2>备份小工具</h2>
		<p>导出导入已添加的小工具数据</p>
		<?php
			if ( be_have_import_results() ) {
				be_show_import_results();
				return;
			}
			$self = isset( $_SERVER['PHP_SELF'] ) ? wp_unslash( filter_input( INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL ) ) : '';
			$page = isset( $_GET['page'] ) ? wp_unslash( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL ) ) : '';
		?>

		<h4 class="title">导出小工具</h4>

		<p>
			生成并下载<b>.wie</b>备份文件
		</p>

		<p class="submit">
			<a href="<?php echo esc_url( admin_url( basename( $self ) . '?page=' . $page . '&export=1&be_export_nonce=' . wp_create_nonce( 'be_export' ) ) ); ?>" id="be-export-button" class="button button-primary">
				导出
			</a>
		</p>

		<h4 class="title">导入小工具</h4>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'be_import', 'be_import_nonce' ); ?>
			<textarea name="be_import_data" id="be-import-data" placeholder="用记事本打开导出的.wie文件，复制粘贴数据到此，点击导入！" rows="6" style="width: 50%;padding:10px;"></textarea>
			<?php submit_button( '导入' ); ?>
		</form>

		<?php if ( ! empty( $be_import_results ) ) : ?>
			<p id="be-import-results">
				<?php echo wp_kses_post( $be_import_results ); ?>
			</p>
			<br/>
		<?php endif; ?>

	</div>

	<?php

}

// 要显示导入结果
function be_have_import_results() {
	global $be_import_results;

	if ( ! empty( $be_import_results ) ) {
		return true;
	}

	return false;
}

// 显示导入结果
function be_show_import_results() {
	global $be_import_results;

	$self = isset( $_SERVER['PHP_SELF'] ) ? wp_unslash( filter_input( INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL ) ) : '';
	$page = isset( $_GET['page'] ) ? wp_unslash( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL ) ) : '';
	?>

	<h3 class="title">导入完成
		<span>
			<?php
				printf(
					'<a href="%1$s" style="font-weight: 400;font-size: 14px;padding: 0 5px;text-decoration: none;">管理小工具</a><a href="%2$s" style="font-weight: 400;font-size: 14px;padding: 0 5px;text-decoration: none;">重新导入</a>',
					esc_url( admin_url( 'widgets.php' ) ),
					esc_url( admin_url( basename( $self ) . '?page=' . $page ) )
				);
			?>
		</span>
	</h3>


	<table id="be-import-results">
		<tr>
			<th class="be-import-results-header" style="font-weight: 700;text-align: left;padding: 10px 10px 10px 0;width: 30%;">侧边栏小工具名称</th>
			<th class="be-import-results-header" style="font-weight: 700;text-align: left;padding: 10px 10px 10px 0;width: 30%;">小工具标题</th>
			<th class="be-import-results-header" style="font-weight: 700;text-align: left;padding: 10px 10px 10px 0;width: 30%;">导入状态</th>
		</tr>

		<?php
			$results = $be_import_results;
			foreach ( $results as $sidebar ) :
		?>

		<tr class="be-import-results-sidebar">
			<td colspan="2" class="be-import-results-sidebar-name" style="font-weight: 700;padding: 10px 10px 10px 0;">
				<?php echo esc_html( $sidebar['name'] ); ?>
			</td>
			<td class="be-import-results-sidebar-message be-import-results-message be-import-results-message-<?php echo esc_attr( $sidebar['message_type'] ); ?>" style="font-weight: 700;padding: 10px 0;">
				<?php echo esc_html( $sidebar['message'] ); ?>
			</td>
		</tr>

		<?php foreach ( $sidebar['widgets'] as $widget ) : ?>

			<tr class="be-import-results-widget">
				<td class="be-import-results-widget-name" style="width: 30%;padding: 5px 0 5px 10px;">
					<?php echo esc_html( $widget['name'] ); ?>
				</td>
				<td class="be-import-results-widget-title" style="width: 30%;padding: 5px 0 5px 10px;">
					<?php echo esc_html( $widget['title'] ); ?>
				</td>
				<td class="be-import-results-widget-message be-import-results-message be-import-results-message-<?php echo esc_attr( $widget['message_type'] ); ?>">
					<?php echo esc_html( $widget['message'] ); ?>
				</td>
			</tr>

		<?php endforeach; ?>

			<tr class="be-import-results-space">
				<td colspan="3"></td>
			</tr>

		<?php endforeach; ?>
	</table>

	<?php
}

// 获取可用的小工具
function be_available_widgets() {
	global $wp_registered_widget_controls;

	$widget_controls = $wp_registered_widget_controls;

	$available_widgets = array();

	foreach ( $widget_controls as $widget ) {
		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
			$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
			$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
		}
	}

	return apply_filters( 'be_available_widgets', $available_widgets );
}