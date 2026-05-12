<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( zm_get_option( 'slider_captcha' ) || zm_get_option( 'verify_comment' ) ) {
	add_action( 'wp_enqueue_scripts', 'be_captcha_scripts' );
}

if ( zm_get_option( 'slider_captcha' ) ) {
	add_action( 'be_login_form', 'add_slidercaptcha_form' );
	add_action( 'be_lostpassword_form', 'add_slidercaptcha_form' );
	add_action( 'be_register_form', 'add_slidercaptcha_form' );
}

if ( zm_get_option( 'verify_comment' ) ) {
	add_filter( 'be_comment_form', 'add_slidercaptcha_form', 10, 1 );
}

function be_captcha_scripts() {
	wp_enqueue_script( 'captcha', get_template_directory_uri() . '/js/captcha.js', array( 'jquery' ), version, true );
	$captcha_img_urls           = zm_get_option( 'captcha_img_url' );
	$captcha_img_array          = $captcha_img_urls ? explode( ',', $captcha_img_urls ) : array();
	$default_image_url          = get_template_directory_uri() . '/img/default/captcha/y1.jpg';
	$captcha_img_array          = $captcha_img_array ?: array( $default_image_url );
	$captcha_img                = 'var captcha_images = ' . wp_json_encode( $captcha_img_array ) . ';';
	$captcha_ajax_data          = array( 'ajax_url' => admin_url( 'admin-ajax.php' ) );
	$captcha_ajax_data['nonce'] = wp_create_nonce( 'be_captcha_nonce' );
	wp_localize_script( 'captcha', 'verify_ajax', $captcha_ajax_data );
	wp_add_inline_script( 'captcha', $captcha_img, 'after' );
}

function add_slidercaptcha_form() {
	if ( ! session_id() ) {
		session_start();
	}

	$header_text    = esc_html__( '拖动滑块以完成验证', 'begin' );
	$slider_text    = esc_html__( '向右滑动完成拼图', 'begin' );
	$try_again_text = esc_html__( '请再试一次', 'begin' );

	?>
	<div class="slidercaptcha-box">
		<div class="bec-slidercaptcha bec-card">
			<div class="becclose"></div>
			<div class="refreshimg"></div>
			<div class="bec-card-header">
				<span><?php echo esc_html( $header_text ); ?></span>
			</div>
			<div class="bec-card-body"><div data-heading="<?php echo esc_attr( $header_text ); ?>" data-slider="<?php echo esc_attr( $slider_text ); ?>" data-tryagain="<?php echo esc_attr( $try_again_text ); ?>" data-form="login" class="bec-captcha"></div></div>
		</div>
	</div>
	<?php
}

function be_ajax_verify_callback() {
	if ( ! session_id() ) {
		session_start();
	}
	if ( ! check_ajax_referer( 'be_captcha_nonce', 'security', false ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	$allowed_forms = array( 'login', 'register', 'lostpassword', 'comment' );
	$form          = isset( $_POST['form'] ) ? sanitize_text_field( $_POST['form'] ) : '';
	if ( ! in_array( $form, $allowed_forms ) ) {
		wp_send_json_error( 'Invalid form type' );
	}

	$_SESSION['captcha_verified'] = true;
	$_SESSION['captcha_form']     = $form;
	$_SESSION['captcha_expire']   = time() + 300;
	wp_send_json_success( 'verified' );
}

add_action( 'wp_ajax_be_ajax_verify', 'be_ajax_verify_callback' );
add_action( 'wp_ajax_nopriv_be_ajax_verify', 'be_ajax_verify_callback' );

function verify_slider_captcha() {
	if ( ! session_id() ) {
		session_start();
	}

	if ( ! isset( $_SESSION['captcha_verified'] ) || $_SESSION['captcha_verified'] !== true ) {
		wp_send_json_error( __( '验证失败，请重试', 'begin' ) );
		return false;
	}

	$_SESSION['captcha_expire'] = time() + 60; // 5分钟有效期
	return true;
}
