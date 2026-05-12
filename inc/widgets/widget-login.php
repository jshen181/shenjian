<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 用户登录
class Be_Ajax_login extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'ajax-login-widget',
			'description' => 'Ajax 登录注册小工具',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'Be_Ajax_login', '登录注册', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'login_back'   => get_template_directory_uri() . '/img/default/options/user.jpg',
			'login_avatar' => get_template_directory_uri() . '/img/favicon.png"',
			'login_tip'    => '访客，请登录！',
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		echo $args['before_widget'];
	?>

	<div class="be-login-widget">
		<?php if ( ! is_user_logged_in() ) { ?>
			<?php if ( ! zm_get_option( 'only_social_login' ) ) { ?>
				<div class="sidebox">
					<?php if ( $instance[ 'login_back' ]  ) { ?>
						<div class="author-back"><img src="<?php echo $instance['login_back']; ?>" alt="bj"/></div>
					<?php } ?>

					<div class="usericon load">
						<img alt="<?php echo $instance['login_tip']; ?>" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<?php echo $instance['login_avatar']; ?>" class="default-avatar" height="96" width="96">
					</div>

					<div class="no-login-name">
						<?php if ( isset( $_COOKIE["comment_author_" . COOKIEHASH] ) && $_COOKIE["comment_author_" . COOKIEHASH] != "" ) { ?>
							<?php printf( __('%s，您还未登录！'), $_COOKIE["comment_author_" . COOKIEHASH] ) ?>
						<?php } else { ?>
							<?php echo $instance['login_tip']; ?>
						<?php } ?>
					</div>
					<div class="login-form">
						<?php do_action( 'be_login_form' ); ?>
						<?php do_action( 'login_form' ); ?>
					</div>
					<div class="userinfo">
						<div>
							<?php if ( ! zm_get_option( 'user_l' ) == '' ) { ?>
								<span class="widget-login-reg-btn widget-login-btn-l"><a href="<?php echo zm_get_option( 'user_l' ); ?>" rel="external nofollow" target="_blank"><?php _e( '登录', 'begin' ); ?></a></span>
							<?php } else { ?>
								<span class="widget-login-reg-btn widget-login-btn show-layer<?php echo cur(); ?>"><?php _e( '登录', 'begin' ); ?></span>
							<?php } ?>
							<?php if ( zm_get_option( 'menu_reg' ) && get_option( 'users_can_register' ) ) { ?>
								 <span class="widget-login-reg-btn widget-reg-btn"><a href="<?php echo zm_get_option( 'reg_l' ); ?>" rel="external nofollow" target="_blank"><?php _e( '注册', 'begin' ); ?></a></span>
							 <?php } ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<?php only_social(); ?>
			<?php } ?>
		<?php } else { ?>
			<?php be_login_reg(); ?>
		<?php } ?>
	</div>

	<?php
		echo $args['after_widget'];
	}
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance = array();
			$instance['login_back']   = $new_instance['login_back'];
			$instance['login_avatar'] = $new_instance['login_avatar'];
			$instance['login_tip']    = $new_instance['login_tip'];
			return $instance;
		}
		function form($instance) {
			global $wpdb;
			$defaults     = $this -> zm_defaults();
			$instance     = wp_parse_args( ( array ) $instance, $defaults );
			$login_back   = $instance['login_back'];
			$login_avatar = $instance['login_avatar'];
			$login_tip    = $instance['login_tip'];
	?>

	<p>
		<label for="<?php echo $this->get_field_id( 'login_back' ); ?>">背景图片：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'login_back' ); ?>" name="<?php echo $this->get_field_name( 'login_back' ); ?>" type="text" value="<?php echo $login_back; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('login_avatar'); ?>">默认头像 ( 正方图片 )：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'login_avatar' ); ?>" name="<?php echo $this->get_field_name( 'login_avatar' ); ?>" type="text" value="<?php echo $login_avatar; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'login_tip' ); ?>">提示文字：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'login_tip' ); ?>" name="<?php echo $this->get_field_name( 'login_tip' ); ?>" type="text" value="<?php echo $login_tip; ?>" />
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
<?php }
}

function ajax_login_init() {
	register_widget( 'Be_Ajax_login' );
}
add_action( 'widgets_init', 'ajax_login_init' );