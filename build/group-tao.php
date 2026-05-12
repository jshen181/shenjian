<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$build_id = get_the_ID();

if ( ! be_build( $build_id, 'g_tao_h' ) ) {
	return;
}
$tao_bg = be_build( $build_id, 'tao_bg' );

switch ( $tao_bg ) {
	case 'white':
		$bg = ' group-white';
		break;
	case 'gray':
		$bg = ' group-gray';
		break;
	case 'auto':
	default:
		$bg = '';
		break;
}

// 获取配置的分类ID
$term_ids_str = be_build( $build_id, 'g_tao_h_id' );
if ( empty( $term_ids_str ) ) {
	return;
}

$term_ids = array_filter( array_map( 'intval', explode( ',', $term_ids_str ) ) );
if ( empty( $term_ids ) ) {
	return;
}

$taxonomies = get_object_taxonomies( 'any' );
$tax_terms  = get_terms(
	array(
		'taxonomy'   => $taxonomies,
		'include'    => $term_ids,
		'orderby'    => 'include',
		'order'      => 'ASC',
		'hide_empty' => false,
	)
);

if ( $tax_terms ) {
	foreach ( $tax_terms as $tax_term ) {
		$args = array(
			'post_type'           => 'any',
			'tax_query'           => array(
				array(
					'taxonomy' => $tax_term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $tax_term->term_id,
				),
			),
			'post_status'         => 'publish',
			'posts_per_page'      => be_build( $build_id, 'g_tao_h_n' ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
		);

		$be_query = new WP_Query( $args );

		if ( $be_query->have_posts() ) {
			?>
			<div class="line-tao g-row g-line<?php echo $bg; ?>" <?php aos(); ?>>
				<div class="g-col">
					<div class="cms-picture-box">
						<div class="group-title" <?php aos_b(); ?>>
							<h3><a href="<?php echo get_term_link( $tax_term ); ?>" rel="bookmark" <?php echo goal(); ?>><?php echo $tax_term->name; ?></a></h3>
							<div class="group-des"><?php echo $tax_term->description; ?></div>
							<div class="clear"></div>
						</div>
						
						<?php
						while ( $be_query->have_posts() ) :
							$be_query->the_post();
							?>
							<div class="tao-home-area tao-home-fl tao-home-fl-<?php echo be_build( $build_id, 'g_tao_home_f' ); ?>">
								<?php get_template_part( '/template/tao-home' ); ?>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
						
						<div class="clear"></div>
					</div>
					
					<div class="group-post-more">
						<a href="<?php echo get_term_link( $tax_term ); ?>" title="<?php _e( '更多', 'begin' ); ?>" rel="bookmark" <?php echo goal(); ?>><i class="be be-more"></i></a>
					</div>
					
					<?php bu_help( $text = '商品模块', $number = 'g_tao_s' ); ?>
				</div>
			</div>
			<?php
		}
	}
}
?>
