<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( zm_get_option( 'search_captcha' ) ) {
	// 搜索验证
	function be_search_captcha( $query, $error = true ) {
		if ( is_search() && ! is_admin() ) {
			if ( ! session_id() ) {
				session_start();
			}

			// 检查验证是否过期
			if ( isset( $_SESSION[ zm_get_option( 'search_captcha_cookie' ) ] ) &&
				isset( $_SESSION['captcha_time'] ) &&
				time() - $_SESSION['captcha_time'] > zm_get_option( 'search_captcha_time' ) ) {
				unset( $_SESSION[ zm_get_option( 'search_captcha_cookie' ) ] );
				unset( $_SESSION['captcha_time'] );
			}

			if ( ! isset( $_SESSION[ zm_get_option( 'search_captcha_cookie' ) ] ) ) {
				$query->is_search       = false;
				$query->query_vars['s'] = false;
				$query->query['s']      = false;

				if ( $error == true ) {
					if ( isset( $_POST['result'] ) ) {
						if ( $_POST['result'] == $_SESSION['result'] ) {
							$_SESSION[ zm_get_option( 'search_captcha_cookie' ) ] = 1;
							$_SESSION['captcha_time']                             = time();
							echo '<script>location.reload();</script>';
						}
					}

					$num1               = rand( 1, 10 );
					$num2               = rand( 1, 10 );
					$result             = $num1 + $num2;
					$_SESSION['result'] = $result;

					get_header();

					echo '<title>' . __( '搜索验证', 'begin' );
					connector();
					if ( ! zm_get_option( 'blog_name' ) ) {
						bloginfo( 'name' );
					}
					echo '</title>';

					be_back_img();

					echo '<div class="be-search-captcha-box">';
					echo '<div class="be-search-captcha fd">';
					echo '<div class="be-search-captcha-tip">' . __( '输入答案查看搜索结果', 'begin' ) . '</div>';
					echo '<form action="" method="post" autocomplete="off">';
					echo $num1 . ' + ' . $num2 . ' = <input type="text" name="result" required autofocus />';
					echo '<button type="submit">' . __( '确定', 'begin' ) . '</button>';
					echo '</form>';
					echo '<a class="be-search-captcha-btu" href="' . esc_url( home_url( '/' ) ) . '">' . __( '返回首页', 'begin' ) . '</a>';
					echo '</div>';
					echo '</div>';

					get_footer();
					exit;
				}
			}
		}
	}

	if ( ! current_user_can( 'administrator' ) ) {
		add_action( 'parse_query', 'be_search_captcha' );
		if ( ! isset( $_SESSION['result'] ) ) {
			$_SESSION['result'] = 0;
		}
	}
}
