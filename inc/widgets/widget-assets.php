<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 会员资源
class Be_Assets_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be-assets-widget',
			'description' => '调用指定会员资源文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('Be_Assets_Widget', '会员资源', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'post_id' => '',
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		echo $before_widget;
?>

<div class="assets-widget">
	<?php
		$args = array(
			'post__in'  => explode( ',', $instance['post_id']),
			'orderby'   => 'post__in', 
			'order'     => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			);
		$query = new WP_Query( $args );
	?>

	<div class="flexbox-grid">
		<?php
			if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
				require get_template_directory() . '/template/assets.php';
			endwhile;
			endif;
			wp_reset_postdata();
		?>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<div class="clear"></div>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['post_id'] = strip_tags( $new_instance['post_id'] );
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		global $wpdb;
?>

	<p>
		<label for="<?php echo $this->get_field_id( 'post_id' ); ?>">输入文章ID：</label>
		<textarea style="height:63px;" class="widefat" id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['post_id'] ), ENT_QUOTES)); ?></textarea>
		多个ID用英文半角逗号","隔开，按先后排序
	</p>


	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'be_assets_Widget_init' );
function be_assets_Widget_init() {
	register_widget( 'Be_Assets_Widget' );
}