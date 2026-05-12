<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 幻灯小工具
class slider_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'slider_post',
			'description' => '以幻灯形式调用指定的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('slider_post', '幻灯', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'post_id'     => '',
			'title'       => '幻灯',
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
		$postid = $instance['post_id'];
?>
<div id="slider-widge" class="slider-widge-box">
	<div class="owl-carousel slider-widge">
		<?php
			$args = array(
				'post__in' => explode( ',', $postid ), 
				'orderby'   => 'post__in', 
				'order'     => 'DESC',
				'ignore_sticky_posts' => true, 
				'post_status'    => 'publish',
				'no_found_rows'       => true,
				);
			$query = new WP_Query( $args );
		?>
		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="slider-widge-main">
			<div class="slider-widge-img"><?php echo zm_widge_thumbnail(); ?></div>
			<?php the_title( sprintf( '<div class="widge-caption"><a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a></div>' ); ?>
		</div>
		<?php endwhile; ?>
		<?php else : ?>
			<ul class="slider-widge-none">在幻灯小工具中输入文章ID</ul>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>

	<div class="lazy-img ajax-owl-loading">
		<?php
			$args = array(
				'post__in' => explode( ',', $postid ), 
				'orderby'   => 'post__in', 
				'order'     => 'DESC',
				'posts_per_page' => 1,
				'ignore_sticky_posts' => true, 
				);
			$query = new WP_Query( $args );
		?>
		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="slider-widge-main">
			<div class="slider-widge-img"><?php echo zm_widge_thumbnail(); ?></div>
			<div class="widge-caption"><a href="#" rel="bookmark"><?php _e( '加载中...', 'begin' ); ?></a></div>
		</div>
		<?php break; ?>
		<?php endwhile; ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['post_id'] = strip_tags($new_instance['post_id']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '幻灯';
		}
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'post_id' ); ?>">输入文章ID：</label>
		<textarea style="height:63px;" class="widefat" id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['post_id'] ), ENT_QUOTES)); ?></textarea>
		多个ID用英文半角逗号","隔开，按先后排序
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'slider_post_init' );
function slider_post_init() {
	register_widget( 'slider_post' );
}