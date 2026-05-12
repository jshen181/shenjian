<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// 嵌入文章
function quote_post( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'ids' => '',
			),
			$atts
		)
	);
	$html  = '';
	$quote = new WP_Query(
		array(
			'post_type'    => 'any',
			'post__in'     => explode( ',', $ids ),
			'post__not_in' => get_option( 'sticky_posts' ),
		)
	);
	while ( $quote->have_posts() ) :
		$quote->the_post();
		global $wpdb, $post;
		$content = $post->post_content;
		preg_match_all( '/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER );
		$n     = count( $strResult[1] );
		$html .= '<div class="quote-post sup">';
		if ( get_post_meta( get_the_ID(), 'thumbnail', true ) ) {
			$html .= '<figure class="thumbnail">';
			$html .= zm_thumbnail();
			$html .= '<div class="quote-cat cat ease">';
			foreach ( ( get_the_category() ) as $category ) {
				$html .= '<a target="_blank" href="' . get_category_link( $category->cat_ID ) . '">' . $category->cat_name . '</a>';
			}
			$html .= '</div>';
			$html .= '</figure>';
		} elseif ( $n > 0 ) {
				$html .= '<figure class="thumbnail">';
				$html .= zm_thumbnail();
				$html .= '<div class="quote-cat cat ease">';

				$post_id           = $ids;
				$post_type         = get_post_type( $post_id );
				$custom_post_types = get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					)
				);
			if ( in_array( $post_type, $custom_post_types ) ) {
				$taxonomies = get_object_taxonomies( $post_type );

				foreach ( $taxonomies as $taxonomy ) {
					if ( is_taxonomy_hierarchical( $taxonomy ) ) {
						$terms = get_the_terms( $post_id, $taxonomy );

						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
							$first_term = $terms[0];
							$term_link  = get_term_link( $first_term, $taxonomy );

							if ( ! is_wp_error( $term_link ) ) {
								$html .= '<a href="' . $term_link . '" target="_blank">' . $first_term->name . '</a>';
							}
						}
					}
				}
			} else {
					$terms = get_the_terms( $post_id, 'category' );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					$first_term = $terms[0];
					$term_link  = get_term_link( $first_term );

					if ( ! is_wp_error( $term_link ) ) {
						$html .= '<a href="' . $term_link . '" target="_blank">' . $first_term->name . '</a>';
					}
				}
			}

				$html .= '</div>';
				$html .= '</figure>';
		}
		$html   .= '<div class="quote-title over"><a target="_blank" href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
		$html   .= '<div class="quote-post-content">';
		$content = strip_shortcodes( $content );
		$html   .= wp_trim_words( $content, 62, '...' );
		$html   .= '</div>';
		if ( ! $n > 0 && ! get_post_meta( get_the_ID(), 'thumbnail', true ) ) {
			$post_id           = $ids;
			$post_type         = get_post_type( $post_id );
			$custom_post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				)
			);
			if ( in_array( $post_type, $custom_post_types ) ) {
				$taxonomies = get_object_taxonomies( $post_type );

				foreach ( $taxonomies as $taxonomy ) {

					if ( is_taxonomy_hierarchical( $taxonomy ) ) {

						$terms = get_the_terms( $post_id, $taxonomy );

						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
							$first_term = $terms[0];
							$term_link  = get_term_link( $first_term, $taxonomy );

							if ( ! is_wp_error( $term_link ) ) {
								$html .= '<a href="' . $term_link . '" target="_blank">' . $first_term->name . '</a>';
							}
						}
					}
				}
			} else {
				$terms = get_the_terms( $post_id, 'category' );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					$first_term = $terms[0];
					$term_link  = get_term_link( $first_term );

					if ( ! is_wp_error( $term_link ) ) {
						$html .= '<a  class="quote-inf-cat" target="_blank" href="' . $term_link . '"><i class="be be-sort"></i> ' . $first_term->name . '</a>';
					}
				}
			}
		}
		$html .= '<div class="quote-inf fd">';
		$html .= '<span class="quote-views"><i class="be be-eye ri"></i>' . get_post_meta( get_the_ID(), 'views', true ) . '</span>';
		if ( get_comments_number() > 0 ) {
			$html .= '<span class="quote-comments"><i class="be be-speechbubble ri"></i>' . get_comments_number() . '</span>';
		}
		$html .= '</div>';
		$html .= '<div class="quote-more fd"><a href="' . get_permalink() . '" target="_blank" rel="external nofollow"><i class="be be-sort"></i></a></div>';
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
	endwhile;
	wp_reset_postdata();
	return $html;
}