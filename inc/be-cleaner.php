<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 清理冗余
add_action( 'admin_menu', 'be_cleaner_admin' );
function be_cleaner_admin() {
	add_management_page(
		'清理冗余',
		'<span class="bem"></span>清理冗余',
		'manage_options',
		'be_cleaner',
		'be_cleaner_page'
	);
}

function be_cleaner_page() {
	function be_cleaner( $type ) {
		global $wpdb;
		switch ( $type ) {
			case 'revision':
				$ru_sql = "DELETE FROM $wpdb->posts WHERE post_type = 'revision'";
				$wpdb->query( $ru_sql );
				break;
			case 'draft':
				$ru_sql = "DELETE FROM $wpdb->posts WHERE post_status = 'draft'";
				$wpdb->query( $ru_sql );
				break;
			case 'autodraft':
				$ru_sql = "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'";
				$wpdb->query( $ru_sql );
				break;
			case 'moderated':
				$ru_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = '0'";
				$wpdb->query( $ru_sql );
				break;
			case 'spam':
				$ru_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'";
				$wpdb->query( $ru_sql );
				break;
			case 'trash':
				$ru_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'";
				$wpdb->query( $ru_sql );
				break;
			case 'postmeta':
				$ru_sql = "DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
				$wpdb->query( $ru_sql );
				break;
			case 'commentmeta':
				$ru_sql = "DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)";
				$wpdb->query( $ru_sql );
				break;
			case 'relationships':
				$ru_sql = "DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)";
				$wpdb->query( $ru_sql );
				break;
			case 'feed':
				$ru_sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'";
				$wpdb->query( $ru_sql );
				break;
		}
	}

	function be_cleaner_count( $type ) {
		global $wpdb;
		switch ( $type ) {
			case 'revision':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'draft':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'autodraft':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'moderated':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'spam':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'trash':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'postmeta':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'commentmeta':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'relationships':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)";
				$count  = $wpdb->get_var( $ru_sql );
				break;
			case 'feed':
				$ru_sql = "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'";
				$count  = $wpdb->get_var( $ru_sql );
				break;
		}
		return $count;
	}

	function be_cleaner_optimize() {
		global $wpdb;
		$tables = $wpdb->get_col( 'SHOW TABLES FROM `' . DB_NAME . '`' );
		foreach ( $tables as $table ) {
			$wpdb->query( 'OPTIMIZE TABLE ' . $table );
		}
	}

		$ru_message = '';

		// 数组
		$clean_types = array(
			'revision'      => '所有修订已删除',
			'draft'         => '所有草稿已删除',
			'autodraft'     => '所有自动草稿已删除',
			'moderated'     => '所有待审核评论已删除',
			'spam'          => '所有垃圾评论已删除',
			'trash'         => '所有垃圾评论已删除',
			'postmeta'      => '所有无关联的文章信息已删除',
			'commentmeta'   => '所有无关联的评论信息已删除',
			'relationships' => '所有无关联的信息已删除',
			'feed'          => '所有仪表盘消息已删除',
		);

		// 处理单个清理操作
		foreach ( $clean_types as $type => $message ) {
			if ( isset( $_POST[ 'be_cleaner_' . $type ] ) ) {
				be_cleaner( $type );
				$ru_message = $message;
				break;
			}
		}

		// 处理全部清理操作
		if ( isset( $_POST['be_cleaner_all'] ) ) {
			foreach ( $clean_types as $type => $message ) {
				be_cleaner( $type );
			}
			$ru_message = '所有不需要的数据已删除';
		}

		if ( $ru_message != '' ) {
			echo '<div id="message" class="updated"><p><strong>' . $ru_message . '</strong></p></div>';
		}
		?>

	<div class="wrap">
		<h2>清理冗余</h2>
		<p>用于清理冗余的数据，操作不可逆，请提前做好数据备份！</p>
		<table class="widefat">
			<thead>
				<tr>
					<th style="width: 60px;">计数</th>
					<th style="width: 220px;">类型</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr class="alternate">
					<td class="column-name"><?php echo be_cleaner_count( 'revision' ); ?></td>
					<td class="column-name">修订</td>
					<td class="column-name">
						<?php render_cleaner_form( 'revision', be_cleaner_count( 'revision' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="column-name"><?php echo be_cleaner_count( 'autodraft' ); ?></td>
					<td class="column-name">自动草稿</td>
					<td class="column-name">
						<?php render_cleaner_form( 'autodraft', be_cleaner_count( 'autodraft' ) ); ?>
					</td>
				</tr>
				<tr class="alternate">
					<td class="column-name"><?php echo be_cleaner_count( 'draft' ); ?></td>
					<td class="column-name">草稿（酌情删除）</td>
					<td class="column-name">
						<?php render_cleaner_form( 'draft', be_cleaner_count( 'draft' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="column-name"><?php echo be_cleaner_count( 'moderated' ); ?></td>
					<td class="column-name">待审核评论（酌情删除）</td>
					<td class="column-name">
						<?php render_cleaner_form( 'moderated', be_cleaner_count( 'moderated' ) ); ?>
					</td>
				</tr>
				<tr class="alternate">
					<td class="column-name"><?php echo be_cleaner_count( 'spam' ); ?></td>
					<td class="column-name">垃圾评论</td>
					<td class="column-name">
						<?php render_cleaner_form( 'spam', be_cleaner_count( 'spam' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="column-name"><?php echo be_cleaner_count( 'trash' ); ?></td>
					<td class="column-name">垃圾邮件评论</td>
					<td class="column-name">
						<?php render_cleaner_form( 'trash', be_cleaner_count( 'trash' ) ); ?>
					</td>
				</tr>
				<tr class="alternate">
					<td class="column-name"><?php echo be_cleaner_count( 'postmeta' ); ?></td>
					<td class="column-name">无关联的文章信息</td>
					<td class="column-name">
						<?php render_cleaner_form( 'postmeta', be_cleaner_count( 'postmeta' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="column-name"><?php echo be_cleaner_count( 'commentmeta' ); ?></td>
					<td class="column-name">无关联的评论信息</td>
					<td class="column-name">
						<?php render_cleaner_form( 'commentmeta', be_cleaner_count( 'commentmeta' ) ); ?>
					</td>
				</tr>
				<tr class="alternate">
					<td class="column-name"><?php echo be_cleaner_count( 'relationships' ); ?></td>
					<td class="column-name">无关联的信息</td>
					<td class="column-name">
						<?php render_cleaner_form( 'relationships', be_cleaner_count( 'relationships' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="column-name"><?php echo be_cleaner_count( 'feed' ); ?></td>
					<td class="column-name">仪表盘消息</td>
					<td class="column-name">
						<?php render_cleaner_form( 'feed', be_cleaner_count( 'feed' ) ); ?>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th>计数</th>
					<th>类型</th>
					<th>操作</th>
				</tr>
			</tfoot>
		</table>
		</p>
	</div>
	<?php
}

// 添加通用表单渲染函数
function render_cleaner_form( $type, $count ) {
	if ( $count > 0 ) {
		?>
		<form action="" method="post" style="margin:0;padding:0;display:inline;">
			<input type="hidden" name="be_cleaner_<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $type ); ?>" />
			<input type="submit" value="删除" style="background:none;border:none;color:#0073aa;cursor:pointer;padding:0 15px 0 0;margin:0;" />
		</form>
		<?php
	}
}
