<?php
// AJAX
add_action('wp_ajax_load_smilies', 'load_smilies_callback');
add_action('wp_ajax_nopriv_load_smilies', 'load_smilies_callback');

function load_smilies_callback() {
	ob_start();
	get_template_part('inc/smiley');
	$smilies_html = ob_get_clean();
	echo $smilies_html;
	wp_die();
}

function be_smilies_src( $old, $img ) {
	return get_template_directory_uri().'/img/smilies/'.$img;
}
function be_smilies(){
	global $wpsmiliestrans;
	$wpsmiliestrans = array(
		':mrgreen:' => 'icon_mrgreen.gif',
		':neutral:' => 'icon_neutral.gif',
		':twisted:' => 'icon_twisted.gif',
		':arrow:' => 'icon_arrow.gif',
		':shock:' => 'icon_eek.gif',
		':smile:' => 'icon_smile.gif',
		':???:' => 'icon_confused.gif',
		':cool:' => 'icon_cool.gif',
		':evil:' => 'icon_evil.gif',
		':grin:' => 'icon_biggrin.gif',
		':idea:' => 'icon_idea.gif',
		':oops:' => 'icon_redface.gif',
		':razz:' => 'icon_razz.gif',
		':roll:' => 'icon_rolleyes.gif',
		':wink:' => 'icon_wink.gif',
		':cry:' => 'icon_cry.gif',
		':eek:' => 'icon_surprised.gif',
		':lol:' => 'icon_lol.gif',
		':mad:' => 'icon_mad.gif',
		':sad:' => 'icon_sad.gif',
		'8-)' => 'icon_cool.gif',
		'8-O' => 'icon_eek.gif',
		':-(' => 'icon_sad.gif',
		':-)' => 'icon_smile.gif',
		':-?' => 'icon_confused.gif',
		':-D' => 'icon_biggrin.gif',
		':-P' => 'icon_razz.gif',
		':-o' => 'icon_surprised.gif',
		':-x' => 'icon_mad.gif',
		':-|' => 'icon_neutral.gif',
		';-)' => 'icon_wink.gif',
		'8O' => 'icon_eek.gif',
		':(' => 'icon_sad.gif',
		':)' => 'icon_smile.gif',
		':?' => 'icon_confused.gif',
		':D' => 'icon_biggrin.gif',
		':P' => 'icon_razz.gif',
		':o' => 'icon_surprised.gif',
		':x' => 'icon_mad.gif',
		':|' => 'icon_neutral.gif',
		';)' => 'icon_wink.gif',
		':!:' => 'icon_exclaim.gif',
		':?:' => 'icon_question.gif',
	);
	add_filter( 'smilies_src' , 'be_smilies_src' , 10 , 2 );
}
add_action( 'init', 'be_smilies', 5 );