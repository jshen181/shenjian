<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 文字截断
function begin_strimwidth( $str, $start, $width, $trimmarker ) {
	$output = preg_replace( '/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $start . '}((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $width . '}).*/s', '\1', $str );
	return $output . $trimmarker;
}

// begin_trim_words()
function begin_trim_words() {
	if ( has_excerpt( '' ) ) {
		if ( get_bloginfo( 'language' ) === 'en-US' ) {
			echo wp_trim_words( get_the_excerpt(), zm_get_option( 'en_word_n' ), '...' );
		} else {
			echo wp_trim_words( get_the_excerpt(), zm_get_option( 'word_n' ), '...' );
		}
	} else {
		$content = get_the_content();
		$content = strip_shortcodes( $content );
		if ( get_bloginfo( 'language' ) === 'en-US' ) {
			echo begin_strimwidth( strip_tags( $content ), 0, zm_get_option( 'en_words_n' ), '...' );
		} else {
			echo wp_trim_words( $content, zm_get_option( 'words_n' ), '...' );
		}
	}
}

function begin_primary_class() {
	global $wpdb, $post;
	$primary_id       = get_post_meta( get_the_ID(), 'sidebar_l', true ) ? 'primary-l' : 'primary';
	$no_sidebar_class = ( get_post_meta( get_the_ID(), 'no_sidebar', true ) || zm_get_option( 'single_no_sidebar' ) ) ? ' no-sidebar' : '';
	echo '<div id="' . $primary_id . '" class="content-area' . $no_sidebar_class . '">';
}

function begin_abstract() {
	global $wpdb, $post;
	if ( has_excerpt() ) {
		$no_abstract_class = get_post_meta( get_the_ID(), 'no_abstract', true ) ? ' no_abstract' : '';
		echo '<span class="abstract' . $no_abstract_class . '">';
		echo '<fieldset><legend>' . __( '摘要', 'begin' ) . '</legend>';
		echo wp_trim_words( get_the_excerpt(), zm_get_option( 'excerpt_n' ), '...' );
		echo '<div class="clear"></div></fieldset></span>';
	}
}

function bedown_show() {
	if ( zm_get_option( 'be_down_show' ) && ! get_post_meta( get_the_ID(), 'ed_down_start', true ) ) {
		if ( ! get_post_meta( get_the_ID(), 'down_start', true ) && get_post_meta( get_the_ID(), 'start_down', true ) || get_post_meta( get_the_ID(), 'down_url_free', true ) ) {
			if ( function_exists( 'epd_assets_vip' ) ) {
				echo '<fieldset class="down-content-show erphpdown" id="erphpdown"><legend>资源下载</legend>';
				echo be_erphpdown_show();
				echo '</fieldset>';
			}
		}
	}
}

function indent() {
	if ( zm_get_option( 'p_first' ) && get_bloginfo( 'language' ) !== 'en-US' ) {
		echo ' p-em';
	}
}

// 固定内容
function logic_notice() {
	if ( zm_get_option( 'logic_notice_enable' ) ) {
		$logic_notice = (array) zm_get_option( 'logic_notice' );
		foreach ( $logic_notice as $items ) {
			if ( ! empty( $items['logic_notice_id'] ) ) {
				$cat_ids  = explode( ',', $items['logic_notice_id'] );
				$category = has_term( $cat_ids, 'category' );
				$taobao   = has_term( $cat_ids, 'taobao' );
				$products = has_term( $cat_ids, 'products' );

				if ( $taobao || $products || $category ) {
					if ( empty( $items['logic_inf_border'] ) ) {
						echo '<div class="logic-post-inf betip">';
					} else {
						echo '<div class="logic-post-inf logic-inf-border betip">';
					}

					if ( ! empty( $items['logic_notice_txt'] ) ) {
						echo '<p>';
						echo $items['logic_notice_txt'];
						echo '</p>';
					}

					if ( ! empty( $items['logic_notice_visual'] ) ) {
						echo '<p>';
						echo $items['logic_notice_visual'];
						echo '</p>';
					}
					be_help( $text = '主题选项 → 文章设置 → 固定信息 → 按分类显示', $base = '文章设置', $go = '固定信息' );
					echo '</div>';
					echo '<div class="clear"></div>';
				}
			}
		}
	}
}

// 正文
function content_support_general() {
	if ( zm_get_option( 'all_more' ) && ! get_post_meta( get_the_ID(), 'not_more', true ) ) {
		all_content();
	}

	if ( zm_get_option( 'begin_today' ) && ! get_post_meta( get_the_ID(), 'no_today', true ) ) {
		echo begin_today();
	}

	begin_link_pages();
	relat_post();

	if ( ! zm_get_option( 'be_like_content' ) || ( wp_is_mobile() ) ) {
		be_like();
	}

	if ( zm_get_option( 'single_weixin' ) ) {
		get_template_part( 'template/weixin' );
	}

	echo '<div class="content-empty"></div>';
	get_template_part( 'ad/ads', 'single-b' );
	echo '<footer class="single-footer">';
	begin_single_cat();
	echo '</footer>';
}

function content_support() {
	global $wpdb, $post;
	echo bedown_show();

	if ( zm_get_option( 'copyright_info' ) ) {
		echo '<div class="copyright-post betip">';
		if ( get_post_meta( get_the_ID(), 'post_info', true ) ) {
			echo get_post_meta( get_the_ID(), 'post_info', true );
		} else {
			echo wpautop( zm_get_option( 'copyright_content' ) );
		}
		be_help( '主题选项 → 文章设置 → 固定信息 → 通用固定信息', '文章设置', '固定信息' );
		echo '</div><div class="clear"></div>';
	}
	echo content_support_general();
}

function content_support_down() {
	global $wpdb, $post;
	echo bedown_show();

	if ( zm_get_option( 'copyright_down_info' ) ) {
		echo '<div class="copyright-post betip">';
		echo wpautop( zm_get_option( 'copyright_content_down' ) );
		be_help( '主题选项 → 文章设置 → 固定信息 → 下载模板信息', '文章设置', '固定信息' );
		echo '</div><div class="clear"></div>';
	}
	echo content_support_general();
}

function content_support_vip() {
	global $wpdb, $post;
	echo bedown_show();

	if ( zm_get_option( 'all_more' ) && ! get_post_meta( get_the_ID(), 'not_more', true ) ) {
		all_content();
	}

	if ( zm_get_option( 'begin_today' ) && ! get_post_meta( get_the_ID(), 'no_today', true ) ) {
		echo begin_today();
	}

	begin_link_pages();
	relat_post();

	if ( ! zm_get_option( 'be_like_content' ) || ( wp_is_mobile() ) ) {
		be_like();
	}

	if ( zm_get_option( 'single_weixin' ) ) {
		get_template_part( 'template/weixin' );
	}

	echo '<div class="content-empty"></div>';
	get_template_part( 'ad/ads', 'single-b' );
	echo '<footer class="single-footer">';
	begin_single_cat();
	echo '</footer>';
}

function header_title() {
	$class = zm_get_option( 'title_c' ) ? ' entry-header-c' : '';
	echo '<header class="entry-header' . $class . '">';
}

// 阴影判断
function boxsstart() {
	if ( ! zm_get_option( 'post_no_margin' ) ) {
		echo '<div class="boxs5 scl">';
	}
}
function boxsend() {
	if ( ! zm_get_option( 'post_no_margin' ) ) {
		if ( ! is_single() ) {
			echo '</div>';
		}
	}
}
