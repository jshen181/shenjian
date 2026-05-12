<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 即将发布
class be_timing_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_timing_post',
			'description' => '即将发表的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_timing_post', '即将发布', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'number'        => 5,
			'title'         => '即将发布',
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
?>

<div class="timing_post">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<?php
			$be_query = new WP_Query( array ( 'post_status' => 'future', 'order' => 'ASC', 'showposts' => $number, 'ignore_sticky_posts' => '1'));
			$i = 1; // 初始化计数器
			if ( $be_query->have_posts() ) {
				while ($be_query->have_posts()) : $be_query->the_post();
					$do_not_duplicate = get_the_ID(); ?>
					<li>
						<span class="li-icon li-icon-<?php echo $i; ?>"><span class="timing-date"><?php the_time( 'd' ); ?></span><span class="timing-date"><?php _e( '日', 'begin' ); ?></span></span>
						<span class="timing_time"><?php _e( '预定发表于', 'begin' ); ?> <?php the_time( 'm-d G:i' ); ?></span>
						<?php the_title(); ?>
						</span>
					</li>
					<?php $i++;; ?>
				<?php endwhile; wp_reset_postdata();
			} else {
				echo '<li>'. __( '暂无文章', 'begin' ) .'</li>';
			}
		?>
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
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
	} else {
		$title = '即将发布';
	}
	global $wpdb;
	$instance = wp_parse_args((array) $instance, array('number' => '5'));
	$number = strip_tags($instance['number']);
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
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
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function be_timing_post_init() {
	register_widget( 'be_timing_post' );
}
add_action( 'widgets_init', 'be_timing_post_init' );