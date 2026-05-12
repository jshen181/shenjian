<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * category Template: 图片按钮布局
 */
get_header(); ?>
<div class="ajax-content-area content-area">
	<main id="main" class="site-main ajax-site-main" role="main">
		<?php 
			$orderby = 'date';
			$meta_key = '';

			switch ( zm_get_option( 'ajax_code_h_orderby' ) ) {
				case 'modified':
					$orderby = 'modified';
					break;
				case 'comment_count':
					$orderby = 'comment_count';
					break;
				case 'views':
					$orderby = 'meta_value_num';
					$meta_key = 'views';
					break;
			}

			$btns     = zm_get_option( 'ajax_layout_code_h_btn' ) ? be_cat_btn() : 'no';
			$children = ( zm_get_option('ajax_layout_code_h_chil' ) == 'false' ) ? 'false' : 'true';

			echo do_shortcode( '[be_ajax_post posts_per_page="' . zm_get_option( 'ajax_layout_code_h_n' ) . '" column="' . zm_get_option( 'ajax_layout_code_h_f' ) . '" img="' . zm_get_option( 'ajax_layout_code_h_img' ) . '" cat="' . get_query_var( 'cat' ) . ',' . be_subcat_id() . '" btn="' . $btns . '" btn_all= "no" more="' . zm_get_option( 'nav_btn_h' ) . '" infinite="' . zm_get_option( 'more_infinite_h' ) . '" meta_key="' . $meta_key . '" orderby="' . $orderby . '" children="' . $children . '" order="DESC" style="btnlink"]' );
		?>
	</main>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>