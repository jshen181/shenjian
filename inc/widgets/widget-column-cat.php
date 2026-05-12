<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 单栏分类
class widget_color_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_color_cat',
			'description' => '单栏分类目录',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_color_cat', '单栏分类', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'      => '',
			'show_icon'    => '',
			'show_svg'     => '',
			'e_cat'        => '',
			'title'        => '单栏分类',
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
		$e_cat = strip_tags($instance['e_cat']);
?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

<ul class="color-cat">
	<?php
		$terms = get_terms(
			array(
				'taxonomy'      => array( 'category', 'taobao', 'gallery', 'videos', 'products', 'notice' ),
				'include'       => '',
				'exclude'       => $e_cat,
				'hide_empty'   => true,
				'hierarchical' => false,
				'title_li'     =>  '',
				'orderby'      => 'id',
				'order'        => 'ASC',
			)
		);
		foreach ( $terms as $term ) {
	?>
	<li><a class="get-icon" href="<?php echo esc_url( get_term_link( $term ) ); ?>" rel="bookmark" <?php echo goal(); ?>><?php echo $term->name; ?></a></li>
	<?php } ?>
</ul>
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
		$instance['e_cat'] = strip_tags($new_instance['e_cat']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '单栏分类';
		}
		global $wpdb;
		$e_cat = strip_tags($instance['e_cat']);
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
		<label for="<?php echo $this->get_field_id('e_cat'); ?>">输入排除的分类ID：</label>
		<textarea style="height:50px;" class="widefat" id="<?php echo $this->get_field_id( 'e_cat' ); ?>" name="<?php echo $this->get_field_name( 'e_cat' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['e_cat'] ), ENT_QUOTES)); ?></textarea>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'widget_color_cat_init' );
function widget_color_cat_init() {
	register_widget( 'widget_color_cat' );
}
