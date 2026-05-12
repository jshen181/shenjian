<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 公告
class widget_notice extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_notice',
			'description' => '滚动显示公告',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_notice', '滚动公告', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'        => '',
			'show_icon'      => '',
			'show_svg'       => '',
			'cat'            => '',
			'number'         => 2,
			'notice_back'    => get_template_directory_uri() . '/img/default/random/320.jpg',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 2;
?>

<div class="zm-notice">

	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ( $instance[ 'notice_back' ]  ) { ?>
		<div class="list-img-box"><img src="<?php echo $instance['notice_back']; ?>" alt="notice"></div>
	<?php } ?>
	<div class="clear"></div>
	<?php if ( $instance[ 'notice_back' ]  ) { ?>
	<div class="notice-main notice-main-img">
		<?php } else { ?>
	<div class="notice-main">
	<?php } ?>
		<ul class="list placardtxt owl-carousel">
			<?php
				$args = array(
					'post_type' => 'bulletin',
					'showposts' => $number, 
					'post_status'    => 'publish',
					'ignore_sticky_posts' => 1, 
					'no_found_rows'       => true,
					'tax_query' => array(
						array(
							'taxonomy' => 'notice',
							'terms' => $instance['cat']
						),
					)
				);
		 	?>
			<?php $be_query = new WP_Query($args); while ($be_query->have_posts()) : $be_query->the_post(); ?>
			<?php the_title( sprintf( '<li><a href="%s" rel="bookmark" ' . goal() . '><i class="be be-volumedown ri"></i>', esc_url( get_permalink() ) ), '</a></li>' ); ?>
			<?php endwhile;?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</div>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['hideTitle'] = !empty($new_instance['hideTitle']) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['notice_back'] = $new_instance['notice_back'];
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['cat'] = $new_instance['cat'];
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '滚动公告';
		}
	global $wpdb;
	$instance = wp_parse_args((array) $instance, array('number' => '2'));
	$number = strip_tags($instance['number']);
		$instance = wp_parse_args((array) $instance, array('notice_back' => get_template_directory_uri() . '/img/default/random/320.jpg'));
		$notice_back = $instance['notice_back'];
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
		<label for="<?php echo $this->get_field_id('notice_back'); ?>">背景图片：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'notice_back' ); ?>" name="<?php echo $this->get_field_name( 'notice_back' ); ?>" type="text" value="<?php echo $notice_back; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('cat'); ?>">选择分类：
		<?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => '选择分类', 'hide_empty'=>0, 'hierarchical'=>1,'taxonomy' => 'notice', 'selected'=>$instance['cat'])); ?></label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

if ( is_array( zm_get_option( 'be_type' ) ) && in_array( 'notice', zm_get_option( 'be_type' ) ) ) {
	add_action( 'widgets_init', 'widget_notice_init' );
	function widget_notice_init() {
		register_widget( 'widget_notice' );
	}
}