<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( be_build( get_the_ID(), 'group_cat_b' ) ) {
	$args = [
		'auto'  => '',
		'white' => ' group-white',
		'gray'  => ' group-gray',
	];
	$bg = '';
	if ( isset( $items['cat_b_bg'] ) && isset( $args[$items['cat_b_bg']] ) ) {
		$bg = $args[$items['cat_b_bg']];
	}
?>
<div class="g-row g-line group-cat-b<?php echo $bg; ?>" <?php aos(); ?>>
	<div class="g-col">
		<div class="group-cat">
			<?php $categories =  explode( ',', $items['group_cat_b_id'] );
				foreach ( $categories as $category ) {
				$cat = be_build( get_the_ID(), 'group_no_cat_child' ) ? 'category' : 'category__in';
				$becat = $category;
				if ( function_exists( 'icl_object_id' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
					$becat = icl_object_id( $category, 'category', true );
				}
			?>

				<div class="gr2">
					<div class="gr-cat-box">
						<h3 class="gr-cat-title" <?php aos_f(); ?>><a href="<?php echo get_category_link( $category ); ?>" rel="bookmark" <?php echo goal(); ?>><?php echo get_cat_name( $becat ); ?><span class="gr-cat-more"><i class="be be-more"></i></span></a></h3>
						<div class="clear"></div>
						<div class="gr-cat-area">
							<?php if ( ! empty( $items['group_cat_b_first'] ) ) { ?>
								<?php
									if ( ! empty( $items['group_cat_b_top'] ) ) {
										$first = 'meta_key';
										$value = 'cat_top';
									} else {
										$first = '';
										$value = '';
									}

									$imgt = get_posts( array(
										'post_status'    => 'publish',
										'posts_per_page' => '1',
										$first           => $value,
										$cat             => $category,
									) );
								?>
								<?php foreach ( $imgt as $post ) : setup_postdata( $post ); $grouptop[] = $post->ID; $has_top_post = true; ?>
									<figure class="gr-thumbnail" <?php aos_b(); ?>><?php echo zm_long_thumbnail(); ?></figure>
									<div class="be-aos" <?php aos_f(); ?>><?php the_title( sprintf( '<h2 class="gr-title"><a class="srm" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a></h2>' ); ?></div>
								<?php endforeach; ?>
								<?php wp_reset_postdata(); ?>

								<div class="clear"></div>
							<?php } ?>
							<ul class="gr-cat-list" <?php aos_f(); ?>>
								<?php
									$imgb = get_posts( array(
										'posts_per_page' => $items['group_cat_b_n'],
										'post_status'    => 'publish',
										'post__not_in'   => $has_top_post ? $grouptop : array(),
										$cat             => $category,
									) );
								?>
								<?php foreach ( $imgb as $post ) : setup_postdata( $post ); ?>
									<li class="list-date"><time datetime="<?php echo get_the_date( 'Y-m-d' ); ?> <?php echo get_the_time( 'H:i:s' ); ?>"><?php the_time( 'm/d' ) ?></time></li>
									<?php the_title( sprintf( '<li class="list-title"><h2 class="group-list-title"><a class="srm" href="%s" rel="bookmark" ' . goal() . '>' . t_mark(), esc_url( get_permalink() ) ), '</a></h2></li>' ); ?>
								<?php endforeach; ?>
								<?php wp_reset_postdata(); ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="clear"></div>
		</div>
		<?php bu_help_n( $text = '新闻资讯B', $items['group_cat_b_s'] ); ?>
	</div>
</div>
<?php } ?>