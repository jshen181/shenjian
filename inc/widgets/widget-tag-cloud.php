<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 标签云
class cx_tag_cloud extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'cx_tag_cloud',
			'description'                 => '可实现3D特效',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'cx_tag_cloud', '热门标签', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_3d'   => 1,
			'color'     => 1,
			'title_z'   => '',
			'show_icon' => '',
			'show_svg'  => '',
			'number'    => 20,
			'tags_id'   => '',
			'title'     => '热门标签',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults = $this->zm_defaults();
		$title_w  = title_i_w();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title    = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title_w . $title . $after_title;
		}

		$number  = strip_tags( $instance['number'] ) ? absint( $instance['number'] ) : 20;
		$tags_id = strip_tags( $instance['tags_id'] ) ? absint( $instance['tags_id'] ) : 1;

		if ( $instance['show_icon'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'] . '</h3>';
		}

		if ( $instance['show_svg'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#' . $instance['show_svg'] . '"></use></svg>' . $instance['title_z'] . '</h3>';
		}

		if ( $instance['show_3d'] ) {
			echo '<div id="tag_cloud_widget" class="cloud-tag">';
		} elseif ( $instance['color'] ) {
				echo '<div class="tagcloud-color">';
		} else {
			echo '<div class="be-tagcloud">';
		}

		wp_tag_cloud(
			array(
				'smallest'   => 14,
				'largest'    => 20,
				'unit'       => 'px',
				'order'      => 'RAND',
				'hide_empty' => 0,
				'number'     => $number,
				'include'    => $instance['tags_id'],
			)
		);

		echo '<div class="clear"></div>';

		if ( $instance['show_3d'] ) {
			wp_enqueue_script( '3dtag' );
		}

		echo '</div>';
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance              = array();
		$instance['show_3d']   = ! empty( $new_instance['show_3d'] ) ? 1 : 0;
		$instance['color']     = ! empty( $new_instance['color'] ) ? 1 : 0;
		$instance['title_z']   = strip_tags( $new_instance['title_z'] );
		$instance['show_icon'] = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg']  = strip_tags( $new_instance['show_svg'] );
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['number']    = strip_tags( $new_instance['number'] );
		$instance['tags_id']   = strip_tags( $new_instance['tags_id'] );
		return $instance;
	}
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '热门标签';
		}
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, array( 'number' => '20' ) );
		$number   = strip_tags( $instance['number'] );
		$instance = wp_parse_args( (array) $instance, array( 'tags_id' => '' ) );
		$tags_id  = strip_tags( $instance['tags_id'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_z' ); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
		</p>
		<p class="layoutflex">
			<label for="<?php echo $this->get_field_id( 'show_icon' ); ?>">单色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_icon' ); ?>" name="<?php echo $this->get_field_name( 'show_icon' ); ?>" type="text" value="<?php echo $instance['show_icon']; ?>" />
			</label>
			<label for="<?php echo $this->get_field_id( 'show_svg' ); ?>">彩色图标代码：
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_svg' ); ?>" name="<?php echo $this->get_field_name( 'show_svg' ); ?>" type="text" value="<?php echo $instance['show_svg']; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tags_id' ); ?>">输入标签ID调用指定标签：</label>
			<textarea style="height:50px;" class="widefat" id="<?php echo $this->get_field_id( 'tags_id' ); ?>" name="<?php echo $this->get_field_name( 'tags_id' ); ?>"><?php echo stripslashes( htmlspecialchars( ( $instance['tags_id'] ), ENT_QUOTES ) ); ?></textarea>
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_3d' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_3d' ) ); ?>" <?php checked( (bool) $instance['show_3d'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_3d' ) ); ?>">显示3D特效</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>" <?php checked( (bool) $instance['color'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>">随机背景色</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

function cx_tag_cloud_init() {
	register_widget( 'cx_tag_cloud' );
}
add_action( 'widgets_init', 'cx_tag_cloud_init' );
