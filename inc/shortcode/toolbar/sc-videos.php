<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 视频
function my_videos( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'src' => '""',
			),
			$atts
		)
	);
	return '<div class="video-content"><video src="' . $src . '" controls="controls" width="100%"></video></div>';
}