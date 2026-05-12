<?php
if ( zm_get_option( 'menu_corner_mark' ) ) {
if ( ! defined( 'ABSPATH' ) ) exit;
// 添加菜单角标自定义字段
add_action( 'wp_nav_menu_item_custom_fields', 'corner_mark_fields', 0, 4 );
function corner_mark_fields( $item_id, $item, $depth, $args ) {
	$mark_text  = get_post_meta( $item_id, 'menu_corner_mark', true );
	$mark_style = get_post_meta( $item_id, 'menu_mark_style', true ) ? : '#da6472';
	$mark_top   = get_post_meta( $item_id, 'menu_mark_top', true ) ? : '11';
	$mark_right = get_post_meta( $item_id, 'menu_mark_right', true ) ? : '5';
	?>

	<div class="mark-btn"><div class="markenu">菜单角标</div></div>
	<div class="mark-box" style="display: none;">
		<div class="description-group">
			<p class="menu-mark description description-thin">
				<label for="menu-item-custom-text-<?php echo esc_attr( $item_id ); ?>">
					角标文字<br>
					<input type="text" id="menu-item-custom-text-<?php echo esc_attr( $item_id ); ?>" class="widefat menu-item-mark-text" style="width: 100%;" name="menu_corner_mark[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $mark_text ); ?>" />
				</label>
			</p>

			<p class="mark-style description description-thin" style="width: 70%;">
				<label for="menu-item-mark-style-<?php echo esc_attr( $item_id ); ?>">角标背景色<br></label>
				<input type="text" name="menu_mark_style[<?php echo esc_attr( $item_id ); ?>]" class="color-mark" value="<?php echo esc_attr( $mark_style ); ?>" style="width: 100%;" />
			</p>
		</div>

		<div class="description-group">
			<p class="menu-mark-top description description-thin" style="float: left;width: 49%;">
				<label for="menu-item-mark-top-<?php echo esc_attr( $item_id ); ?>">
					角标距上<br>
					<input type="number" id="menu-item-mark-top-<?php echo esc_attr( $item_id ); ?>" class="small-text menu-item-mark-top" style="width: 100%;margin: 2px 0 0 0;" name="menu_mark_top[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $mark_top ); ?>" />
				</label>
			</p>

			<p class="menu-mark-right description description-thin" style="float: left;width: 49%;">
				<label for="menu-item-mark-right-<?php echo esc_attr( $item_id ); ?>">
					角标距右<br>
					<input type="number" id="menu-item-mark-right-<?php echo esc_attr( $item_id ); ?>" class="small-text menu-item-mark-right" style="width: 100%;margin: 2px 0 0 0" name="menu_mark_right[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $mark_right ); ?>" />
				</label>
			</p>
		</div>
	</div>
	<?php
}

// 保存角标文字和样式
add_action( 'wp_update_nav_menu_item', 'save_corner_mark_fields', 10, 3 );
function save_corner_mark_fields( $menu_id, $menu_item_id, $args ) {
	if ( isset( $_POST['menu_corner_mark'][$menu_item_id] ) && ! empty( $_POST['menu_corner_mark'][$menu_item_id] ) ) {
		$mark_text = sanitize_textarea_field( $_POST['menu_corner_mark'][$menu_item_id] );
		update_post_meta( $menu_item_id, 'menu_corner_mark', $mark_text );

		if ( isset( $_POST['menu_mark_style'][$menu_item_id] ) ) {
			$mark_style = sanitize_text_field( $_POST['menu_mark_style'][$menu_item_id] );
			update_post_meta( $menu_item_id, 'menu_mark_style', $mark_style );
	    }

	    if ( isset( $_POST['menu_mark_top'][$menu_item_id] ) ) {
			$mark_top = intval( $_POST['menu_mark_top'][$menu_item_id] );
			update_post_meta( $menu_item_id, 'menu_mark_top', $mark_top );
	    }

		if ( isset( $_POST['menu_mark_right'][$menu_item_id] ) ) {
			$mark_right = intval( $_POST['menu_mark_right'][$menu_item_id] );
			update_post_meta( $menu_item_id, 'menu_mark_right', $mark_right );
		}
	} else {
		delete_post_meta( $menu_item_id, 'menu_corner_mark' );
		delete_post_meta( $menu_item_id, 'menu_mark_style' );
		delete_post_meta( $menu_item_id, 'menu_mark_top' );
		delete_post_meta( $menu_item_id, 'menu_mark_right' );
	}
}

// 输出
add_filter( 'walker_nav_menu_start_el', 'display_menu_item_mark', 11, 4 );
function display_menu_item_mark( $item_output, $item, $depth, $args ) {
	$mark_text = get_post_meta( $item->ID, 'menu_corner_mark', true );
	$mark_style = get_post_meta( $item->ID, 'menu_mark_style', true );
	$mark_top = get_post_meta( $item->ID, 'menu_mark_top', true );
	$mark_right = get_post_meta( $item->ID, 'menu_mark_right', true );

	if ( $mark_text ) {
		$mark_html = '<small class="menu-mark" style="background: ' . esc_attr( $mark_style ) . ';top:' . esc_attr( $mark_top ) . 'px;right:' . esc_attr( $mark_right ) . 'px;">' . esc_html( $mark_text ) . '</small>';
		$item_output = preg_replace_callback( '/(<a[^>]*>)(.*?)(<\/a>)/i', function( $matches ) use ( $mark_html ) {
			return $matches[1] . $matches[2] . $mark_html . $matches[3];
		}, $item_output );
	}

	return $item_output;
}

if ( is_admin() && 'nav-menus.php' === $pagenow ) {
	function mark_box() { ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.mark-btn').click(function() {
					var menuBox = $(this).next('.mark-box');
					menuBox.slideToggle();
					$(this).toggleClass('action');
				});
			});
		</script>


		<?php
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".color-mark").wpColorPicker();
			});
		</script>
	<?php }
	add_action( 'admin_head', 'mark_box' );
}
}