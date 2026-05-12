<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ajax随机
class ajax_random_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'ajax-random',
			'description' => 'Ajax动态调用随机文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'ajax_random_post', '随便看看', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'title'         => '随便看看',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
?>

<?php if ( zm_get_option( 'random_post_img' ) ) { ?>
<div class="new_cat">
<?php } else { ?>
<div class="ajax-random-list">
<?php } ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<div class="random-btn"><span class="random-btn-ico"><i class="dashicons dashicons-update"></i></span><?php _e( '换一批', 'begin' ); ?></div>
		<div class="random-content">
			<?php if ( zm_get_option( 'random_post_img' ) ) { ?>
				<?php echo random_load(); ?>
			<?php } else { ?>
				<?php echo random_load(); ?>
			<?php } ?>
		</div>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance = array();
			$instance['title_z'] = strip_tags($new_instance['title_z']);
			$instance['show_icon'] = strip_tags($new_instance['show_icon']);
			$instance['show_svg'] = strip_tags($new_instance['show_svg']);
			$instance['title'] = strip_tags( $new_instance['title'] );
			return $instance;
		}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '随便看看';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('title_z'); ?>">图标标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name('title_z'); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
	</p>
	<p class="layoutflex">
		<label for="<?php echo $this->get_field_id('show_icon'); ?>">单色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_icon'); ?>" name="<?php echo $this->get_field_name('show_icon'); ?>" type="text" value="<?php echo $instance['show_icon']; ?>" />
		</label>
		<label for="<?php echo $this->get_field_id('show_svg'); ?>">彩色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_svg'); ?>" name="<?php echo $this->get_field_name('show_svg'); ?>" type="text" value="<?php echo $instance['show_svg']; ?>" />
		</label>
	</p>
	<p>更多选项：</p>
	<p>主题选项 → 基本设置 → 侧边栏小工具 → 随便看看小工具</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

function ajax_random_post_init() {
	register_widget( 'ajax_random_post' );
}
add_action( 'widgets_init', 'ajax_random_post_init' );