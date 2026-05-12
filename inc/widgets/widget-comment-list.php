<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 本文评论者列表
class be_comment_list extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_comment_list',
			'description' => '显示当前文章评论者列表',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_comment_list', '评论者列表', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'title'       => '评论者列表',
		);
	}

	function widget($args, $instance) {
		extract($args);
		if ( is_single() ) {
			$title_w = title_i_w();
			$defaults = $this -> zm_defaults();
			$instance = wp_parse_args( (array) $instance, $defaults );
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( ! empty( $title ) )
			echo $before_title . $title_w . $title . $after_title;
		}
?>
<?php if ( is_single() ) { ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

<ul class="comment-names gaimg names-area">
	<?php if ( have_comments() ) { ?>
		<?php echo get_comment_authors_list(); ?>
		<div class="all-names-list" title="<?php _e( '更多', 'begin' ); ?>"><i class="be be-more"></i></div>
	<?php } else { ?>
		<a class="names-scroll"><?php echo $instance['about_the']; ?></a>
	<?php } ?>
</ul>
<?php } ?>
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
		$instance['about_the'] = $new_instance['about_the'];
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '评论者列表';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('about_the' => '暂无评论，抢沙发？'));
		$about_the = $instance['about_the'];
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
		<label for="<?php echo $this->get_field_id('about_the'); ?>">无评论提示：</label>
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id('about_the'); ?>" name="<?php echo $this->get_field_name('about_the'); ?>"><?php echo $about_the; ?></textarea></p>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'be_comment_list_init' );
function be_comment_list_init() {
	register_widget( 'be_comment_list' );
}