<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 附件信息
class be_attachment_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be-attachment-inf',
			'description' => '用于显示当前文章图片附件信息',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_attachment_widget', '图片信息', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title'     => '图片信息',
			'model'     => 0,
			'down_btu'  => 0,
		);
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$title_w = title_i_w();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);

		if ( $instance['model'] ) {
			$number = 1;
		} else {
			$number = -1;
		}

		$attachments = get_children( array(
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'numberposts'    => $number,
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ASC'
		) );

		if ( $attachments ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( ! empty( $title ) )
			echo $before_title . $title_w . $title . $after_title;
		}
?>
<?php if ( $attachments ) { ?>
	<div class="be-attachment-inf-box<?php if ( $instance['model'] ) { ?> annex-one<?php } else { ?> annex-mult<?php } ?>">
		<?php 
			foreach ( $attachments as $attachment_id => $attachment ) {
				echo '<div class="be-attachment-item">';
				$image_url = wp_get_attachment_image_url( $attachment_id );
				$image_id = be_get_image_id( $image_url );
				$metadata = wp_get_attachment_metadata( $image_id );
				$image = get_post_meta( get_the_ID(), 'thumbnail', true );
				echo '<div class="annex-img">';
				if ( get_post_meta( get_the_ID(), 'thumbnail', true ) ) {
					echo '<img src="' . $image . '" alt="' . $image_id . '" width="320" height="240" />';
				} else {
					echo '<img src="'. be_resize_image( $image_url, 320, 240, true ) . '" alt="' . $image_id . '" width="320" height="240" />';
				}
				if ( !$instance['down_btu'] || is_user_logged_in() ) {
					echo '<a class="annex-down" href="' . $image_url . '" download="' . $image_url . '"></a>';
				} else {
					echo '<span class="annex-down show-layer"></span>';
				}
				echo '</div>';

				echo '<div class="annex-inf">';
				echo sprintf(__( '编号', 'begin' )) . '<span>' . $image_id . '</span>';
				echo '<div class="img-inc-width">' . sprintf(__( '分辨率', 'begin' )) . '<span>';
				echo $metadata['width'];
				echo '&times;' . $metadata['height'] . '</span></div>';
				echo '<div class="img-inc-size">' . sprintf(__( '大小', 'begin' )) . '<span>' . size_format( filesize( get_attached_file( $image_id ) ), 2 ) . '</span></div>';
				echo '</div>';
				echo '</div>';
			}
		?>
	</div>
<?php } ?>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['down_btu'] = !empty($new_instance['down_btu']) ? 1 : 0;
		$instance['model'] = !empty($new_instance['model']) ? 1 : 0;
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '图片信息';
		}
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id('down_btu') ); ?>">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('down_btu') ); ?>" name="<?php echo esc_attr( $this->get_field_name('down_btu') ); ?>" <?php checked( (bool) $instance["down_btu"], true ); ?>>
			登录可见下载按钮
		</label>
	</p>

	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id('model') ); ?>">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('model') ); ?>" name="<?php echo esc_attr( $this->get_field_name('model') ); ?>" <?php checked( (bool) $instance["model"], true ); ?>>
			仅一张图
		</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'be_attachment_widget_init' );
function be_attachment_widget_init() {
	register_widget( 'be_attachment_widget' );
}