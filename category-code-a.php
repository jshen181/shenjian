<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * category Template: Ajax图片布局
 */
get_header(); ?>
<?php if ( ! zm_get_option( 'ajax_layout_code_a_r' ) ) { ?><div class="ajax-content-area content-area"><?php } else { ?><div id="<?php if ( ! zm_get_option( 'ajax_layout_code_a_l' ) ) { ?>primary<?php } else { ?>primary-l<?php } ?>" class="ajax-content-area content-area"><?php } ?>
	<main id="main" class="site-main ajax-site-main" role="main">
		<?php 
			$orderby = 'date';
			$meta_key = '';

			switch ( zm_get_option( 'ajax_code_a_orderby' ) ) {
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

			$btns     = zm_get_option( 'ajax_layout_code_a_btn' ) ? be_cat_btn() : 'no';
			$children = ( zm_get_option('ajax_layout_code_a_chil' ) == 'false' ) ? 'false' : 'true';

			echo do_shortcode( '[be_ajax_post posts_per_page="' . zm_get_option( 'ajax_layout_code_a_n' ) . '" column="' . zm_get_option( 'ajax_layout_code_a_f' ) . '" img="' . zm_get_option( 'ajax_layout_code_a_img' ) . '" cat="' . get_query_var( 'cat' ) . ',' . be_subcat_id() . '" btn="' . $btns . '" btn_all= "no" more="' . zm_get_option( 'nav_btn_a' ) . '" infinite="' . zm_get_option( 'more_infinite_a' ) . '" meta_key="' . $meta_key . '" orderby="' . $orderby . '" children="' . $children . '" order="DESC"]' );
		?>
	</main>
	<div class="clear"></div>
</div>

<?php if ( zm_get_option( 'ajax_layout_code_a_r' ) ) { ?>
	<div id="<?php if ( ! zm_get_option( 'ajax_layout_code_a_l' ) ) { ?>sidebar<?php } else { ?>sidebar-l<?php } ?>" class="widget-area all-sidebar">
		<?php if ( ! dynamic_sidebar( 'sidebar-a' ) ) : ?>
			<aside id="add-widgets" class="widget widget_text">
				<h3 class="widget-title"><i class="be be-warning"></i>添加小工具</h3>
				<div class="add-widget-tip">
					<a href="<?php echo admin_url(); ?>widgets.php" target="_blank">点此为“分类归档侧边栏”添加小工具</a>
				</div>
			</aside>
		<?php endif; ?>
		<?php be_help( $text = '分类归档侧边栏' ); ?>
	</div>

	<div class="clear"></div>
<?php } ?>
<?php get_footer(); ?>