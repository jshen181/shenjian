<?php if ( ! defined( 'ABSPATH' ) ) exit;
if ( be_build( get_the_ID(), 'group_channel' ) ) {
	$args = [
		'auto'  => '',
		'white' => ' group-white',
		'gray'  => ' group-gray',
	];
	$bg = '';
	if ( isset( $items['group_channel_bg'] ) && isset( $args[$items['group_channel_bg']] ) ) {
		$bg = $args[$items['group_channel_bg']];
	}

	$args = [
		'3' => '3',
		'4' => '4',
	];
	$column = '';
	if ( isset( $items['group_channel_column'] ) && isset( $args[$items['group_channel_column']] ) ) {
		$column = $args[$items['group_channel_column']];
	}

?>
	<div class="g-row g-line sort group-channel<?php echo $bg; ?>" <?php aos(); ?>>
		<div class="g-col">
			<div class="group-channel-box">
				<div class="group-title" <?php aos_b(); ?>>
					<?php if ( ! empty( $items['group_channel_t'] ) ) { ?>
						<h3><?php echo $items['group_channel_t']; ?></h3>
					<?php } ?>
					<?php if ( ! empty( $items['group_channel_des'] ) ) { ?>
						<div class="group-des"><?php echo $items['group_channel_des']; ?></div>
					<?php } ?>
					<div class="clear"></div>
				</div>

				<div class="group-channel-main">
					<?php 
						$channelitems = ( array ) $items['group_channel_add'];
						foreach ( $channelitems as $channelitem ) {
							$args = [
								'left'  => ' btnleft',
								'right' => ' btnright',
							];
							$channelbtn = '';
							if ( isset( $channelitem['group_channel_float'] ) && isset( $args[$channelitem['group_channel_float']] ) ) {
								$channelbtn = $args[$channelitem['group_channel_float']];
							}
					?>
						<div class="group-channel-item channel-column-<?php echo $column; ?>">
							<div class="group-channel-content boxs6">
								<?php if ( ! empty( $channelitem['group_channel_img'] ) ) { ?>
									<div class="group-channel-bg" style="background-image: url(<?php echo $channelitem['group_channel_img']; ?>)"></div>
								<?php } ?>
								<div class="group-channel-area">
									<?php if ( ! empty( $channelitem['group_channel_title'] ) ) { ?>
										<div class="group-channel-title"><?php echo $channelitem['group_channel_title']; ?></div>
									<?php } ?>

									<?php if ( ! empty( $channelitem['group_channel_text'] ) ) { ?>
										<div class="group-channel-text"><?php echo $channelitem['group_channel_text']; ?></div>
									<?php } ?>
									<?php if ( ! empty( $channelitem['group_channel_btna_url'] ) ) { ?>
										<div class="group-channel-perch"></div>
										<div class="group-channel-btn<?php echo $channelbtn; ?>">
											<a class="group-channel-btna" href="<?php echo $channelitem['group_channel_btna_url']; ?>" rel="bookmark" <?php echo goal(); ?>><?php echo $channelitem['group_channel_btna']; ?></a>
											<?php if ( ! empty( $channelitem['group_channel_btnb_url'] ) ) { ?>
												<a class="group-channel-btnb" href="<?php echo $channelitem['group_channel_btnb_url']; ?>" rel="bookmark" <?php echo goal(); ?>><?php echo $channelitem['group_channel_btnb']; ?></a>
											<?php } ?>
										</div>
										<div class="group-channel-perch"></div>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php bu_help_n( $text = '投资渠道', $items['group_channel_s'] ); ?>
			<div class="clear"></div>
		</div>
	</div>
<?php } ?>