<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 目录小工具
class be_toc_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be-toc-widget toc-no',
			'description' => '文章目录，仅适合添加到正文侧边栏中',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_toc_widget', '文章目录', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title' => '文章目录',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! wp_is_mobile() ) {
			echo $before_widget;
		}
?>
<?php if ( ! wp_is_mobile() && zm_get_option( 'be_toc') ) { ?>
	<div id="toc-widge" class="toc-widge">
		<div class="toc-widge-title"><i class="be be-sort"></i><?php _e( '文章目录', 'begin' ); ?></div>
		<div class="toc-widge-main">
			<?php be_toc(); ?>
		</div>
		<div class="adorn rec-adorn-s"></div>
		<div class="adorn rec-adorn-x"></div>
	</div>
<?php } ?>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '文章目录';
		}
		global $wpdb;
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p style="color: #c40000;">警告：禁止加到正文底部小工具中，否则将无法打开文章</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'be_toc_widget_init' );
function be_toc_widget_init() {
	register_widget( 'be_toc_widget' );
}