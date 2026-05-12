<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( zm_get_option( 'search_history' ) ) {
	// 创建数据库表
	function be_create_search_history_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'be_search_history';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			search_term varchar(255) NOT NULL,
			search_time datetime NOT NULL,
			count int(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (ID)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	add_action( 'after_switch_theme', 'be_create_search_history_table' );

	// 处理搜索请求
	function be_process_search_request() {
		// 检查是否是AJAX请求
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// 检查是否是其他类型的AJAX请求
		if ( ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) || ( isset( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'ajax' ) !== false ) ) {
			return;
		}

		// 如果启用了搜索验证码，检查验证码是否通过
		if ( zm_get_option( 'search_captcha' ) ) {
			if ( ! session_id() ) {
				session_start();
			}
			$captcha_cookie = zm_get_option( 'search_captcha_cookie' );
			if ( ! isset( $_SESSION[ $captcha_cookie ] ) ) {
				return;
			}
		}

		$request_uri   = $_SERVER['REQUEST_URI'];
		$exclude_paths = array( '/avatar/' ); // 定义需要排除的路径

		foreach ( $exclude_paths as $exclude_path ) {
			if ( strpos( $request_uri, $exclude_path ) !== false ) {
				return; // 如果请求 URI 包含需要排除的路径，则直接返回
			}
		}

		if ( isset( $_GET['s'] ) ) {
			$search_term = sanitize_text_field( $_GET['s'] );
			be_save_search_history( $search_term );
			return; // 只记录一次搜索历史
		}

		$search_path = '/search/';

		if ( strpos( $request_uri, $search_path ) !== false ) {
			$search_terms = explode( '/', trim( parse_url( $request_uri, PHP_URL_PATH ), '/' ) );
			$search_key   = array_search( 'search', $search_terms );
			if ( $search_key !== false && isset( $search_terms[ $search_key + 1 ] ) ) {
				$search_term = sanitize_text_field( urldecode( $search_terms[ $search_key + 1 ] ) );
				be_save_search_history( $search_term );
				return;
			}
		}
	}

	if ( ! current_user_can( 'administrator' ) ) {
		add_action( 'template_redirect', 'be_process_search_request' );
	}

	// 保存搜索记录到数据库
	function be_save_search_history( $search_term ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';

		// 使用静态变量缓存表检查结果
		static $table_exists = null;

		// 只在第一次调用时检查表是否存在
		if ( $table_exists === null ) {
			$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name;
			if ( ! $table_exists ) {
				be_create_search_history_table();
				$table_exists = true;
			}
		}

		// 检查是否已存在相同的搜索记录
		$existing_record = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE search_term = %s",
				$search_term
			)
		);

		if ( $existing_record ) {
			// 如果已存在相同的搜索记录，则更新计数
			$wpdb->update(
				$table_name,
				array(
					'search_time' => current_time( 'mysql' ),
					'count'       => $existing_record->count + 1,
				),
				array( 'ID' => $existing_record->ID ),
				array( '%s', '%d' ),
				array( '%d' )
			);
		} else {
			// 如果不存在相同的搜索记录，则插入新记录
			$wpdb->insert(
				$table_name,
				array(
					'search_term' => $search_term,
					'search_time' => current_time( 'mysql' ),
					'count'       => 1,
				),
				array( '%s', '%s', '%d' )
			);
		}
	}

	// 处理搜索记录删除请求
	function be_process_search_history_delete() {
		// 处理批量删除
		if ( isset( $_POST['delete_selected'] ) && isset( $_POST['search_ids'] ) ) {
			$ids = array_map( 'intval', $_POST['search_ids'] );
			foreach ( $ids as $id ) {
				be_delete_search_history_item( $id );
			}
			wp_redirect( home_url() . '/wp-admin/tools.php?page=search-log' );
			exit;
		}

		// 处理单个删除和清理全部
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_search_history' ) {
			be_delete_all_search_history();
		} elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_search_history_item' && isset( $_GET['search_id'] ) ) {
			$search_id = intval( $_GET['search_id'] );
			be_delete_search_history_item( $search_id );
		}
	}
	add_action( 'init', 'be_process_search_history_delete' );

	// 删除所有搜索记录
	function be_delete_all_search_history() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';
		$wpdb->query( "DELETE FROM $table_name" );
		// 重定向到搜索历史页面或其他适当的位置
		wp_redirect( home_url() . '/wp-admin/tools.php?page=search-log' );
		exit;
	}

	// 删除单个搜索记录
	function be_delete_search_history_item( $search_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';

		$wpdb->delete(
			$table_name,
			array( 'ID' => $search_id ),
			array( '%d' )
		);

		// 可以选择重定向到搜索历史页面或其他适当的位置
		wp_redirect( home_url() . '/wp-admin/tools.php?page=search-log' );
		exit;
	}

	// 获取搜索记录
	function be_get_search_history( $limit = 10, $title = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';

		// 获取当前的排序和时间筛选
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date_time';
		$order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc';
		$filter  = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : '';

		// 验证排序字段和顺序
		$allowed_orderby = array( 'date_time', 'total_count' );
		$allowed_order   = array( 'asc', 'desc' );
		$orderby         = in_array( $orderby, $allowed_orderby ) ? $orderby : 'date_time';
		$order           = in_array( $order, $allowed_order ) ? $order : 'desc';

		// 获取当前页码
		$paged    = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
		$per_page = $limit;
		$offset   = ( $paged - 1 ) * $per_page;

		// 构建时间筛选条件
		$where_clause = '';
		switch ( $filter ) {
			case 'today':
				$where_clause = 'WHERE DATE(search_time) = CURDATE()';
				break;
			case 'yesterday':
				$where_clause = 'WHERE DATE(search_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
				break;
			case 'thisweek':
				$where_clause = 'WHERE YEARWEEK(search_time) = YEARWEEK(NOW())';
				break;
			case 'thismonth':
				$where_clause = "WHERE DATE_FORMAT(search_time, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
				break;
		}

		// 获取总记录数
		$total_keywords = $wpdb->get_var(
			"SELECT COUNT(DISTINCT search_term) FROM $table_name $where_clause"
		);

		// 计算总页数
		$total_pages = ceil( $total_keywords / $per_page );

		// 获取分页数据
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, search_term, SUM(count) AS total_count, DATE_FORMAT(search_time, '%%Y-%%m-%%d %%H:%%i:%%s') AS date_time
		            FROM $table_name
		            $where_clause
		            GROUP BY search_term
		            ORDER BY $orderby $order
		            LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		// 生成 HTML 输出
		$html = '<div class="wrap recently-searches">';
		if ( $title ) {
			$html .= '<h2>' . esc_html( $title ) . '</h2>';
		}

		// 添加表单开始标签
		$html .= '<form method="post">';

		// 添加筛选按钮
		$html .= '<div class="tablenav top">';
		$html .= '<div class="alignleft actions">';
		// 筛选按钮时总是重置到第一页
		$html .= '<a href="' . esc_url(
			add_query_arg(
				array(
					'filter' => '',
					'paged'  => 1,
				)
			)
		) . '" class="button' . ( $filter === '' ? ' button-primary' : '' ) . '">全部</a> ';
		$html .= '<a href="' . esc_url(
			add_query_arg(
				array(
					'filter' => 'today',
					'paged'  => 1,
				)
			)
		) . '" class="button' . ( $filter === 'today' ? ' button-primary' : '' ) . '">今天</a> ';
		$html .= '<a href="' . esc_url(
			add_query_arg(
				array(
					'filter' => 'yesterday',
					'paged'  => 1,
				)
			)
		) . '" class="button' . ( $filter === 'yesterday' ? ' button-primary' : '' ) . '">昨天</a> ';
		$html .= '<a href="' . esc_url(
			add_query_arg(
				array(
					'filter' => 'thisweek',
					'paged'  => 1,
				)
			)
		) . '" class="button' . ( $filter === 'thisweek' ? ' button-primary' : '' ) . '">本周</a> ';
		$html .= '<a href="' . esc_url(
			add_query_arg(
				array(
					'filter' => 'thismonth',
					'paged'  => 1,
				)
			)
		) . '" class="button' . ( $filter === 'thismonth' ? ' button-primary' : '' ) . '">本月</a>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<p class="searches-title recently-searches-title">' . ( $total_keywords ? $total_keywords : '0' ) . ' 个关键词</p>';

		// 表格头
		$html .= '<table class="wp-list-table widefat fixed striped searches-table">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th scope="col" class="check-column" style="width: 30px; padding: 8px 0 0 3px; vertical-align: top;"><input type="checkbox" id="cb-select-all"></th>';
		$html .= '<th scope="col">关键词</th>';
		$html .= '<th scope="col" class="sorted" style="width: 120px;"><a href="' . esc_url(
			add_query_arg(
				array(
					'orderby' => 'total_count',
					'order'   => $orderby == 'total_count' && $order == 'asc' ? 'desc' : 'asc',
				)
			)
		) . '">搜索次数</a></th>';
		$html .= '<th scope="col" class="sorted"><a href="' . esc_url(
			add_query_arg(
				array(
					'orderby' => 'date_time',
					'order'   => $orderby == 'date_time' && $order == 'asc' ? 'desc' : 'asc',
				)
			)
		) . '">最后搜索时间</a></th>';
		$html .= '<th scope="col" style="width: 60px;">操作</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		if ( $results ) {
			$count = $offset + 1; // 序号从当前页的第一个记录开始
			foreach ( $results as $result ) {
				$html .= '<tr>';
				$html .= '<td class="check-column" style="width: 30px; padding: 5px 0 0 10px; vertical-align: top;"><input type="checkbox" name="search_ids[]" value="' . esc_attr( $result->ID ) . '"></td>';
				$html .= '<td><a href="' . home_url( '/' ) . '?s=' . esc_html( $result->search_term ) . '" target="_blank">' . esc_html( $result->search_term ) . '</a></td>';
				$html .= '<td>' . esc_html( $result->total_count ) . '</td>';
				$html .= '<td>' . esc_html( $result->date_time ) . '</td>';
				$html .= '<td><a href="' . esc_url(
					add_query_arg(
						array(
							'action'    => 'delete_search_history_item',
							'search_id' => $result->ID,
						)
					)
				) . '">删除</a></td>';
				$html .= '</tr>';
				++$count;
			}
		} else {
			$html .= '<tr>';
			$html .= '<td></td>';
			$html .= '<td colspan="1">无记录</td>';
			$html .= '<td></td>';
			$html .= '<td></td>';
			$html .= '<td></td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '<tfoot>';
		$html .= '<tr>';
		$html .= '<th scope="col" class="check-column" style="width: 30px; padding: 8px 0 0 3px; vertical-align: top;"><input type="checkbox" id="cb-select-all-2"></th>';
		$html .= '<th scope="col">关键词</th>';
		$html .= '<th scope="col" class="sorted" style="width: 120px;"><a href="' . esc_url(
			add_query_arg(
				array(
					'orderby' => 'total_count',
					'order'   => $orderby == 'total_count' && $order == 'asc' ? 'desc' : 'asc',
				)
			)
		) . '">搜索次数</a></th>';
		$html .= '<th scope="col" class="sorted"><a href="' . esc_url(
			add_query_arg(
				array(
					'orderby' => 'date_time',
					'order'   => $orderby == 'date_time' && $order == 'asc' ? 'desc' : 'asc',
				)
			)
		) . '">最后搜索时间</a></th>';
		$html .= '<th scope="col" style="width: 60px;">操作</th>';
		$html .= '</tr>';
		$html .= '</tfoot>';
		$html .= '</table>';

		// 分页导航
		if ( $total_pages > 1 ) {
			$html .= '<div class="tablenav-pages">';
			$html .= '<span class="pagination-links">';
			for ( $i = 1; $i <= $total_pages; $i++ ) {
				$active = ( $i == $paged ) ? 'active' : '';
				// 保持当前的筛选条件和排序参数
				$pagination_args = array(
					'paged' => $i,
				);
				if ( $filter ) {
					$pagination_args['filter'] = $filter;
				}
				if ( $orderby ) {
					$pagination_args['orderby'] = $orderby;
				}
				if ( $order ) {
					$pagination_args['order'] = $order;
				}
				$html .= '<a class="button ' . $active . '" href="' . esc_url( add_query_arg( $pagination_args ) ) . '">' . $i . '</a> ';
			}
			$html .= '</span>';
			$html .= '</div>';
		}

		$html .= '<div class="tablenav bottom">';
		$html .= '<div class="alignleft actions">';
		$html .= '<input type="submit" name="delete_selected" class="button action" value="删除所选项">';
		$html .= '</div>';
		$html .= '<a class="button" href="' . esc_url( add_query_arg( 'action', 'delete_search_history' ) ) . '" class="recently-searches-del">删除全部</a>';
		$html .= '</div>';
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}

	// 添加菜单
	function display_search_log() {
		echo be_get_search_history( zm_get_option( 'search_history_n' ), '搜索记录' );
	}

	function add_search_log_page() {
		add_management_page( '搜索记录', '<span class="bem"></span>搜索记录', 'manage_options', 'search-log', 'display_search_log' );
	}

	add_action( 'admin_menu', 'add_search_log_page' );

	// 热门搜索
	function be_hot_search( $limit = 10, $title = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';

		// 检查表是否存在
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			return '';
		}

		$results = $wpdb->get_results(
			"SELECT search_term, SUM(count) AS total_count FROM $table_name GROUP BY search_term ORDER BY total_count DESC LIMIT $limit"
		);

		$html = '<div class="recently-searches">';
		if ( $title ) {
			$html .= '<h3 class="searches-title recently-searches-title">' . esc_html( $title ) . '</h3>';
		}
		$html .= '<ul class="recently-searches">';
		foreach ( $results as $result ) {
			$html .= '<li class="search-item"><a href="' . home_url( '/' ) . '?s=' . esc_html( $result->search_term ) . '">' . esc_html( $result->search_term ) . '</a></li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}

	// AJAX加载热门搜索记录
	function load_hot_search_list() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'be_search_history';
		// 添加表存在检查
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			wp_die();
			return;
		}
		$limit   = zm_get_option( 'search_hot_n' );
		$results = $wpdb->get_results(
			"SELECT search_term, SUM(count) AS total_count FROM $table_name GROUP BY search_term ORDER BY total_count DESC LIMIT $limit"
		);

		$html  = '<div class="recently-searches">';
		$html .= '<h3 class="searches-title recently-searches-title">' . sprintf( __( '热门搜索', 'begin' ) ) . '</h3>';

		$html .= '<ul class="recently-searches">';
		if ( $results ) {
			foreach ( $results as $result ) {
				$html .= '<li class="search-item"><a href="' . home_url( '/' ) . '?s=' . esc_html( $result->search_term ) . '">' . esc_html( $result->search_term ) . '</a></li>';
			}
		}
		$html .= '</ul>';
		$html .= '</div>';
		echo $html;
		wp_die();
	}
	add_action( 'wp_ajax_search_hot_list', 'load_hot_search_list' );
	add_action( 'wp_ajax_nopriv_search_hot_list', 'load_hot_search_list' );
}

// 在页面底部添加JavaScript
add_action( 'admin_footer', 'be_search_history_scripts' );
function be_search_history_scripts() {
	?>
	<script>
	jQuery(document).ready(function($) {
		$('#cb-select-all').on('change', function() {
			$('input[name="search_ids[]"]').prop('checked', $(this).prop('checked'));
		});
	});
	</script>
	<?php
}
