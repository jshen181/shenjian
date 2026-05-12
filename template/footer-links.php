<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_front_page() && ! is_paged() ) {
	if ( current_user_can( 'administrator' ) && empty( get_bookmarks() ) ) {
		if ( be_get_option( 'home_much_links' ) == 'btn' ) {
			echo '<div class="much-links-main links-box">首页设置 → 页脚链接<a class="link-add" href="' . home_url() . '/wp-admin/link-add.php" target="_blank"><i class="be be-edit"></i> 添加链接</a></div>';
		}
	}

	if ( ! be_get_option( 'layout' ) || ( be_get_option( 'layout' ) == 'group' ) ) {
		if ( ! be_get_option( 'footer_link_no' ) || ( ! wp_is_mobile() ) ) {
			if ( ! be_get_option( 'home_much_links' ) || ( be_get_option( 'home_much_links' ) == 'much' ) ) {
				echo '<div class="much-links-main links-group">';
				group_much_links();
				echo '</div>';
			}
			if ( be_get_option( 'home_much_links' ) == 'btn' ) {
				echo '<div class="links-group">';
				links_footer();
				echo '</div>';
			}
		}
	} elseif ( ! be_get_option( 'footer_link_no' ) || ( ! wp_is_mobile() ) ) {
		if ( ! be_get_option( 'home_much_links' ) || ( be_get_option( 'home_much_links' ) == 'much' ) ) {
			echo '<div class="much-links-main links-box">';
			much_links();
			echo '</div>';
		}
		if ( be_get_option( 'home_much_links' ) == 'btn' ) {
			echo '<div class="links-box">';
			links_footer();
			echo '</div>';
		}
	}
}
