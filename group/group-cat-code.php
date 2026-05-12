<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( co_get_option( 'group_ajax_cat' ) ) {
	if ( ! co_get_option( 'ajax_bg' ) || ( co_get_option( 'ajax_bg' ) == 'auto' ) ) {
		$bg = '';
	}
	if ( co_get_option( 'ajax_bg' ) == 'white' ) {
		$bg = ' group-white';
	}
	if ( co_get_option( 'ajax_bg' ) == 'gray' ) {
		$bg = ' group-gray';
	}
?>
<?php $hm = ( ! co_get_option( 'group_ajax_code_title_m' ) || co_get_option( 'group_ajax_code_title_m' ) == 'yes' ) ? ' group-tabs-show' : ''; ?>
<div class="g-row g-line group-tabs-line<?php echo $bg; ?>" <?php aos(); ?>>
	<div class="g-col">
		<div class="group-tabs-content group-tab-img-meta<?php if ( co_get_option( 'group_ajax_code_title_h' ) ) { ?> group-tabs-fold<?php echo $hm; ?><?php } ?><?php if ( co_get_option( 'group_ajax_code_c' ) ) { ?> group-tab-title-c<?php } ?>">
			<?php if ( ! co_get_option( 'group_ajax_code_t' ) == '' ) { ?>
				<div class="group-title" <?php aos_b(); ?>>
					<?php if ( ! co_get_option('group_ajax_code_t') == '' ) { ?>
						<h3><?php echo co_get_option( 'group_ajax_code_t' ); ?></h3>
					<?php } ?>
					<?php if ( ! co_get_option( 'group_ajax_code_des' ) == '' ) { ?>
						<div class="group-des group-tab-des"><?php echo co_get_option( 'group_ajax_code_des' ); ?></div>
					<?php } ?>
					<div class="clear"></div>
				</div>
			<?php } ?>
			<?php if ( co_get_option( 'group_ajax_code_no_btn' ) ) { ?><div class="group-margin-btn"></div><?php } ?>
			<?php echo do_shortcode( co_get_option( 'group_ajax_cat_post_code' ) ); ?>
			<div class="clear"></div>
		</div>
		<?php co_help( $text = '公司主页 → 分类短代码', $number = 'group_ajax_cat_post_s', $go = '分类短代码' ); ?>
	</div>
</div>
<?php } ?>