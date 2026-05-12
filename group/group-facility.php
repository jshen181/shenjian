<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( co_get_option( 'group_facility' ) ) {
	$args = [
		'auto'  => '',
		'white' => ' group-white',
		'gray'  => ' group-gray',
	];

	$bg = '';
	if ( isset( $items['facility_bg'] ) && isset( $args[$items['facility_bg']] ) ) {
		$bg = $args[$items['facility_bg']];
	}
?>
<div class="g-row g-line group-facility<?php echo $bg; ?>" <?php aos(); ?>>
	<div class="g-col">
		<div class="group-facility-box">
			<?php
				$child_categories = get_terms( array(
					'taxonomy' => 'category',
					'parent'   => $items['group_facility_id'],
					'fields'   => 'ids'
				) );

				$args = [
					'true'  => '',
					'false' => $child_categories,
				];

				$child = '';
				if ( isset( $items['group_facility_chil'] ) && isset( $args[$items['group_facility_chil']] ) ) {
					$child = $args[$items['group_facility_chil']];
				}

				$args = array(
					'cat'              => $items['group_facility_id'],
					'posts_per_page'   => $items['group_facility_n'],
					'post_status'      => 'publish',
					'category__not_in' => $child,
					'ignore_sticky_posts' => 1, 
					'no_found_rows'       => true,
				);

				$query = new WP_Query( $args );
			?>

			<div class="group-facility-item facility-column<?php echo $items['group_facility_column']; ?><?php if ( ! empty( $items['group_facility_overlay'] ) ) { ?> facility-overlay<?php } ?>">
				<div class="boxs1">
					<div class="group-facility-area">
						<a class="group-facility-url" href="<?php echo esc_url( get_category_link( $items['group_facility_id'] ) ); ?>"<?php echo goal(); ?>></a>
						<figure class="facility-img">
							<div class="thumbs-b lazy"><a class="thumbs-back sc" rel="bookmark" target="_blank" href="http://localhost/kai/54.html" style="background-image: url(<?php echo $items['group_facility_img']; ?>);"></a></div>
						</figure>
						<div class="group-facility-cat">
							<h2 class="group-facility-cat-title"><?php echo $items['group_facility_name']; ?></h2>
							<div class="group-facility-des"><?php echo $items['group_facility_des']; ?></div>
							<div class="group-facility-btn"><?php echo $items['group_facility_btn']; ?></div>
						</div>
					</div>
				</div>
			</div>

			<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="group-facility-item facility-column<?php echo $items['group_facility_column']; ?>">
				<div class="boxs1">
					<div class="group-facility-area<?php if ( ! empty( $items['group_facility_size'] ) ) { ?> facility-title-big<?php } ?><?php if ( ! empty( $items['group_facility_center'] ) ) { ?> facility-title-center<?php } ?>">
						<figure class="facility-img">
							<?php echo zm_thumbnail(); ?>
						</figure>
						<?php $sub = get_post_meta( get_the_ID(), 'title_sub', true ) ? '<span>' . get_post_meta( get_the_ID(), 'title_sub', true ) . '</span>' : ''; ?>
						<?php the_title(sprintf('<h2 class="group-facility-title"><a href="%s" rel="bookmark" %s>', esc_url(get_permalink()), goal()), $sub . '</a></h2>'); ?>
					</div>
				</div>
			</div>

			<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
			    没有找到相关文章
			<?php endif; ?>
		</div>
		<?php co_help_n( $text = '公司主页 → 设备设施', $items['group_facility_s'], $go = '设备设施' ); ?>
	</div>
</div>
<?php } ?>