<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( be_build( get_the_ID(), 'group_tab' ) ) {
	$args = [
		'auto'  => '',
		'white' => ' group-white',
		'gray'  => ' group-gray',
	];
	$bg = '';
	if ( isset( $items['tab_bg'] ) && isset( $args[$items['tab_bg']] ) ) {
		$bg = $args[$items['tab_bg']];
	}

	$show = [
		'yes' => ' group-tabs-show',
		'no'  => '',
	];
	$hm = '';
	if ( isset( $items['group_tab_title_m'] ) && isset( $show[$items['group_tab_title_m']] ) ) {
		$hm = $show[$items['group_tab_title_m']];
	}

	if ( empty( $items['group_tab_title_h'] ) ) {
		$boxs = '';
	} else {
		$boxs = 'boxs';
	}
?>

<div class="g-row g-line group-tabs-line<?php echo $bg; ?>" <?php aos(); ?>>
	<div class="g-col">
		<div class="group-tabs-content group-tab-img-meta<?php if ( ! empty( $items['group_tab_title_h'] ) ) { ?> group-tabs-fold<?php echo $hm; ?><?php } ?><?php if ( ! empty( $items['group_tab_title_c'] ) ) { ?> group-tab-title-c<?php } ?>">

			<?php if ( ! empty( $items['group_tab_t'] ) ) { ?>
				<div class="group-title" <?php aos_b(); ?>>
					<?php if ( ! empty( $items['group_tab_t'] ) ) { ?>
						<h3><?php echo $items['group_tab_t']; ?></h3>
					<?php } ?>

					<?php if ( ! empty( $items['group_tab_des'] ) ) { ?>
						<div class="group-des group-tab-des"><?php echo $items['group_tab_des']; ?></div>
					<?php } ?>
					<div class="clear"></div>
				</div>
			<?php } ?>

			<?php 
				if ( ! empty( $items['group_tab_cat_id'] ) ) {
					if ( isset( $items['group_tab_cat_btn'] ) && $items['group_tab_cat_btn'] == 'no' ) {
						echo '<div class="group-margin-btn"></div>';
					}
	
					if ( isset( $items['group_tab_cat_btn'] ) ) {
						$btn = $items['group_tab_cat_btn'];
					} else {
						$btn = '';
					}
					echo do_shortcode( '[be_ajax_post style="' . $items['group_tabs_mode'] . '" terms="' . $items['group_tab_cat_id'] . '" posts_per_page="' . $items['group_tab_n'] . '" column="' . $items['group_tabs_f'] . '" children="' . $items['group_tab_cat_chil'] . '" more="' . $items['group_tabs_nav_btn'] . '" btn="' . $btn . '" btn_all="no" boxs="' . $boxs . '"]' );
				}
			?>

			<div class="clear"></div>
		</div>
		<?php bu_help_n( $text = '分类选项卡', $items['group_tabs_s'] ); ?>
	</div>
</div>
<?php } ?>