<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 计数器
class be_counter_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'be-counter',
			'description' => '数字滚动增加动画',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_counter', '动画计数器', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title' => '',
			'name'  => '网站访问量',
			'value' => '',
			'speed' => '40000',
			'icon'  => 'be be-eye',
			'rec'   => 1,
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
?>

<div class="be-counter-main<?php if ( ! $instance['icon'] ) { ?> be-counter-main-c<?php } ?>">
	<?php if ( $instance['icon'] ) { ?>
		<div class="counters-icon">
			<i class="<?php echo $instance['icon']; ?>"></i>
		</div>
	<?php } ?>

	<div class="counters-item">
		<?php if ( $instance['value'] ) { ?>
			<?php $value = $instance['value']; ?>
		<?php } else { ?>
			<?php $value = all_view(); ?>
		<?php } ?>

		<div class="counters">
			<div class="counter" data-TargetNum="<?php echo $value; ?>" data-Speed="<?php echo $instance['speed']; ?>">0</div><span class="counter-unit">+</span>
		</div>
		<div class="counter-title"><?php echo $instance['name']; ?></div>
	</div>
	<?php if ( $instance['rec'] ) { ?>
		<div class="rec-adorn-s"></div><div class="rec-adorn-x"></div>
	<?php } ?>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name']  = strip_tags( $new_instance['name'] );
		$instance['value'] = strip_tags( $new_instance['value'] );
		$instance['speed'] = strip_tags( $new_instance['speed'] );
		$instance['icon']  = strip_tags( $new_instance['icon'] );
		$instance['rec']   = ! empty( $new_instance['rec'] ) ? 1 : 0;
		return $instance;
	}
	function form( $instance ) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'name' ); ?>">名称</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" type="text" value="<?php echo $instance['name']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'icon' ); ?>">图标（留空则不显示）</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" type="text" value="<?php echo $instance['icon']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'value' ); ?>">数值（留空为网站浏览总数）
			<input class="number-text-be" style="width: 100px;" id="<?php echo $this->get_field_id( 'value' ); ?>" name="<?php echo $this->get_field_name( 'value' ); ?>" type="number" min="1" step="1" value="<?php echo $instance['value']; ?>" />
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'speed' ); ?>">速度（多少秒内达到设定数值）
			<input class="number-text-be" style="width: 100px;" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>"  type="number" min="1" step="1" value="<?php echo $instance['speed']; ?>" />
		</label>
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'rec' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rec' ) ); ?>" <?php checked( (bool) $instance["rec"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'rec' ) ); ?>">装饰</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'be_counter_widget_init' );
function be_counter_widget_init() {
	register_widget( 'be_counter_widget' );
}