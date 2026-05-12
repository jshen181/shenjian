<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 网站概况
class site_profile extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'site_profile',
			'description' => '网站概况',
			'customize_selective_refresh' => true,
		);
		parent::__construct('site_profile', '网站概况', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'time'          => 2007-8-1,
			'title'         => '网站概况',
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
		$time = strip_tags($instance['time']) ? absint( $instance['time'] ) : 2007-8-1;
?>

<div class="site-profile">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<li><?php _e( '文章', 'begin' ); ?><span><?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?></span></li>
		<li><?php _e( '分类', 'begin' ); ?><span><?php echo $count_categories = wp_count_terms('category'); ?></span></li>
		<li><?php _e( '标签', 'begin' ); ?><span><?php echo $count_tags = wp_count_terms('post_tag'); ?></span></li>
		<li><?php _e( '留言', 'begin' ); ?><span><?php $my_email = get_bloginfo ( 'admin_email' ); global $wpdb; echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments where comment_author_email!='$my_email'");?></span></li>
		<li><?php _e( '链接', 'begin' ); ?><span><?php global $wpdb; echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'"); ?></span></li>
		<li><?php _e( '浏览', 'begin' ); ?><span><?php echo all_view(); ?></span></li>
		<li><?php _e( '今日', 'begin' ); ?><span><?php today_renew(); ?></span></li>
		<li><?php _e( '本周', 'begin' ); ?><span><?php week_renew(); ?></span></li>
		<li><?php _e( '运行', 'begin' ); ?><span><?php echo floor((time()-strtotime($instance['time']))/86400); ?> <?php _e( '天', 'begin' ); ?></span></li>
		<li><?php _e( '更新', 'begin' ); ?><span><?php global $wpdb; $last =$wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");$last = date('Y-n-j', strtotime($last[0]->MAX_m));echo $last; ?></span></li>
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
		$instance['time'] = strip_tags($new_instance['time']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '网站概况';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('time' => '2007-8-1'));
		$time = strip_tags($instance['time']);
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
	<p><label for="<?php echo $this->get_field_id('time'); ?>">建站日期：</label>
	<input id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>" type="text" value="<?php echo $time; ?>" size="10" /> 格式：2007-8-1</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function site_profile_init() {
	register_widget( 'site_profile' );
}
add_action( 'widgets_init', 'site_profile_init' );