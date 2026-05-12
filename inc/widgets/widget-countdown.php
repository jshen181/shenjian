<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 人生倒计时
class Countdown_Be_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'countdown_widget',
			'description' => '日、周、月、年倒计时',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'Countdown_Be_Widget', '时光静好', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title_z'      => '',
			'show_icon'    => '',
			'show_svg'     => '',
			'title'        => '时光静好',
			'square'       => 'round-progress',
			'current_date' => 1,
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		wp_enqueue_script( 'countdown' );
?>

<div class="be-countdown-widge">
	<?php if( $instance['show_icon'] ) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon da"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if( $instance['show_svg'] ) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon da"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<div class="countdown-count-main <?php echo $instance['square']; ?>">
		<?php if( $instance['current_date'] ) { ?>
			<div class="current-date"><span class="current-time"></span></div>
			<script>
				function updateTime() {
					var currentTime = new Date();
					var year = currentTime.getFullYear();
					var month = (currentTime.getMonth() < 9 ? "0": "") + (currentTime.getMonth() + 1);
					var day = (currentTime.getDate() < 10 ? "0": "") + currentTime.getDate();
					var hours = currentTime.getHours();
					var minutes = currentTime.getMinutes();
					var seconds = currentTime.getSeconds();

					hours = (hours < 10 ? "0": "") + hours;
					minutes = (minutes < 10 ? "0": "") + minutes;
					seconds = (seconds < 10 ? "0": "") + seconds;

					var dateTimeString = year + "年" + month + "月" + day + "日 " + hours + ":" + minutes + ":" + seconds;

					var timeElements = document.getElementsByClassName("current-time");
					for (var i = 0; i < timeElements.length; i++) {
						timeElements[i].innerHTML = " " + dateTimeString;
					}
					setTimeout(updateTime, 1000);
				}
				updateTime();
			</script>
		<?php } ?>

		<div class="countdown-area">
			<div class="countdown-item countdown-day" id="dayprogress">
				<div class="countdown-title"><?php _e( '今日', 'begin' ); ?></div>
				<div class="besea">
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="progress-count">00%</div>
				</div>
				<div class="countdown-time"><?php _e( '已', 'begin' ); ?><span>00</span><?php _e( '小时', 'begin' ); ?></div>
			</div>

			<div class="countdown-item countdown-week" id="weekprogress">
				<div class="countdown-title"><?php _e( '本周', 'begin' ); ?></div>
				<div class="besea">
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="progress-count">00%</div>
				</div>
				<div class="countdown-time"><?php _e( '已', 'begin' ); ?><span>00</span><?php _e( '天', 'begin' ); ?></div>
			</div>

			<div class="countdown-item countdown-month" id="monthprogress">
				<div class="countdown-title"><?php _e( '本月', 'begin' ); ?></div>
				<div class="besea">
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="progress-count">00%</div>
				</div>
				<div class="countdown-time"><?php _e( '已', 'begin' ); ?><span>00</span><?php _e( '天', 'begin' ); ?></div>
			</div>

			<div class="countdown-item countdown-year" id="yearprogress">
				<div class="countdown-title"><?php _e( '今年', 'begin' ); ?></div>
				<div class="besea">
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="bewave"></div>
					<div class="progress-count">00%</div>
				</div>
				<div class="countdown-time"><?php _e( '已', 'begin' ); ?><span>00</span><?php _e( '月', 'begin' ); ?></div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title_z'] = strip_tags( $new_instance['title_z'] );
		$instance['show_icon'] = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg'] = strip_tags( $new_instance['show_svg'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['square'] = $new_instance['square'];
		$instance['current_date']   = ! empty( $new_instance['current_date'] ) ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '时光静好';
		}
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
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
		<span class="btn-option-fl" style="margin: 0 10px 0 0;">样式</span>
		<span class="btn-option-radio-wrapper">
			<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'square' ) ); ?>" value="round-progress" <?php checked( $instance['square'], 'round-progress' ); ?>/>圆形</label>
			<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'square' ) ); ?>" value="square-progress" <?php checked( $instance['square'], 'square-progress' ); ?>/>柱形</label>
		</span>
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'current_date' ) ); ?>" style="margin: 0 10px 0 0;">时间</label>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'current_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'current_date' ) ); ?>" <?php checked( (bool) $instance["current_date"], true ); ?>>
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'countdown_widget_init' );
function countdown_widget_init() {
	register_widget( 'Countdown_Be_Widget' );
}