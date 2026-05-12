<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 嵌入地图
function be_map_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'key'      => '7b74d82b37fc458e0c4930bdd30b6a17',
			'position' => '116.397427,39.909249',
			'zoom'     => '16',
			'height'   => '400', // 高度
			'name'     => '我的位置',
		),
		$atts,
		'amap_map'
	);

	// 解析位置参数
	list( $lng, $lat ) = explode( ',', $atts['position'] );
	ob_start();
	?>

	<div class="map-content"><div id="mapgd" style="height: <?php echo esc_attr( $atts['height'] ); ?>px;"></div></div>
	<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=<?php echo esc_attr( $atts['key'] ); ?>"></script>
	<script type="text/javascript">
		var position = new AMap.LngLat(<?php echo esc_js( $lng ); ?>, <?php echo esc_js( $lat ); ?>);
		// 创建地图实例
		var map = new AMap.Map("mapgd", {
			zoom: <?php echo esc_attr( $atts['zoom'] ); ?>,
			center: position,
			resizeEnable: true
		});
		// 标记图标
		var markerContent = '' +
			'<div class="custom-content-marker">' +
			'<div class="namemarker"><?php echo esc_attr( $atts['name'] ); ?></div>' + 
			'<svg t="1732628228291" class="icon" style="width: 2em;height: 2em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9778"><path d="M511.911544 0a350.42548 350.42548 0 0 0-245.999857 597.522245l245.999857 247.535528 246.072985-247.535528A350.42548 350.42548 0 0 0 511.911544 0.073127z m0 505.528244a155.102764 155.102764 0 0 1-154.737127-155.4684 155.029637 155.029637 0 0 1 154.664-155.468399 155.102764 155.102764 0 0 1 154.737128 155.468399A155.029637 155.029637 0 0 1 512.057799 505.455117z m-154.737127 453.38856c0 35.905449 69.251446 65.010069 154.737127 65.010069s154.737128-29.250875 154.737128-65.010069-69.251446-65.010069-154.737128-65.01007-154.664001 28.958366-154.664 64.863815z" fill="#D9001B" p-id="9779" data-spm-anchor-id="a313x.search_index.0.i0.29f93a81dKALUq"></path></svg>' +
			'' +
			'</div>';

		var marker = new AMap.Marker({
			position: position,
			content: markerContent,
			offset: new AMap.Pixel(-13, -30), // 调整标记的显示位置
		});

		map.add(marker);

		function clearMarker() {
			map.remove(marker);
		}
	</script>
	<?php
	return ob_get_clean();
}

// 短代码 [mapgd height="500" key="7b74d82b37fc458e0c4930bdd30b6a17" position="123.494473,41.633617" zoom="18" name="我的位置"]
add_shortcode( 'mapgd', 'be_map_shortcode' );