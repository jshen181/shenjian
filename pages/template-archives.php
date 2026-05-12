<?php
/*
Template Name: 文章归档
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'be_get_archive_post_types' ) ) {
	function be_get_archive_post_types() {
		$types = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects'
		);
		// 排除的文章类型
		$exclude_types   = array( 'attachment', 'revision', 'nav_menu_item', 'surl' );
		$exclude_setting = zm_get_option( 'archive_exclude' );
		if ( $exclude_setting && is_array( $exclude_setting ) ) {
			$exclude_types = array_merge( $exclude_types, $exclude_setting );
		}
		$archive_types = array();
		foreach ( $types as $type => $obj ) {
			if ( ! in_array( $type, $exclude_types ) ) {
				$archive_types[] = $type;
			}
		}
		return $archive_types;
	}
}

get_header();

function be_archives_list() {
	$output = '<div id="all_archives">';
	global $wpdb;

	$post_types = be_get_archive_post_types();
	if ( empty( $post_types ) ) {
		$post_types = array( 'post' );
	}
	$type_placeholders = array_fill( 0, count( $post_types ), '%s' );
	$type_where        = 'post_type IN (' . implode( ',', $type_placeholders ) . ')';

	$years = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT YEAR(post_date) as year, MONTH(post_date) as month, COUNT(*) as count
			FROM $wpdb->posts
			WHERE post_status = 'publish' AND $type_where AND post_date <= NOW()
			GROUP BY YEAR(post_date), MONTH(post_date)
			ORDER BY year DESC, month DESC",
			$post_types
		)
	);

	if ( $years ) {
		$current_year  = 0;
		$current_month = 0;
		foreach ( $years as $year_month ) {
			$year  = $year_month->year;
			$month = $year_month->month;

			if ( $current_year != $year ) {
				if ( $current_month > 0 ) {
					$output .= '</ul>';
				}
				if ( $current_year > 0 ) {
					$output .= '</ul></div>';
				}
				$current_year  = $year;
				$current_month = 0;
				$output       .= '<div class="year-wrapper post-item post ms"><h3 class="beyear" data-year="' . $year . '">' . $year . '</h3><ul class="mon_list">';
			}

			if ( $current_month != $month ) {
				if ( $current_month > 0 ) {
					$output .= '</ul>';
				}
				$current_month = $month;
				$count         = $year_month->count;
				$output       .= '<li class="bemon-btn"><span class="bemon" data-year="' . $year . '" data-month="' . sprintf( '%02d', $month ) . '">' . sprintf( '%02d', $month ) . sprintf( __( '月', 'begin' ) ) . '<span class="mon-num">' . $count . sprintf( __( '篇', 'begin' ) ) . '</span></span><ul class="post_list" style="display:none;">';
			}
		}
		$output .= '</ul></ul></div>';
	}
	$output .= '</div>';

	echo $output;
}
?>
<div class="archives-area">
	<main id="main" class="be-main site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" class="post-item post ms" <?php aos_a(); ?>>
				<?php if ( get_post_meta( get_the_ID(), 'header_img', true ) || get_post_meta( get_the_ID(), 'header_bg', true ) ) { ?>
				<?php } else { ?>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->
				<?php } ?>

				<div class="archives-meta">
					<div class="running-stably">
						<?php _e( '网站已稳定运行', 'begin' ); ?>
						<?php
						$post_types = be_get_archive_post_types();
						if ( empty( $post_types ) ) {
							$post_types = array( 'post' );
						}
						$type_placeholders = array_fill( 0, count( $post_types ), '%s' );
						$type_where        = implode( ',', $type_placeholders );

						$first_post = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ($type_where) ORDER BY post_date ASC LIMIT 1",
								$post_types
							)
						);
						if ( $first_post ) {
							$start_date = strtotime( $first_post->post_date );
							$years      = floor( ( time() - $start_date ) / ( 365 * 24 * 60 * 60 ) );
							echo $years > 0 ? $years : 1;
						}
						?>
						<?php _e( '年', 'begin' ); ?>
					</div>
					<span>
						<?php echo $count_categories = wp_count_terms( 'category' ); ?>
						<?php _e( '分类', 'begin' ); ?>
					</span>/
					<span>
						<?php echo $count_tags = wp_count_terms( 'post_tag' ); ?>
						<?php _e( '标签', 'begin' ); ?>
					</span>/
					<span>
						<?php
						$total_posts = 0;
						foreach ( $post_types as $type ) {
							$count        = wp_count_posts( $type );
							$total_posts += $count->publish;
						}
						echo $total_posts;
						?>
						<?php _e( '文章', 'begin' ); ?>
					</span>/
					<span>
						<?php
						$my_email = get_bloginfo( 'admin_email' );
						echo $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments where comment_author_email!='$my_email'" );
						?>
						<?php _e( '留言', 'begin' ); ?>
					</span>/
					<span>
						<?php echo all_view(); ?>
						<?php _e( '浏览', 'begin' ); ?>
					</span>
					<div>
						<?php
						$last = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE post_type IN ($type_where) AND (post_status = 'publish' OR post_status = 'private')",
								$post_types
							)
						);
						$last = wp_date( 'Y-m-d', strtotime( $last[0]->MAX_m ) );
						echo $last;
						?>
						<?php _e( '更新', 'begin' ); ?>
					</div>
					<div class="clear"></div>
					<?php edit_post_link( '<i class="be be-editor"></i>', '<div class="page-edit-link edit-link">', '</div>' ); ?>
				</div>
			</article>
		<?php endwhile; ?>
		<?php be_archives_list(); ?>
	</main>
</div>
<?php get_footer(); ?>
