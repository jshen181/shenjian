<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Be_editroles {
	public static function init (){
		add_action( 'admin_init', array( __CLASS__, 'Be_add_permission_caps' ) );
		add_action( 'admin_menu', array( __CLASS__, 'Be_UserRoles_menu' ) );
		add_action( 'wp_ajax_Be_delete_role', array( __CLASS__, 'Be_delete_role' ) );
		add_action( 'admin_footer', array( __CLASS__, 'Be_user_permission_js' ) );
	}

	public static function Be_add_permission_caps() {
		$role = get_role( 'administrator' );
		$role->add_cap( 'edit_user_permission' ); 
	}

	public static function Be_UserRoles_menu() {
		add_users_page( '角色管理', '角色管理', 'edit_user_permission', 'role_admin', array( __CLASS__, 'Be_page_user_permission' ) );
	}

	public static function Be_delete_role() {
		$role = sanitize_text_field( $_REQUEST['delete'] );
		if ( ! wp_verify_nonce( $_REQUEST['token'], $role ) ) {
			$result['type'] = "error";
			$result['role_count'] = '无删除权限';
		} elseif ( ! current_user_can( 'edit_user_permission' ) ) {
			$result['type'] = "error";
			$result['role_count'] = '无删除权限';
		} elseif ( $role == 'administrator' ) {
			$result['type'] = "error";
			$result['role_count'] = '无删除权限';
		} elseif ( ! get_role($role)){
			$result['type'] = "error";
			$result['role_count'] = sprintf( __( '没有这个 %s 角色' ), $role );
		} elseif ( get_role( $role ) ) {
			$args2 = array( 'role' => $role );
			$authors = get_users( $args2 );
			if( $authors ) {
				foreach ( $authors as $user ) {
					wp_update_user( array( 'ID' => $user->ID, 'role' => get_option( 'default_role' ) ) );
				}
			}
			remove_role( $role );
			$result['type'] = "success";
		}
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
			$result = json_encode( $result );
			echo $result;
		} else {
			header( "Location: ".$_SERVER["HTTP_REFERER"] );
		}
		die();
	}

	public static function Be_user_permission_js() {
		global $current_screen;
		$current_scre = $current_screen->id ;
		if( 'users_page_user_permission' != $current_scre ) {
			return;
		}
		?>
		<script type="text/javascript">jQuery(document).ready(function(a){a(document).on("click",".delete_user_roless",function(){if(!confirm(commonL10n.warnDelete))return!1;var b=a(this),c=b.data("name"),d=b.data("token"),e=a("#"+c);return a.ajax({type:"post",url:"<?php echo admin_url( 'admin-ajax.php' );?>",dataType:"json",data:{action:"Be_delete_role",token:d,delete:c},success:function(b){"success"==b.type?e.remove():a("#ajax-response").html( '<div class="error notice"><p><strong>'+b.role_count+"</strong></p></div>")}}),!1}),a(".toggle-all-terms").on("change",function(){a("#rolechecklist").closest("ul").find(":checkbox").prop("checked",this.checked)})});</script>
		<?php 
	}

	public static function Be_get_rolecaps( $role ) {
		$caps = array();
		$role_obj = get_role( $role );
		if ( $role_obj && isset( $role_obj->capabilities ) )
		$caps = $role_obj->capabilities;
		return $caps;
	}

	public static function Be_get_role( $name, $role, $default = false ) {
		$options = Be_editroles::Be_get_rolecaps( $role );
		// 返回相应选项
		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}
		return $default;
	}

	public static function UR_add_role( $role, $name, $caps ) {
		global $wp_user_roles;
		$string = preg_replace( '/\s+/', '', $role );
		$role_obj = get_role( $string );
			if ( ! current_user_can( 'edit_user_permission' ) ) {
				return false;
			} elseif ( ! $role_obj ) {
			$capabilities = array();
			foreach ( (array) $caps as $cap ) {
				$capabilities[ $cap ] = true;
			}
			$result= add_role( $string, $name, $capabilities );
			if ( null !== $result ) {
				return true;
			}
			else {
				return false;
			}
			if ( ! isset( $wp_user_roles[ $string ] ) ) {
				$wp_user_roles[ $string ] = array(
					'name' => $name,
					'capabilities' => $capabilities,
				);
			}
			eg_refresh_current_user_caps( $string );
		} else {
			return false;
		}
	}

	public static function UR_merge_rolecaps( $role, $caps ) {
		global $wp_user_roles , $wp_roles;
		$role_obj = get_role( $role );
		//禁止修改管理员 if ( ! current_user_can( 'edit_user_permission' ) || $role == 'administrator' ) {
		if ( ! current_user_can( 'edit_user_permission' ) ) {
			return false;
		} elseif ( ! $role_obj )
			return false;
		 $capabilities = array();
		foreach ( ( array ) $caps as $cap ) {
			$capabilities[ $cap ] = true;
		}
		$current_caps = Be_editroles::Be_get_rolecaps( 'administrator' );
		foreach ( $current_caps as $capremove => $value ) {
			if ( isset( $capabilities[$capremove] ) ){
				 $role_obj->add_cap($capremove);
			} else {
				$role_obj->remove_cap($capremove);
			}
		}
		if ( isset( $wp_user_roles[ $role ] ) ) {
			$wp_user_roles[ $role ] = array(
				'capabilities' => $caps
			);
		}
		Be_editroles::UR_refresh_usercaps( $role );
	}

	public static function UR_refresh_usercaps( $role ) {
		if ( is_user_logged_in() && current_user_can( $role ) ) {
			wp_get_current_user()->get_role_caps();
		}
	}

	public static function Be_list_cps () {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();
		// 统计角色数量
		$result = count_users();
		$role_counts = $result['avail_roles'];

		$roles = $wp_roles->get_names();
		foreach ( $roles as $role_value => $role_name ) { ?>
			<tr id="<?php echo $role_value; ?>" class="iedit <?php echo $role_value; ?> ">
				<td class="title column-title">
					<strong><a href="users.php?page=role_admin&edit&user_role=<?php echo $role_value; ?>"><?php echo _x( $role_name, 'User role' ); ?></a></strong>
				</td>
				<td class="slug column-slug">
					<?php echo $role_value; ?>
				</td>
				<td class="column-role">
					<a href="users.php?page=role_admin&edit&user_role=<?php echo $role_value; ?>">编辑</a> |
					<a class="delete_user_roless" href="<?php echo admin_url( 'admin-ajax.php?action=Be_delete_role&delete='.$role_value.'&token='.wp_create_nonce( $role_value ) ); ?>" data-token="<?php echo wp_create_nonce( $role_value ); ?>">删除</a>
				</td>
				<td class="slug column-slug" style="width: 50px;text-align: center;">
					<?php 
						echo isset( $role_counts[$role_value] ) ? $role_counts[$role_value] : 0; 
					?>
				</td>
			</tr>
		<?php 
		}
	}

	public static function Be_page_user_permission() { 
		global $wp_roles;
		if ( ! current_user_can( 'edit_user_permission' ) ) {
			wp_die(
				'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
				'<p>无管理权限</p>',
				403
			);
		}

		if ( isset( $_POST['addroless'] ) ) {
			$roless_id = sanitize_text_field( $_POST['roless_id'] );
			$roless_name = sanitize_text_field( $_POST['roless_name'] );
			$permission = isset( $_POST['permission'] ) ? (array) $_POST['permission'] : array();
			$permission = array_map( 'esc_attr', $permission );
			if ( ! wp_verify_nonce( $_POST['authenticity_token'], 'add_user_roless' ) ) {
				$eg_error = '无删除权限';
			} elseif ( ! current_user_can( 'edit_user_permission' ) ) {
				$eg_error = '无删除权限';
			} elseif ( empty( $roless_name ) ) {
				$eg_error = '请填写名称';
			} elseif ( empty( $roless_id ) ) {
				$eg_error = '请填写角色ID';
			} elseif ( empty( $permission ) ) {
				$eg_error = '至少勾选一个权限';
			} else {
				$UR_add_roles = Be_editroles::UR_add_role( $roless_id, $roless_name, $permission );
				if( $UR_add_roles === false ) {
					$eg_error = '添加失败';
				} else {
					$eg_success = '添加成功';
				}
			}
		}

		if ( isset( $_POST['editrole'] ) ){
			$idrole = sanitize_text_field( $_POST['idrole'] );
			$new_name = sanitize_text_field( $_POST['name'] );
			$permission = isset( $_POST['permission'] ) ? (array) $_POST['permission'] : array();
			$permission = array_map( 'esc_attr', $permission );
			if ( ! wp_verify_nonce( $_POST['authenticity_token'], 'editrole-'.$idrole ) ) {
				$eg_error = '无删除权限';
			} elseif (! current_user_can( 'edit_user_permission' ) ) {
				$eg_error = '无删除权限';
			} elseif ( empty($idrole ) ) {
				$eg_error = '更新失败';
			} elseif (empty($permission ) ) {
				$eg_error = '更新失败';
			} else {
				$wp_roles->roles[$idrole]['name'] = $new_name;
				$UR_merge_role = Be_editroles::UR_merge_rolecaps( $idrole, $permission );
				if ( $UR_merge_role === false ) {
					$eg_error = '更新失败';
				} else {
					$eg_success = '更新成功';
				}
			}
		}
	?>

	<div class="wrap nosubsub">
		<h1 class="wp-heading-inline">
			<?php 
				if( isset( $_GET['edit'] ) ) {
					echo '编辑角色';
					echo '<a href="' . home_url() . '/wp-admin/users.php?page=role_admin" class="page-title-action">角色管理</a>';
				} else {
					echo '角色管理';
				}
			?>
		</h1>

		<?php if ( ! empty( $eg_error ) ) { ?>
			<div id="message" class="error notice is-dismissible">
				<p><strong><?php echo $eg_error; ?></strong></p>
			</div>
		<?php } ?>
		<?php if (! empty( $eg_success ) ) { ?>
			<div id="message" class="updated notice is-dismissible">
				<p><strong><?php echo $eg_success; ?></strong></p>
			</div>
		<?php } ?>

		<div id="ajax-response"></div>
			<div class="tablenav top">
				<div id="col-container" class="wp-clearfix">
					<?php 
						if( isset( $_GET['edit'] ) ) {
							Be_editroles::UR_user_page_edit();
						} else {
							Be_editroles::UR_user_page_default();
						}
					?>
				</div>
			</div>
		</div>
		<?php
		}

		public static function UR_user_page_default() { ?>
			<div id="col-left">
				<div class="col-wrap">
					<div class="form-wrap">
						<h2>添加角色</h2>
						<form method="post">
							<input type="hidden" name="authenticity_token" id="authenticity_token" value="<?php echo wp_create_nonce( 'add_user_roless' ); ?>" />
							<div class="form-field form-required term-name-wrap">
								<label for="roless_name">名称</label>
								<input name="roless_name" id="roless_name" size="40" aria-required="true" type="text">
								<p class="description">用于前端显示</p></td>
							</div>
							<div class="form-field form-required term-name-wrap">
								<label for="roless_id">角色 <small>( ID )</small></label>
								<input name="roless_id" id="roless_id" size="40" aria-required="true" type="text">
								<p>仅限英文字母，不可与其他角色相同</p>
							</div>
							<div id="Capabilities" class="taxonomydiv">
								<label for="rolechecklist">选择权限（默认为作者）</label>
								<div class="tabs-panel">
									<ul id="rolechecklist" class="form-no-clear">
										<?php
											$caps = Be_editroles::Be_get_rolecaps( 'administrator' );
											foreach ( $caps as $key => $value ):
												$default_checked_permissions = array( 'upload_files', 'edit_posts', 'edit_published_posts', 'publish_posts', 'read', 'level_2', 'level_1', 'level_0', 'delete_posts', 'delete_published_posts' );
												$checked = in_array( $key, $default_checked_permissions ) ? 'checked="checked"' : '';
										?>
										<li>
											<label>
												<input name="permission[]" value="<?php echo $key; ?>" type="checkbox" <?php echo $checked; ?>>
												<?php echo $key; ?>
											</label>
										</li>
										<?php endforeach; ?>
									</ul>
								</div>

								<p class="button-controls wp-clearfix">
									<span class="list-controls">
										<label class="select-all"><input type="checkbox" class="toggle-all-terms"/>全选</label>
									</span>
								</p>
							</div>

							<p>参考默认角色，授予相应的权限，<a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">权限说明</a></p>

							<br />
							<p class="submit">
								<input name="addroless" id="addroless" class="button button-primary" value="添加" type="submit">
							</p>
						</form>
					</div>
				</div>
			</div>

			<div id="col-right">
				<div class="col-wrap">
					<div class="tablenav top">
				</div>
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<th scope="col" id="title" class="name manage-column column-text">
								<span>名称</span><br>
							</th>
							<th scope="col" id="slug" class="manage-column column-text">
								<span>角色 <small>( ID )</small></span>
							</th>
							<th scope="col" id="Action" class="manage-column column-role">操作</th>
							<th scope="col" id="quantity" class="manage-column" style="width: 50px;text-align: center;">
								<span>成员</span>
							</th>
						</tr>
					</thead>

					<tbody id="the-list">
						<?php Be_editroles::Be_list_cps(); ?>
					</tbody>

					<tfoot>
						<tr>
							<th scope="col" id="title" class="name manage-column column-text">
								<span>名称</span><br>
							</th>
							<th scope="col" id="slug" class="manage-column column-text">
								<span>角色 <small>( ID )</small></span>
							</th>
							<th scope="col" id="Action" class="manage-column column-role">操作</th>
							<th scope="col" id="quantity" class="manage-column column-quantity" style="width: 50px;text-align: center;">
								<span>成员</span>
							</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	<?php
	}

	public static function UR_user_page_edit() {
		global $wp_roles;
		$role = $_GET['user_role'];
		?>

		<form name="editrole" id="editrole" method="post" class="validate">
			<input type="hidden" name="authenticity_token" id="authenticity_token" value="<?php echo wp_create_nonce( 'editrole-'.$role ); ?>" />
			<input type="hidden" name="idrole" id="idrole" value="<?php echo $role; ?>" />
			<table class="form-table">
				<tbody>
					<tr class="form-field form-required role-name-wrap">
						<th scope="row"><label for="name">名称</label></th>
						<td>
							<input id="name" name="name" value="<?php echo esc_attr( $wp_roles->roles[ $role ]['name'],'User role' ); ?>" size="40" aria-required="true" type="text" style="width: 95%;">
						</td>
					</tr>
					<tr class="form-field role-Capabilities-wrap">
						<th scope="row"><label>权限</label></th>
						<td>
							<div id="Capabilities" class="taxonomydiv" style="width: 95%;">
								<div class="tabs-panel">
									<ul id="rolechecklist" class="form-no-clear">
										<?php $caps = Be_editroles::Be_get_rolecaps( 'administrator' );
										foreach ( $caps as $key => $value ): ?>
										<li>
											<label>
												<input name="permission[]" value="<?php echo $key; ?>" <?php if( Be_editroles::Be_get_role( $key, $role ) == 1 ){ echo'checked="checked"';} ?> type="checkbox"> <?php echo $key; ?>
											</label>
										</li>
										<?php endforeach; ?>
									</ul>
								</div>
								<p class="button-controls wp-clearfix">
									<span class="list-controls">
										<label class="select-all"><input type="checkbox" class="toggle-all-terms"/>全选</label>
									</span>
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
			<input name="editrole" id="editrole" class="button button-primary" value="更新" type="submit"></p>
		</form>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label>作者权限参考</label></th>
					<td>
						<div class="taxonomydiv">
							<strong>勾选：</strong>
							<ul>
								<li>upload_files （允许上传图片附件）</li>
								<li>edit_posts （允许编辑文章）</li>
								<li>edit_published_posts （允许编辑已发表的文章）</li>
								<li>publish_posts （允许发表文章）</li>
								<li>read （允许查看网站公开的内容）</li>
								<li>level_2 （作者）</li>
								<li>level_1 （贡献者）</li>
								<li>level_0 （订阅者）</li>
								<li>delete_posts （允许删除文章）</li>
								<li>delete_published_posts （允许删除已发表的文章）</li>
							</ul>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label>贡献者权限参考</label></th>
					<td>
						<div class="taxonomydiv">
							<strong>勾选：</strong>
							<ul>
								<li>edit_posts</li>
								<li>read</li>
								<li>level_1</li>
								<li>level_0</li>
								<li>delete_posts</li>
							</ul>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	<?php }
}

Be_editroles::init();