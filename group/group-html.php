<?php
// 自定义模块
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! co_get_option( 'group_html' ) ) {
	return;
}

$items = $args['items'] ?? array();

$args = array(
	'auto'  => '',
	'white' => ' group-white',
	'gray'  => ' group-gray',
);

$bg = '';
if ( isset( $items['group_html_bg'] ) && isset( $args[ $items['group_html_bg'] ] ) ) {
	$bg = $args[ $items['group_html_bg'] ];
}

$col_class = '';
if ( isset( $items['group_html_m'] ) ) {
	if ( $items['group_html_m'] == 'group_html_normal' ) {
		$col_class = ' g-col';
	} elseif ( $items['group_html_m'] == 'group_html_full' ) {
		$col_class = ' g-col-full';
	}
}
?>
<div class="g-row g-line sort<?php echo $bg; ?>" <?php aos(); ?>>
	<div class="group-html be-text<?php echo $col_class; ?>">
		<div class="section-box group-html-wrap">
			<?php if ( ! empty( $items['group_html_t'] ) ) : ?>
				<div class="group-title" <?php aos_b(); ?>>
					<h3><?php echo $items['group_html_t']; ?></h3>
					<div class="clear"></div>
				</div>
			<?php endif; ?>
			
			<div class="group-html-text" <?php aos_f(); ?>>
				<?php echo do_shortcode( $items['group_html_text'] ?? '' ); ?>
			</div>
			
			<div class="clear"></div>
		</div>
		
		<?php
		if ( isset( $items['group_html_s'] ) ) {
			co_help_n( $text = '公司主页 → 自定义', $items['group_html_s'], $go = '自定义' );
		}
		?>
		
		<div class="clear"></div>
	</div>
</div>
