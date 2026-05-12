<?php
// 主打产品
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( co_get_option( 'group_goods' ) && isset( $items ) ) {
	$mode   = isset( $items['group_goods_m'] ) ?
		( $items['group_goods_m'] == 'group_goods_right' ? ' right' :
		( $items['group_goods_m'] == 'group_goods_left' ? ' left' : '' ) ) : '';
	$colour = isset( $items['group_goods_colour'] ) ?
		( $items['group_goods_colour'] == 'group_goods_white' ? ' white' :
		( $items['group_goods_colour'] == 'group_goods_black' ? ' black' : '' ) ) : '';

	$goods_img   = isset( $items['group_goods_img'] ) ? esc_url( $items['group_goods_img'] ) : '';
	$title_img   = isset( $items['group_goods_title_img'] ) ? esc_url( $items['group_goods_title_img'] ) : '';
	$goods_title = isset( $items['group_goods_title'] ) ? $items['group_goods_title'] : '';
	$goods_des   = isset( $items['group_goods_des'] ) ? $items['group_goods_des'] : '';
	$btn_url     = isset( $items['group_goods_btn_url'] ) ? $items['group_goods_btn_url'] : '';
	$goods_btn   = isset( $items['group_goods_btn'] ) ? $items['group_goods_btn'] : '';
	$goods_number   = isset( $items['group_goods_number'] ) ? $items['group_goods_number'] : '';
	$goods_id   = isset( $items['group_goods_id'] ) ? $items['group_goods_id'] : '';
	?>
	<div class="g-row g-line group-goods-line">
		<div class="g-col-full" style="background: url('<?php echo $goods_img; ?>') no-repeat center / cover;">
			<div class="group-goods-box<?php echo esc_attr( $mode ); ?><?php echo esc_attr( $colour ); ?>">
				<div class="group-goods-main">
					<div class="group-goods-blank"></div>
					<div class="group-goods-area">
						<h3 class="group-goods-title
						<?php
						if ( ! empty( $title_img ) ) {
							?>
							goods-title-ico<?php } ?>">
							<?php if ( ! empty( $title_img ) ) { ?>
								<span class="group-goods-title-ico">					
									<img class="group-goods-title-img" src="<?php echo $title_img; ?>" alt="<?php echo $goods_title; ?>">
								</span>
							<?php } ?>
							<a href="<?php echo $btn_url; ?>" <?php echo goal(); ?>><?php echo $goods_title; ?></a>
						</h3>

						<?php if ( ! empty( $goods_des ) ) { ?>
							<div class="group-goods-des">
								<?php echo wpautop( $goods_des ); ?>
							</div>
						<?php } ?>


						<?php if ( ! empty( $goods_id ) ) { ?>
							<?php
								$category_id = $goods_id;

								$args = array(
									'cat'            => $category_id,
									'posts_per_page' => $goods_number,
									'order'          => 'DESC',
									'orderby'        => 'date',
									'ignore_sticky_posts' => 1, 
									'no_found_rows'       => true,
								);


								$the_query = new WP_Query( $args );
								if ( $the_query->have_posts() ) : ?>
	
									<ul class="group-goods-text">
										<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
											<li class="group-goods-post">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>											
											</li>
										<?php endwhile; ?>
									</ul>
						
	
							<?php wp_reset_postdata(); endif; ?>

						<?php } else { ?>

							<ul class="group-goods-text">
								<?php
								if ( isset( $items['group_goods_text'] ) && is_array( $items['group_goods_text'] ) ) {
									foreach ( $items['group_goods_text'] as $goods_item ) {
										$url  = isset( $goods_item['group_goods_post_url'] ) ? esc_url( $goods_item['group_goods_post_url'] ) : '';
										$text = isset( $goods_item['group_goods_post'] ) ? esc_html( $goods_item['group_goods_post'] ) : '';
										?>
										<li class="group-goods-post">
											<a class="srm get-icon" href="<?php echo $url; ?>" <?php echo goal(); ?>><?php echo $text; ?></a>
										</li>
										<?php
									}
								}
								?>
							</ul>
						<?php } ?>

						<?php if ( ! empty( $goods_btn ) ) { ?>
							<div class="group-goods-btn">
								<a class="srm" href="<?php echo $btn_url; ?>" <?php echo goal(); ?>><?php echo $goods_btn; ?></a>
							</div>
						<?php } ?>

					</div>
				</div>
				<?php co_help( $text = '公司主页 → 主打产品', $number = 'group_goods_s', $go = '主打产品' ); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php } ?>
