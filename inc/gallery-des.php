<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 图文模块
function plchldr( $placeholder ) {
	return 'placeholder="' . esc_attr( $placeholder ) . '" onfocus="this.placeholder=\'\'" onblur="this.placeholder=\'' . esc_attr( $placeholder ) . '\'"';
}

// 加载保存
if ( cx_get_option( 'gallery_meta' ) ) {
	if ( ! cx_get_option( 'gallery_box' ) || current_user_can( 'manage_options' ) ) {
		add_action( 'admin_init', 'add_gallery_meta_boxes', 1 );
		add_action( 'save_post', 'gallery_meta_box_save' );
	}
}


// 添加图文模块的元数据框
function add_gallery_meta_boxes() {
	$post_types     = get_post_types( array( 'public' => true ) );
	$excluded_types = array( 'video', 'tao', 'sites', 'bulletin', 'question' ); // 要排除的类型

	foreach ( $post_types as $post_type ) {
		if ( ! in_array( $post_type, $excluded_types ) ) {
			add_meta_box(
				'gallery-fields',
				'图文模块',
				'gallery_meta_box_display',
				$post_type,
				'normal',
				'high'
			);
		}
	}
}

/**
 * 元数据框字段内容
 */
function gallery_meta_box_display() {
	global $post;

	// 添加媒体上传脚本
	wp_enqueue_media();

	// 获取已保存的字段数据
	$be_gallery_fields1 = get_post_meta( $post->ID, 'be_gallery_fields1', true );
	$be_gallery_fields2 = get_post_meta( $post->ID, 'be_gallery_fields2', true );
	$be_gallery_fields3 = get_post_meta( $post->ID, 'be_gallery_fields3', true );

	// 添加安全验证nonce字段
	wp_nonce_field( 'gallery_meta_box_nonce', 'gallery_meta_box_nonce' );
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// 点击保存按钮时触发文章发布
			$('.metabox_submit').click(function(e) {
				e.preventDefault();
				$('#publish').click();
			});

			// 第一个表格添加新行
			$('#add-line1').on('click', function() {
				var line = $('.empty-line1.screen-reader-text').clone(true);
				line.removeClass('empty-line1 screen-reader-text');
				line.insertBefore('#gallery-fieldset-1 tbody>tr:last');
				return false;
			});

			// 第二个表格添加新行
			$('#add-line2').on('click', function() {
				var line = $('.empty-line2.screen-reader-text').clone(true);
				line.removeClass('empty-line2 screen-reader-text');
				line.insertBefore('#gallery-fieldset-2 tbody>tr:last');
				return false;
			});

			// 第三个表格添加新行
			$('#add-line3').on('click', function() {
				var line = $('.empty-line3.screen-reader-text').clone(true);
				line.removeClass('empty-line3 screen-reader-text');
				line.insertBefore('#gallery-fieldset-3 tbody>tr:last');
				return false;
			});

			// 删除行（通用处理）
			$('.remove-line').on('click', function() {
				$(this).parents('tr').remove();
				return false;
			});

			//第一个表格排序
			$('#gallery-fieldset-1 tbody').sortable({
				opacity: 0.6,
				revert: true,
				cursor: 'move',
				handle: '.sort'
			});

			// 第二个表格排序
			$('#gallery-fieldset-2 tbody').sortable({
				opacity: 0.6,
				revert: true,
				cursor: 'move',
				handle: '.sort'
			});

			// 第三个表格排序
			$('#gallery-fieldset-3 tbody').sortable({
				opacity: 0.6,
				revert: true,
				cursor: 'move',
				handle: '.sort'
			});

			// 上传图片
			$('.add-gallery-media').click(function(e) {
				e.preventDefault();
				var button = $(this);
				var custom_uploader = wp.media({
					title: '选择图片',
					library: {
						type: 'image'
					},
					multiple: false
				}).on('select', function() {
					var attachment = custom_uploader.state().get('selection').first().toJSON();
					button.parent().prev().find('input').val(attachment.url);
				}).open();
			});


		});
	</script>

	<!-- 字段-1 -->
	<table id="gallery-fieldset-1" width="100%">
		<thead>
			<tr>
				<th width="2%"></th>
				<th width="45.3%"></th>
				<th width="2%"></th>
				<th width="2%"></th>
				<th width="42.7%"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( $be_gallery_fields1 ) :
				foreach ( $be_gallery_fields1 as $field ) {
					echo '<tr>';
					echo '<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="image_url[]" ' . plchldr( '添加图片链接' ) . ' value="' . esc_attr( $field['image_url'] ) . '" /></td>';
					echo '<td><a href="javascript:;" class="add-gallery-media button be-metabox-btn" style="padding: 0 6px;"><span class="dashicons dashicons-camera"></span></a></td>';
					echo '<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="image_desc[]" ' . plchldr( '输入图片说明' ) . ' value="' . esc_attr( $field['image_desc'] ) . '" /></td>';
					echo '</tr>';
				}
			else :
				echo '<tr>
					<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
					<td><input type="text" class="widefat" name="image_url[]" ' . plchldr( '添加图片链接' ) . ' /></td>
					<td><a href="javascript:;" class="add-gallery-media button be-metabox-btn" style="padding: 0 6px;"><span class="dashicons dashicons-camera"></span></a></td>
					<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
					<td><input type="text" class="widefat" name="image_desc[]" ' . plchldr( '输入图片说明' ) . ' value="" /></td>

				</tr>';
			endif;
			?>

			<tr class="empty-line1 screen-reader-text">
				<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
				<td><input type="text" class="widefat" name="image_url[]" <?php echo plchldr( '添加图片链接' ); ?> /></td>
				<td><a href="javascript:;" class="add-gallery-media button be-metabox-btn" style="padding: 0 6px;"><span class="dashicons dashicons-camera"></span></a></td>
				<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
				<td><input type="text" class="widefat" name="image_desc[]" <?php echo plchldr( '输入图片说明' ); ?> value="" /></td>
			</tr>
		</tbody>
	</table>

	<p style="margin: 4px 0">
		<a id="add-line1" class="button" style="margin: 0 10px 0 3px;" href="#">添加图片</a>
		<input type="submit" class="metabox_submit button" value="更新" />
	</p>


	<!-- 字段-2 -->
	<table id="gallery-fieldset-2" width="100%">
		<thead>
			<tr>
				<th width="2%"></th>
				<th width="45%"></th>
				<th width="2%"></th>
				<th width="45%"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( $be_gallery_fields2 ) :
				foreach ( $be_gallery_fields2 as $field ) {
					echo '<tr>';
					echo '<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="inf_title[]" ' . plchldr( '输入标题文字' ) . ' value="' . esc_attr( $field['inf_title'] ) . '" /></td>';
					echo '<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="inf_text[]" ' . plchldr( '输入说明文字' ) . ' value="' . esc_attr( $field['inf_text'] ) . '" /></td>';
					echo '</tr>';
				}
			else :
				echo '<tr>
					<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
					<td><input type="text" class="widefat" ' . plchldr( '输入标题文字' ) . ' name="inf_title[]" /></td>
					<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
					<td><input type="text" class="widefat" name="inf_text[]" ' . plchldr( '输入说明文字' ) . ' value="" /></td>
				</tr>';
			endif;
			?>

			<tr class="empty-line2 screen-reader-text">
				<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
				<td><input type="text" class="widefat" name="inf_title[]" <?php echo plchldr( '输入标题文字' ); ?> /></td>
				<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
				<td><input type="text" class="widefat" name="inf_text[]" <?php echo plchldr( '输入说明文字' ); ?> value="" /></td>
			</tr>
		</tbody>
	</table>

	<p style="margin: 4px 0">
		<a id="add-line2" class="button" style="margin: 0 10px 0 3px;" href="#">添加信息</a>
		<input type="submit" class="metabox_submit button" value="更新" />
	</p>


	<!-- 字段-3 -->
	<table id="gallery-fieldset-3" width="100%">
		<thead>
			<tr>
				<th width="2%"></th>
				<th width="45%"></th>
				<th width="2%"></th>
				<th width="45%"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( $be_gallery_fields3 ) :
				foreach ( $be_gallery_fields3 as $field ) {
					echo '<tr>';
					echo '<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="btn_text[]" ' . plchldr( '输入按钮名称' ) . ' value="' . esc_attr( $field['btn_text'] ) . '" /></td>';
					echo '<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>';
					echo '<td><input type="text" class="widefat" name="btn_url[]" ' . plchldr( '输入按钮链接' ) . ' value="' . esc_attr( $field['btn_url'] ) . '" /></td>';
					echo '</tr>';
				}
			else :
				echo '<tr>
					<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
					<td><input type="text" class="widefat" name="btn_text[]" ' . plchldr( '输入按钮名称' ) . ' /></td>
					<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
					<td><input type="text" class="widefat" name="btn_url[]" ' . plchldr( '输入按钮链接' ) . ' value="" /></td>
				</tr>';
			endif;
			?>

			<tr class="empty-line3 screen-reader-text">
				<td><a class="button remove-line be-metabox-btn" href="#" style="padding: 0 6px;"><span class="dashicons dashicons-no-alt"></span></a></td>
				<td><input type="text" class="widefat" name="btn_text[]" <?php echo plchldr( '输入按钮名称' ); ?> /></td>
				<td><a class="sort button be-metabox-btn" style="cursor: move;padding: 0 6px;"><span class="dashicons dashicons-move"></span></a></td>
				<td><input type="text" class="widefat" name="btn_url[]" <?php echo plchldr( '输入按钮链接' ); ?> value="" /></td>
			</tr>
		</tbody>
	</table>

	<p style="margin: 4px 0">
		<a id="add-line3" class="button" style="margin: 0 10px 0 3px;" href="#">添加按钮</a>
		<input type="submit" class="metabox_submit button" value="更新" />
	</p>
	<?php
}

// 保存数据
function gallery_meta_box_save( $post_id ) {
	// 验证nonce字段
	if (
		! isset( $_POST['gallery_meta_box_nonce'] ) ||
		! wp_verify_nonce( $_POST['gallery_meta_box_nonce'], 'gallery_meta_box_nonce' )
	) {
		return;
	}

	// 如果是自动保存，不处理
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 检查用户权限
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// 处理第一个数据
	$old1        = get_post_meta( $post_id, 'be_gallery_fields1', true );
	$new1        = array();
	$image_urls  = isset( $_POST['image_url'] ) ? $_POST['image_url'] : array();
	$image_descs = isset( $_POST['image_desc'] ) ? $_POST['image_desc'] : array();

	$count1 = count( $image_urls );

	for ( $i = 0; $i < $count1; $i++ ) {
		if ( $image_urls[ $i ] != '' ) {
			$new1[ $i ]['image_url']  = stripslashes( $image_urls[ $i ] );
			$new1[ $i ]['image_desc'] = stripslashes( strip_tags( $image_descs[ $i ] ) );
		}
	}

	if ( ! empty( $new1 ) && $new1 != $old1 ) {
		update_post_meta( $post_id, 'be_gallery_fields1', $new1 );
	} elseif ( empty( $new1 ) && $old1 ) {
		delete_post_meta( $post_id, 'be_gallery_fields1', $old1 );
	}

	// 处理第二个数据
	$old2       = get_post_meta( $post_id, 'be_gallery_fields2', true );
	$new2       = array();
	$inf_titles = isset( $_POST['inf_title'] ) ? $_POST['inf_title'] : array();
	$inf_texts  = isset( $_POST['inf_text'] ) ? $_POST['inf_text'] : array();

	$count2 = count( $inf_titles );

	for ( $i = 0; $i < $count2; $i++ ) {
		if ( $inf_titles[ $i ] != '' ) :
			$new2[ $i ]['inf_title'] = stripslashes( strip_tags( $inf_titles[ $i ] ) );
			$new2[ $i ]['inf_text']  = stripslashes( $inf_texts[ $i ] );
		endif;
	}

	if ( ! empty( $new2 ) && $new2 != $old2 ) {
		update_post_meta( $post_id, 'be_gallery_fields2', $new2 );
	} elseif ( empty( $new2 ) && $old2 ) {
		delete_post_meta( $post_id, 'be_gallery_fields2', $old2 );
	}

	// 处理第三个数据
	$old3      = get_post_meta( $post_id, 'be_gallery_fields3', true );
	$new3      = array();
	$btn_texts = isset( $_POST['btn_text'] ) ? $_POST['btn_text'] : array();
	$btn_urls  = isset( $_POST['btn_url'] ) ? $_POST['btn_url'] : array();

	$count3 = count( $btn_texts );

	for ( $i = 0; $i < $count3; $i++ ) {
		if ( $btn_texts[ $i ] != '' ) {
			$new3[ $i ]['btn_text'] = stripslashes( $btn_texts[ $i ] );
			$new3[ $i ]['btn_url']  = stripslashes( strip_tags( $btn_urls[ $i ] ) );
		}
	}

	if ( ! empty( $new3 ) && $new3 != $old3 ) {
		update_post_meta( $post_id, 'be_gallery_fields3', $new3 );
	} elseif ( empty( $new3 ) && $old3 ) {
		delete_post_meta( $post_id, 'be_gallery_fields3', $old3 );
	}
}


// 图片画廊
function gallery_des() {
	global $post;
	$be_gallery_fields1 = get_post_meta( $post->ID, 'be_gallery_fields1', true );

	if ( empty( $be_gallery_fields1 ) || ! is_array( $be_gallery_fields1 ) ) {
		return;
	}

	echo '<div class="gallery-des">';
	echo '<div class="gallery-slide">';
	echo '<div class="gallery-active-image">';

	if ( $be_gallery_fields1 && ! empty( $be_gallery_fields1[0]['image_url'] ) ) {
		echo '<a id="gallery-active-link" href="' . esc_attr( $be_gallery_fields1[0]['image_url'] ) . '" data-fancybox="gallery">';
		echo '<img id="gallery-active-img" class="sc" src="' . esc_attr( $be_gallery_fields1[0]['image_url'] ) . '" alt="' . esc_attr( $be_gallery_fields1[0]['image_desc'] ) . '">';
		echo '</a>';
	}

	echo '</div>';

	if ( $be_gallery_fields1 && count( $be_gallery_fields1 ) > 1 ) {
		echo '<div class="gallery-slide-nav">';
		foreach ( $be_gallery_fields1 as $field ) {
			if ( $field['image_url'] != '' ) {
				echo '<a href="javascript:;" data-large-src="' . esc_attr( $field['image_url'] ) . '" data-caption="' . esc_attr( $field['image_desc'] ) . '">';
				echo '<img class="sc" src="' . esc_attr( $field['image_url'] ) . '" alt="' . esc_attr( $field['image_desc'] ) . '">';
				echo '</a>';
			}
		}
		echo '</div>';
	}

	echo '</div>';

	echo '<div class="gallery-explain">';
	$be_gallery_fields2 = get_post_meta( $post->ID, 'be_gallery_fields2', true );
	$be_gallery_fields3 = get_post_meta( $post->ID, 'be_gallery_fields3', true );

	if ( $be_gallery_fields2 && is_array( $be_gallery_fields2 ) ) {
		echo '<div class="gallery-des-inf">';
		foreach ( $be_gallery_fields2 as $field ) {
			if ( ! empty( $field['inf_title'] ) ) {
				echo '<div class="gallery-des-item">';
				echo '<span class="gallery-inf-title">' . esc_attr( $field['inf_title'] ) . '</span>';
				echo '<span class="gallery-inf-text">' . esc_attr( $field['inf_text'] ) . '</span>';
				echo '</div>';
			}
		}
		echo '</div>';
	}

	if ( $be_gallery_fields3 && is_array( $be_gallery_fields3 ) ) {
		echo '<div class="gallery-des-btn">';
		foreach ( $be_gallery_fields3 as $field ) {
			if ( ! empty( $field['btn_text'] ) ) {
				echo '<div class="gallery-btn">';
				echo '<a href="' . esc_attr( $field['btn_url'] ) . '" target="_blank">' . esc_attr( $field['btn_text'] ) . '</a>';
				echo '</div>';
			}
		}
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}

// 添加到文章顶部
function gallery_des_content( $content ) {
	if ( ( is_single() || is_page() ) && function_exists( 'gallery_des' ) ) {
		return gallery_des() . $content;
	}
	return $content;
}
add_filter( 'the_content', 'gallery_des_content' );
