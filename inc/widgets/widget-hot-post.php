<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 热门文章
class hot_post_img extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'hot_post_img',
			'description' => '调用点击最多的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('hot_post_img', '热门文章', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs'   => 1,
			'show_serial'   => 1,
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'number'        => 5,
			'days'          => 90,
			'title'         => '热门文章',
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
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		$days = strip_tags($instance['days']) ? absint( $instance['days'] ) : 90;
?>

<?php if ($instance['show_thumbs']) { ?>
<div id="hot_post_widget" class="new_cat">
<?php } else { ?>
<div id="hot_post_widget" class="widget-li-icon">
<?php } ?>
	<div id="hot_post_widget" class="<?php echo $instance['show_thumbs'] ? 'new_cat' : 'widget-li-icon'; ?>">
		<?php if ($instance['show_icon']) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
		<?php } ?>
		<?php if ($instance['show_svg']) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
		<?php } ?>
		<ul class="widget-ul-hot<?php if ($instance['show_thumbs']) { ?> show-thumbs<?php } ?><?php if ($instance['show_serial']) { ?> hot-icon<?php } ?>">
			<?php if (zm_get_option('post_views')) { ?>
			<?php if ($instance['show_thumbs']) { ?>
				<?php get_timespan_most_viewed_img( 'post',$number,$days, true, true ); ?>
			<?php } else { ?>
			    <?php get_timespan_most_viewed( 'post',$number, $days, true, true ); ?>
			<?php } ?>
			<?php wp_reset_query(); ?>
			<?php } else { ?>
				<li>需要启用文章浏览统计</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_thumbs'] = !empty($new_instance['show_thumbs']) ? 1 : 0;
		$instance['show_serial'] = !empty($new_instance['show_serial']) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['days'] = strip_tags($new_instance['days']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '热门文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$instance = wp_parse_args((array) $instance, array('days' => '90'));
		$number = strip_tags($instance['number']);
		$days = strip_tags($instance['days']);
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
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'days' ); ?>">时间限定（天）：</label>
		<input class="number-text-be-d" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="number" step="1" min="1" value="<?php echo $days; ?>" size="3" />
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_serial') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_serial') ); ?>" <?php checked( (bool) $instance["show_serial"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_serial') ); ?>">缩略图序号</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function hot_post_img_init() {
	register_widget( 'hot_post_img' );
}
add_action( 'widgets_init', 'hot_post_img_init' );