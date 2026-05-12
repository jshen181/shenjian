<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 天气
function weather_script() {
	?>
	<?php if ( ! cx_get_option( 'weather_code' ) ) { ?>
		<script>
			(function(a,h,g,f,e,d,c,b){b=function(){d=h.createElement(g);c=h.getElementsByTagName(g)[0];d.src=e;d.charset="utf-8";d.async=1;c.parentNode.insertBefore(d,c)};a["SeniverseWeatherWidgetObject"]=f;a[f]||(a[f]=function(){(a[f].q=a[f].q||[]).push(arguments)});a[f].l=+new Date();if(a.attachEvent){a.attachEvent("onload",b)}else{a.addEventListener("load",b,false)}}(window,document,"script","SeniverseWeatherWidget","//cdn.sencdn.com/widget2/static/js/bundle.js?t="+parseInt((new Date().getTime() / 100000000).toString(),10)));
			window.SeniverseWeatherWidget('show', {
				flavor: "slim",
				location: "WX4FBXXFKE4F",
				geolocation: true,
				language: "auto",
				unit: "c",
				theme: "auto",
				token: "e6260434-75b5-424c-be42-1857b7f16451",
				hover: "enabled",
				container: "tp-weather-widget"
			})
		</script>
	<?php } else { ?>
		<?php echo cx_get_option( 'weather_script' ); ?>
	<?php } ?>
	<?php
}

if ( cx_get_option( 'weather_widget' ) && ( cx_get_option( 'weather_mode' ) != 'top' || ( cx_get_option( 'weather_mode' ) == 'top' && zm_get_option( 'top_nav_show' ) ) ) ) {
	add_action( 'wp_footer', 'weather_script' );
}

// 菜单天气
function add_weather_menu( $items, $args ) {
	if ( $args->theme_location == 'navigation' ) {
		$button = '<li class="weather-menu"><i id="tp-weather-widget"><i class="loadball"><i class="ball"></i><i class="ball"></i><i class="ball"></i></i></i>' . be_help_r( $text = '辅助设置 → 实时天气', $base = '综合设置', $go = '实时天气' ) . '</li>';
		// $items = $button . $items;
		$items .= $button;
	}
	return $items;
}

if ( cx_get_option( 'weather_widget' ) && cx_get_option( 'weather_mode' ) == 'main' ) {
	add_filter( 'wp_nav_menu_items', 'add_weather_menu', 10, 2 );
}
