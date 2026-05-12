<?php
// 公司形象
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( co_get_option( 'group_identity' ) && isset( $items ) ) {
	$mode = isset( $items['group_identity_img_m'] ) ?
		( $items['group_identity_img_m'] == 'group_identity_img_right' ? ' right' :
		( $items['group_identity_img_m'] == 'group_identity_img_left' ? ' left' : '' ) ) : '';

	$column = isset( $items['group_identity_column'] ) ?
		( $items['group_identity_column'] == '2' ? ' column2' :
		( $items['group_identity_column'] == '3' ? ' column3' : '' ) ) : '';

	$left_url    = isset( $items['group_identity_url_l'] ) ? esc_url( $items['group_identity_url_l'] ) : '';
	$left_img    = isset( $items['group_identity_img_l'] ) ? esc_url( $items['group_identity_img_l'] ) : '';
	$left_text_a = isset( $items['group_identity_text_a_l'] ) ? esc_html( $items['group_identity_text_a_l'] ) : '';
	$left_text_b = isset( $items['group_identity_text_b_l'] ) ? esc_html( $items['group_identity_text_b_l'] ) : '';
	?>
	<div class="g-row g-line group-identity-line">
		<div class="g-col-full">
			<div class="group-identity-box<?php echo esc_attr( $mode ); ?>">
				<div class="group-identity-area group-identity-l">
					<div class="group-identity-item">
						<a class="group-facility-url" href="<?php echo $left_url; ?>" <?php echo goal(); ?>></a>
						<div class="group-identity-img">
							<img src="<?php echo $left_img; ?>" alt="<?php echo $left_text_b; ?>" loading="lazy">
						</div>
						<div class="group-identity-title">
							<div class="group-identity-text">
								<div class="group-identity-text-a"><?php echo $left_text_a; ?></div>
								<div class="group-identity-text-b"><?php echo $left_text_b; ?></div>
							</div>
						</div>
					</div>
				</div>

				<div class="group-identity-area group-identity-r<?php echo esc_attr( $column ); ?>">    
					<?php
					if ( isset( $items['group_identity_r'] ) && is_array( $items['group_identity_r'] ) ) {
						foreach ( $items['group_identity_r'] as $identity_item ) {
							$right_url    = isset( $identity_item['group_identity_url_r'] ) ? esc_url( $identity_item['group_identity_url_r'] ) : '';
							$right_img    = isset( $identity_item['group_identity_img_r'] ) ? esc_url( $identity_item['group_identity_img_r'] ) : '';
							$right_text_a = isset( $identity_item['group_identity_text_a_r'] ) ? esc_html( $identity_item['group_identity_text_a_r'] ) : '';
							$right_text_b = isset( $identity_item['group_identity_text_b_r'] ) ? esc_html( $identity_item['group_identity_text_b_r'] ) : '';
							?>
							<div class="group-identity-item">
								<a class="group-facility-url" href="<?php echo $right_url; ?>" <?php echo goal(); ?>></a>
								<div class="group-identity-img">
									<img src="<?php echo $right_img; ?>" alt="<?php echo $right_text_b; ?>" loading="lazy">
								</div>
								<div class="group-identity-title">
									<div class="group-identity-text">
										<div class="group-identity-text-a"><?php echo $right_text_a; ?></div>
										<div class="group-identity-text-b"><?php echo $right_text_b; ?></div>
									</div>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
				<?php co_help( $text = '公司主页 → 形象展示', $number = 'group_identity_s', $go = '公司形象' ); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php } ?>
