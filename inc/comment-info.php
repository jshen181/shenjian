<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 评论附加信息
if ( zm_get_option( 'comment_info' ) ) {
		add_action( 'manage_comments_custom_column', 'be_comment_custom_column', 10, 2 );
	function be_comment_custom_column( $column, $comment_id ) {
		switch ( $column ) {
			case 'country':
				$country = get_comment_meta( $comment_id, 'country', true );
				$phone   = get_comment_meta( $comment_id, 'phone', true );
				$extra   = '';
				if ( $country ) { $extra .= esc_html( $country );
				}
				if ( $country && $phone ) { $extra .= ' | ';
				}
				if ( $phone ) { $extra .= esc_html( $phone );
				}
				echo $extra;
				break;
		}
	}

		add_filter( 'manage_edit-comments_columns', 'be_add_comment_columns' );
	function be_add_comment_columns( $columns ) {
		$columns['country'] = zm_get_option( 'country_text' ) . ' | ' . zm_get_option( 'phone_text' );
		return $columns;
	}
}
