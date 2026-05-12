<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 短代码
function be_shortcode_recentlyViewed( $atts ) {
	$args = array(
		'widget_id'    => $atts['widget_id'],
		'by_shortcode' => 'shortcode_',
	);

	ob_start();
	the_widget( 'Be_recently_viewed', '', $args );
	$output = ob_get_clean();
	return $output;
}

add_shortcode( 'be-recentlyviewed', 'be_shortcode_recentlyViewed' );
// 多条件筛选
class widget_filter extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_filter',
			'description' => '多条件筛选',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_filter', '条件筛选', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'title'       => '条件筛选',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! is_page() ) {
			echo $before_widget;
		}
	?>

	<?php if ( ! is_page() ) { ?>
	<div class="widget-filter">
		<div class="filter-box">
			<div class="filter-t"><i class="be be-sort"></i><span><?php echo zm_get_option('filter_t'); ?></span></div>
				<?php if (zm_get_option('filters_hidden')) { ?><div class="filter-box-main filter-box-main-h"><?php } else { ?><div class="filter-box-main"><?php } ?>
				<?php require get_template_directory() . '/inc/filter-core.php'; ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php
		echo $after_widget;
		}
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance = array();
			return $instance;
		}
		function form($instance) {
			$defaults = $this -> zm_defaults();
			$instance = wp_parse_args( (array) $instance, $defaults );
			global $wpdb;

	?>
	<p>多条件筛选该小工具</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
	<?php }
}
add_action( 'widgets_init', 'widget_filter_init' );
function widget_filter_init() {
	register_widget( 'widget_filter' );
}