<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
// remove markup
function remove_shortcode_markup( $string ) {
	$patterns = array(
		'#^\s*</p>#',
		'#<p>\s*$#',
	);
	return preg_replace( $patterns, '', $string );
}

// 过滤多余空格
if ( ! function_exists( 'filter_shortcode_text' ) ) {
	function filter_shortcode_text( $content ) {
		$array   = array(
			'<p>&nbsp;</p>' => '',
		);
		$content = strtr( $content, $array );
		return $content;
	}
	// add_filter( 'the_content', 'filter_shortcode_text' );
}

// 短代码背景图片
function get_secret_img() {
	$beimg = str_replace( array( "\n", "\r", ' ' ), '', explode( ',', zm_get_option( 'secret_image_url' ) ) );
	return $beimg[ array_rand( $beimg ) ];
}

// 加载短代码文件
function be_load_shortcode() {
	$shortcode_path = get_template_directory() . '/inc/shortcode/toolbar';

	if ( ! is_dir( $shortcode_path ) ) {
		return;
	}

	$shortcode_files = glob( $shortcode_path . '/sc-*.php' );

	if ( $shortcode_files ) {
		foreach ( $shortcode_files as $file ) {
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	}
}

// 加载短代码
add_action( 'after_setup_theme', 'be_load_shortcode' );


// 幻灯
function gallery( $atts, $content = null ) {
	$pure_content = str_replace( array( '<br />', '</p>', "\n", "\r" ), '', $content );
	return '<div id="slider-single" class="owl-carousel slider-single be-wol">' . do_shortcode( do_shortcode( remove_shortcode_markup( $pure_content ) ) ) . '</div>';
}

// 下载按钮
function button_a( $atts, $content = null ) {
	return '<div class="down"><a class="d-popup" title="下载链接"><i class="be be-download"></i>下载地址</a><div class="clear"></div></div>';
}

// 弹窗按钮
function button_b( $atts, $content = null ) {
	return '<div class="down"><a class="d-popup" href="#"><div class="btnico"><i class="be be-clouddownload"></i></div><div class="btntxt">' . do_shortcode( do_shortcode( $content ) ) . '</div></a><div class="clear"></div></div>';
}

// 下载按钮
function button_url( $atts, $content = null ) {
	global $wpdb, $post;
	extract( shortcode_atts( array( 'href' => 'http://' ), $atts ) );
	return '<div class="down down-link"><a href="' . $href . '" rel="external nofollow" target="_blank"><div class="btnico"><i class="be be-download"></i></div><div class="btntxt">' . do_shortcode( do_shortcode( $content ) ) . '</div></a></div><div class="clear"></div>';
}

// 链接按钮
function button_link( $atts, $content = null ) {
	global $wpdb, $post;
	extract( shortcode_atts( array( 'href' => 'http://' ), $atts ) );
	return '<div class="down down-link"><a href="' . $href . '" rel="external nofollow" target="_blank"><div class="btnico"><i class="be be-edit"></i></div><div class="btntxt">' . do_shortcode( do_shortcode( $content ) ) . '</div></a></div><div class="clear"></div>';
}

// but
function button_c( $atts, $content = null ) {
	extract( shortcode_atts( array( 'href' => 'http://' ), $atts ) );
	return '<div class="down down-link down-link-but"><a href="' . $href . '" rel="external nofollow" target="_blank"><div class="btnico"><i class="be be-download"></i></div><div class="btntxt">' . do_shortcode( do_shortcode( $content ) ) . '</div></a></div><div class="clear"></div>';
}

// iframe标签
function fancy_iframe( $atts, $content = null ) {
	extract( shortcode_atts( array( 'href' => 'http://' ), $atts ) );
	return '<div class="down down-link down-link-but"><a class="fancy-iframe" data-type="iframe" data-src="' . $href . '" href="javascript:;" rel="external nofollow" target="_blank"><div class="btnico"><i class="be be-star"></i></div><div class="btntxt">' . do_shortcode( do_shortcode( $content ) ) . '</div></a></div><div class="clear"></div>';
}

// fieldset标签
function fieldset_label( $atts, $content = null ) {
	return do_shortcode( $content );
}

// Wide picture
function add_full_img( $atts, $content = null, $full_img = '' ) {
	$return  = '<div class="full-img">';
	$return .= do_shortcode( $content );
	$return .= '</div>';
	return $return;
}

// 中英文混排
function cn_en( $atts, $content = null ) {
	$return  = '<p class="cnen">';
	$return .= do_shortcode( $content );
	$return .= '</p>';
	return $return;
}

// 隐藏图片
function add_hide_img( $atts, $content = null, $hide_img = '' ) {
	$return  = '<div class="hide-img">';
	$return .= do_shortcode( $content );
	$return .= '</div>';
	return $return;
}

// 字体图标
function be_font_shortcode( $atts ) {
	extract(
		shortcode_atts(
			array(
				'icon'  => 'home',
				'size'  => '',
				'color' => '',
				'sup'   => '',
			),
			$atts
		)
	);
	if ( $size ) {
		$size = ' font-size: ' . $size . 'px !important'; } else {
		$size = ''; }
		if ( $color ) {
			$color = ' style="padding: 1px 2px;color: #' . $color . ';'; } else {
			$color = ''; }

			if ( strtolower( $sup ) === '1' ) {
				return '<sup><i class="zm zm-' . str_replace( 'zm-', '', $icon ) . '"' . $color . $size . '"></i></sup>';
			} else {
				return '<i class="zm zm-' . str_replace( 'zm-', '', $icon ) . '"' . $color . $size . '"></i>';
			}
}

// 两栏
function add_two_column( $atts, $content = null, $two_column = '' ) {
	$return  = '<div class="two-column">';
	$return .= do_shortcode( $content );
	$return .= '</div>';
	return $return;
}

// 无缩进居中
function add_center_align( $atts, $content = null, $two_column = '' ) {
	extract(
		shortcode_atts(
			array(
				'center' => '',
			),
			$atts
		)
	);
	if ( strtolower( $center ) === '1' ) {
		$center = ' align-center';
	} else {
		$center = '';
	}
	$return  = '<div class="center-align' . $center . '">';
	$return .= do_shortcode( $content );
	$return .= '</div>';
	return $return;
}

add_shortcode( 'align', 'add_center_align' );

// bec
function add_bec( $atts, $content = null, $bec = '' ) {
	$return  = '<div class="bec"><span class="dashicons dashicons-admin-site"></span>';
	$return .= do_shortcode( $content );
	$return .= '</div>';
	$return .= '<div class="clear"></div>';
	return $return;
}

// Same label
function tags_posts( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'ids'   => '',
				'title' => '',
				'n'     => '',
			),
			$atts
		)
	);
	$content .= '<div class="tags-posts"><h3>' . $title . '</h3><ul>';
	$recent   = new WP_Query(
		array(
			'posts_per_page' => $n,
			'tag__in'        => explode( ',', $ids ),
			'no_found_rows'  => true,
		)
	);
	while ( $recent->have_posts() ) :
		$recent->the_post();
		$content .= '<li><a target="_blank" href="' . get_permalink() . '"><i class="be be-arrowright"></i>' . get_the_title() . '</a></li>';
	endwhile;
	wp_reset_postdata();
	$content .= '</ul></div>';
	return $content;
}

// advertising
function post_ad() {
	if ( wp_is_mobile() ) {
		return '<div class="post-tg"><div class="tg-m tg-site">' . stripslashes( zm_get_option( 'ad_s_z_m' ) ) . '</div></div>';
	} else {
		return '<div class="post-tg"><div class="tg-pc tg-site">' . stripslashes( zm_get_option( 'ad_s_z' ) ) . '</div></div>';
	}
}

// 直达按钮
function direct_btn() {
	global $post;
	if ( get_post_meta( get_the_ID(), 'direct', true ) ) {
		$direct = get_post_meta( get_the_ID(), 'direct', true );
		if ( get_post_meta( get_the_ID(), 'direct_btn', true ) ) {
			$direct_btn = get_post_meta( get_the_ID(), 'direct_btn', true );
			return '<div class="down-doc-box"><div class="down-doc down-doc-go"><a href="' . $direct . '" target="_blank" rel="external nofollow">' . $direct_btn . '</a><a href="' . $direct . '" rel="external nofollow" target="_blank"><i class="be be-skyatlas"></i></a></div></div><div class="clear"></div>';
		} else {
			return '<div class="down-doc-box"><div class="down-doc down-doc-go"><a href="' . $direct . '" target="_blank" rel="external nofollow">' . zm_get_option( 'direct_w' ) . '</a><a href="' . $direct . '" rel="external nofollow" target="_blank"><i class="be be-skyatlas"></i></a></div></div><div class="clear"></div>';
		}
	}
}

// Fixed button
function down_doc_box( $content ) {
	global $post;
	$link_button = get_post_meta( get_the_ID(), 'down_doc', true );
	$doc_name    = get_post_meta( get_the_ID(), 'doc_name', true );
	if ( get_post_meta( get_the_ID(), 'down_doc', true ) ) {
		if ( get_post_meta( get_the_ID(), 'doc_name', true ) ) {
			$down_doc_name = $doc_name;
		} else {
			$down_doc_name = sprintf( __( '下载地址', 'begin' ) );
		}
		$content = $content . '<div class="down-doc-box"><div class="down-doc"><a href="' . $link_button . '" rel="external nofollow" target="_blank">' . $down_doc_name . '</a><a href="' . $link_button . '" rel="external nofollow" target="_blank"><i class="be be-download"></i></a></div></div><div class="clear"></div>';
	}
	return $content;
}

// prompt
function be_green( $atts, $content = null ) {
	return '<div class="mark_a mark">' . do_shortcode( $content ) . '</div>';
}

function be_red( $atts, $content = null ) {
	return '<div class="mark_b mark">' . do_shortcode( $content ) . '</div>';
}

function be_gray( $atts, $content = null ) {
	return '<div class="mark_c mark">' . do_shortcode( $content ) . '</div>';
}

function be_yellow( $atts, $content = null ) {
	return '<div class="mark_d mark">' . do_shortcode( $content ) . '</div>';
}

function be_blue( $atts, $content = null ) {
	return '<div class="mark_e mark">' . do_shortcode( $content ) . '</div>';
}

// 短代码按钮
function begin_add_mce_button() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'begin_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'begin_register_mce_button' );
	}
}

function begin_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['begin_mce_button'] = get_bloginfo( 'template_url' ) . '/inc/tinymce/mce-button.js';
	return $plugin_array;
}
function begin_register_mce_button( $buttons ) {
	array_push( $buttons, 'begin_mce_button' );
	return $buttons;
}

// 图文模块
function wplist_shortcode( $atts, $content = '' ) {
	$atts['content'] = $content;
	$out             = '<div class="wplist-item">';
	if ( ! empty( $atts['link'] ) ) {
		$out .= '<figure class="thumbnail"><div class="thumbs-b lazy"><a class="thumbs-back sc" rel="external nofollow" href="' . $atts['link'] . '" style="background-image: url(' . $atts['img'] . ');"></a></div></figure>';
		$out .= '<a href="' . $atts['link'] . '" target="_blank" isconvert="1" rel="nofollow" ><div class="wplist-title">' . $atts['title'] . '</div></a>';
	} else {
		$out .= '<figure class="thumbnail"><div class="thumbs-b lazy thumbs-back sc"><a class="thumbs-back sc" rel="external nofollow" href="" style="background-image: url(' . $atts['img'] . ');"></a></div></figure>';
		$out .= '<div class="wplist-title">' . $atts['title'] . '</div>';
	}
	$out .= '<div class="wplist-des">' . $atts['content'] . '</div>';
	$out .= '<div class="wplist-link-btn">';
	if ( ! empty( $atts['price'] ) ) {
		$out .= '<div class="wplist-oth"><div class="wplist-res wplist-price">' . $atts['price'] . '</div>';
		if ( ! empty( $atts['oprice'] ) ) {
			$out .= '<div class="wplist-res wplist-old-price"><del>' . $atts['oprice'] . '</del></div>';
		}
		$out .= '</div>';
	}
	if ( ! empty( $atts['link'] ) ) {
		$out .= '<a href="' . $atts['link'] . '" target="_blank" isconvert="1" rel="nofollow" ><div class="wplist-btn">' . $atts['btn'] . '</div></a><div class="clear"></div>';
	}
	$out .= '</div>';
	$out .= '<div class="clear"></div></div>';
	return $out;
}

// iframe
function iframe_add_shortcode( $atts ) {
	extract(
		shortcode_atts(
			array(
				'src' => '',
			),
			$atts
		)
	);

	return '<div class="iframe-class"><iframe src="' . $src . '" security="restricted" allowfullscreen></iframe></div>';
}

// serial number
function serial_number( $atts ) {
	extract(
		shortcode_atts(
			array(
				'text' => '',
			),
			$atts
		)
	);
	return '<div class="serial-number"><div class="borde"></div><div class="borde"></div><span class="serial-txt">' . $text . '</span></div>';
}

// subhead number
function subhead_number( $atts, $content = null ) {
	return '<div class="subhead-area"><div class="subhead-number-bg"><div class="subhead-number"></div></div><span class="subhead-txt">' . do_shortcode( $content ) . '</span></div>';
}

// 随机文章
function random_post_shortcode() {
	ob_start();
	random_post();
	return ob_get_clean();
}

global $pagenow;
if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'page.php', 'page-new.php' ) ) ) {
	add_action( 'init', 'begin_add_mce_button' );
}

// 登录按钮
function login_reg( $atts, $content = null ) {
	extract( shortcode_atts( array( 'sup' => '' ), $atts ) );
	if ( ! is_user_logged_in() ) {
		if ( strtolower( $sup ) === '1' ) {
			$hmtl = ' login-reg-btn';
		} else {
			$hmtl = '';
		}
		return '<span class="btn-login' . $hmtl . ' show-layer' . cur() . '">' . do_shortcode( do_shortcode( $content ) ) . '</span>';
	}
}

// docs
function be_docs_point( $atts, $content = null ) {
	return '<div class="docs-point-box fd"><div class="docs-point-main">' . do_shortcode( $content ) . '</div></div>';
}

// first drop
function be_first_drop( $atts, $content = null ) {
	return '<i class="be-first-drop"><p>' . do_shortcode( $content ) . '</p></i>';
}

// details标签
function be_details_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title' => '',
				'open'  => '',
			),
			$atts
		)
	);
	if ( strtolower( $open ) === '1' ) {
		$open = ' open=""';
	} else {
		$open = '';
	}
	$html  = '<div class="be-details">';
	$html .= '<details' . $open . '>';
	$html .= '<summary>';
	$html .= $title;
	$html .= '</summary>';
	$html .= do_shortcode( do_shortcode( $content ) );
	$html .= '</details>';
	$html .= '</div>';
	return $html;
}

add_shortcode( 'details', 'be_details_shortcode' );

// 复制按钮
function btn_copytext( $atts = array(), $content = null ) {
	extract(
		shortcode_atts(
			array(
				'tip' => '',
			),
			$atts
		)
	);

	if ( strtolower( $tip ) === '1' ) {
		$html = '';
	} else {
		$html = ' textbox-no-tip';
	}

	return '<span class="textbox' . $html . '"><span class="btn-copy">' . sprintf( __( '点击复制', 'begin' ) ) . '</span><span class="copied-text">' . do_shortcode( do_shortcode( $content ) ) . '</span></span>';
}

add_shortcode( 'copy', 'btn_copytext' );

// 禁止解析
function shortcode_demo( $atts, $content = null ) {
	return $content;
}
add_shortcode( 'ban', 'shortcode_demo' );


// 文字块
function text_block_shortcode( $atts, $content = null ) {
	return '<div class="textblock">' . wp_kses_post( $content ) . '</div>';
}

add_shortcode( 'textblock', 'text_block_shortcode' );

// 图片块
function img_block_shortcode( $atts, $content = null ) {
	return '<div class="imgblock">' . wp_kses_post( $content ) . '</div>';
}

add_shortcode( 'imgblock', 'img_block_shortcode' );
