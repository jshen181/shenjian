<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 历年同日文章
function begin_today() {
	global $wpdb;
	$today_post = '';
	$result     = '';
	$post_year  = get_the_time( 'Y' );
	$post_month = get_the_time( 'm' );
	$post_day   = get_the_time( 'j' );

	$sql = $wpdb->prepare(
		"SELECT ID, year(post_date_gmt) as today_year, post_title, comment_count 
		FROM $wpdb->posts 
		WHERE post_password = '' 
		AND post_type = 'post' 
		AND post_status = 'publish'
		AND year(post_date_gmt) != %s 
		AND month(post_date_gmt) = %s 
		AND day(post_date_gmt) = %s
		ORDER BY post_date_gmt DESC 
		LIMIT 8",
		$post_year,
		$post_month,
		$post_day
	);

	$history_posts = $wpdb->get_results( $sql );
	if ( $history_posts ) {
		foreach ( $history_posts as $history_post ) {
			$today_year       = $history_post->today_year;
			$today_post_title = $history_post->post_title;
			$today_permalink  = get_permalink( $history_post->ID );
			$today_post      .= '<li><a href="' . esc_url( $today_permalink ) . '" target="_blank"><span>' . esc_html( $today_year ) . '</span>' . esc_html( $today_post_title ) . '</a></li>';
		}
	}

	if ( $today_post ) {
		$result = sprintf(
			'<div class="begin-today">
				<fieldset>
					<legend>
						<h5>%s</h5>
					</legend>
					<div class="today-date">
						<div class="today-m">%s</div><div class="today-d">%s</div>
					</div>
					<ul class="rp">%s</ul>
				</fieldset>
			</div>',
			esc_html__( '历年同日文章', 'begin' ),
			esc_html( get_the_date( 'F' ) ),
			esc_html( get_the_date( 'j' ) ),
			$today_post
		);
	}

	return $result;
}
