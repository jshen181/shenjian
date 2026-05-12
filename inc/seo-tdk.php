<?php if ( ! defined( 'ABSPATH' ) ) exit;
function be_title_head() {
	global $post, $term;
?>
<?php $term_id = ''; ?>
<?php $bepagin = ' - ' . sprintf( __( '第', 'begin' ) ) . ' '; ?>
<?php if ( get_bloginfo( 'language' ) === 'en-US' ) { ?><?php $pagin = ''; ?><?php } else { ?><?php $pagin = ' ' . sprintf( __( '页', 'begin' ) ); ?><?php } ?>
<?php if ( zm_get_option( 'wp_title' ) ) { ?>
<?php if ( zm_get_option( 'home_title' ) == '' ) { ?>
<?php if ( is_home() || is_front_page() ) { ?><title><?php bloginfo( 'name' ); ?><?php $description = get_bloginfo( 'description', 'display' ); if ( $description ) : ?><?php if ( zm_get_option( 'blog_info' ) ) { ?><?php connector(); ?><?php bloginfo( 'description' ); ?><?php } ?><?php endif; ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php } else { ?>
<?php if ( is_home() || is_front_page() ) { ?><title><?php echo zm_get_option( 'home_title' ); ?><?php if ( zm_get_option( 'home_info' ) ) { ?><?php connector(); ?><?php echo zm_get_option( 'home_info' ); ?><?php } ?><?php if ( get_query_var('paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php } ?>
<?php if ( is_search() ) { ?><title><?php echo wp_get_document_title(); ?></title><?php } ?>
<?php if ( is_single() ) { ?><title><?php if ( get_post_meta( get_the_ID(), 'custom_title', true ) ) { ?><?php echo trim( get_post_meta( get_the_ID(), 'custom_title', true ) ); ?><?php if ( get_query_var('page')) { echo $bepagin; echo get_query_var( 'page' ); echo $pagin;}?><?php } else { ?><?php if ( zm_get_option( 'seo_title_tag' ) ) { ?><?php be_seo_tags(); ?><?php echo zm_get_option( 'seo_separator_tag' ); ?><?php } ?><?php echo trim( wp_title( '',0 ) ); ?><?php if ( get_query_var( 'page' ) ) { echo $bepagin; echo get_query_var('page'); echo $pagin;}?><?php title_name(); ?><?php } ?></title><?php } ?>
<?php if ( is_page() && ! is_front_page() ) { ?><title><?php if ( get_post_meta(get_the_ID(), 'custom_title', true) ) { ?><?php echo trim( get_post_meta( get_the_ID(), 'custom_title', true ) ); ?><?php } else { ?><?php echo trim( wp_title( '',0 ) ); ?><?php } ?><?php if ( get_post_meta( get_the_ID(), 'custom_title', true ) ) { ?><?php } else { ?><?php title_name(); ?><?php } ?></title><?php } ?>
<?php if ( is_category() ) { ?><?php $term_id = get_query_var( 'cat' ); ?><?php } ?>
<?php if ( get_option( 'cat-title-' . $term_id )) : ?>
<?php if ( is_category() ) { ?><title><?php echo get_option( 'cat-title-' . $term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php else: ?>
<?php if ( is_category() ) { ?><title><?php single_cat_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php title_name(); ?></title><?php } ?>
<?php endif; ?>
<?php if ( is_year() ) { ?><title><?php the_time( 'Y' ); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_month() ) { ?><title><?php the_time( 'Y' ); ?><?php the_time('F'); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_day() ) { ?><title><?php the_time( 'Y-n-j' ); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_author() ) { ?><title><?php if ( get_bloginfo( 'language' ) === 'en-US' ) { ?><?php if ( get_the_author_meta( 'last_name' ) ) { ?><?php echo the_author_meta( 'last_name' ); ?><?php } else { ?><?php echo the_author_meta( 'display_name' ); ?><?php } ?><?php } else { ?><?php echo the_author_meta( 'display_name' ); ?><?php } ?><?php _e( '的主页', 'begin' ); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_404() ) { ?><title><?php echo stripslashes( zm_get_option( '404_t' ) ); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_tag() ) { ?><?php $term_id = get_query_var( 'tag_id' ); ?><?php } ?>
<?php if ( get_option( 'tag-title-' . $term_id ) ) : ?>
<?php if ( is_tag() ) { ?><title><?php echo get_option( 'tag-title-' . $term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php title_name(); ?></title><?php } ?>
<?php else: ?>
<?php if ( function_exists( 'is_tag' ) ) { if ( is_tag() ) { ?><title><?php  single_tag_title( "", true ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php title_name(); ?></title><?php } ?><?php } ?>
<?php endif; ?>
<?php if ( function_exists( 'is_shop' ) ) { ?><?php if ( is_shop('') ) { ?><title><?php echo trim( wp_title( '',0 ) ); ?><?php title_name(); ?></title><?php } ?><?php } ?>
<?php if ( is_tax() ) { ?><title><?php be_set_title(); ?><?php title_name(); ?></title><?php } ?>
<?php if ( is_single() || is_page() ) {
	if ( function_exists('get_query_var') ) {
		$cpage = intval( get_query_var( 'cpage' ) );
		$commentPage = intval( get_query_var( 'comment-page' ) );
	}
	if ( ! empty( $cpage ) || !empty( $commentPage ) ) {
		echo '<meta name="robots" content="noindex, nofollow" />';
		echo "\n";
	}
}
?>
<?php
if ( ! function_exists( 'utf8Substr' ) ) {
	function utf8Substr( $str, $from, $len ) {
		return preg_replace( '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str );
	}
}
if ( is_single() ){
	if ( $post->post_excerpt ) {
		if ( preg_match( '/<p>(.*)<\/p>/iU',trim( strip_tags( $post->post_excerpt,"<p>" ) ),$result ) ) {
			$post_content = $result['1'];
		} else {
			$post_content_r = explode( "\n",trim( strip_tags( strip_shortcodes( $post->post_excerpt ) ) ) );
			$post_content = $post_content_r['0'];
		}
		$description = utf8Substr( $post_content, 0, 220 );
	} else {
		if ( preg_match( '/<p>(.*)<\/p>/iU',trim( strip_tags( $post->post_content,"<p>" ) ),$result ) ) {
			$post_content = $result['1'];
		} else {
			$post_content_r = explode( "\n",trim( strip_tags( strip_shortcodes( $post->post_content ) ) ) );
			$post_content = $post_content_r['0'];
		}
		$description = utf8Substr( $post_content, 0, 220 );
	}
	$keywords = "";
	$tags = wp_get_post_tags( $post->ID );
	foreach ( $tags as $tag ) {
		$keywords = $keywords . $tag->name . ",";
	}
}
?>
<?php echo "\n"; ?>
<?php if ( is_single() ) { ?>
<?php if ( get_post_type() == 'sites' ) { ?>
<?php if ( get_post_meta( get_the_ID(), 'sites_des', true ) ) : ?>
<meta name="description" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'sites_des', true ) ); ?>" />
<?php else: ?>
<meta name="description" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'sites_description', true ) ); ?>" />
<?php endif; ?>
<?php if ( get_post_meta( get_the_ID(), 'keywords', true ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'keywords', true ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( trim( wp_title( '', 0 ) ) ); ?>" />
<?php endif; ?>
<?php } else { ?>
<?php if ( get_post_meta( get_the_ID(), 'description', true ) ) : ?>
<meta name="description" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'description', true ) ); ?>" />
<?php else: ?>
<?php if ( function_exists( 'is_bbpress' ) ) { ?>
<?php if ( ! bbp_is_single_topic() && ! bbp_is_single_forum() ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( $description ) ); ?>" />
<?php } ?>
<?php if ( bbp_is_single_forum() ) { ?>
<meta name="description" content="<?php echo esc_attr( wp_trim_words( get_the_content(), 160 ) ); ?>" />
<?php } ?>
<?php if ( bbp_is_single_topic() ) { ?>
<meta name="description" content="<?php echo esc_attr( wp_trim_words( get_the_content(), 160 ) ); ?>" />
<?php } ?>
<?php } else { ?>
<meta name="description" content="<?php echo esc_attr( trim( $description ) ); ?>" />
<?php } ?>
<?php endif; ?>
<?php if ( get_post_meta( get_the_ID(), 'keywords', true ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'keywords', true ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( trim( $keywords, ',' ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php } ?>
<?php if ( is_page() ) { ?>
<?php if ( is_front_page() ) { ?>
<meta name="description" content="<?php echo esc_attr( zm_get_option( 'description' ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( zm_get_option( 'keyword' ) ); ?>" />
<?php } else { ?>
<meta name="description" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'description', true ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_post_meta( get_the_ID(), 'keywords', true ) ); ?>" />
<?php } ?>
<?php } ?>
<?php if ( is_category() ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( wp_trim_words( tag_description(), 160 ) ) ) ); ?>" />
<?php if ( get_option( 'cat-words-' . $term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( single_cat_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_search() ) { ?>
<meta name="description" content="<?php echo esc_attr( __( '在', 'begin' ) . get_bloginfo( 'name' ) . __( '网站搜索', 'begin' ) . '\'' . get_search_query() . '\'' . __( '结果', 'begin' ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_search_query() ); ?>" />
<?php } ?>
<?php if ( is_author() ) { ?>
<meta name="description" content="<?php if ( get_the_author_meta( 'description' ) ) { ?><?php echo esc_attr( get_the_author_meta( 'description' ) ); ?><?php } else { ?><?php echo esc_attr( __( '暂无个人说明', 'begin' ) ); ?><?php } ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_the_author() ); ?>" />
<?php } ?>
<?php if ( is_year() ) { ?>
<meta name="description" content="<?php echo esc_attr( the_time( 'Y' ) . title_name( '', false ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
<?php } ?>
<?php if ( is_month() ) { ?>
<meta name="description" content="<?php echo esc_attr( the_time( 'Y' ) . '-' . the_time( 'F' ) . title_name( '', false ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
<?php } ?>
<?php if ( is_day() ) { ?>
<meta name="description" content="<?php echo esc_attr( the_time( 'Y-n-j' ) . title_name( '', false ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
<?php } ?>
<?php if ( is_tag() ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( wp_trim_words( tag_description(), 160 ) ) ) ); ?>" />
<?php if ( get_option( 'tag-words-' . $term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'tag-words-' . $term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( single_tag_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_home() ) { ?>
<meta name="description" content="<?php echo esc_attr( zm_get_option( 'description' ) ); ?>" />
<meta name="keywords" content="<?php echo esc_attr( zm_get_option( 'keyword' ) ); ?>" />
<?php } ?>
<?php if ( is_tax('taobao') ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'taobao' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax('gallery') ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'gallery' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax('videos') ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'videos' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax( 'products' ) ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'products' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax( 'special' ) ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'special' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax( 'favorites' ) ) { ?>
<meta name="description" content="<?php echo esc_attr( trim( strip_tags( tag_description() ) ) ); ?>" />
<?php $term_my = get_term_by( 'slug', $term, 'favorites' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo esc_attr( get_option( 'cat-words-' . $term_my->term_id ) ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo esc_attr( be_set_title( '', false ) ); ?>" />
<?php endif; ?>
<?php } ?>
<?php } ?>
<?php if ( zm_get_option( 'og_title' ) ) { ?>
<?php if ( is_single() ) { ?>
<meta property="og:type" content="article">
<meta property="og:locale" content="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>" />
<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>" />
<meta property="og:author" content="<?php echo esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ); ?>" />
<meta property="og:image" content="<?php echo esc_url( og_post_img() ); ?>" />
<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
<?php $description = get_post_meta( get_the_ID(), 'description', true ); ?>
<?php if ( get_post_meta( get_the_ID(), 'description', true ) ) : ?>
<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
<?php else: ?>
<meta property="og:description" content="<?php echo esc_attr( zm_og_excerpt() ); ?>" />
<?php endif; ?>
<meta property="og:url" content="<?php echo esc_url( get_the_permalink() ); ?>" />
<meta property="og:release_date" content="<?php echo esc_attr( get_the_date( 'Y-m-d' ) . ' ' . get_the_time( 'H:i:s' ) ); ?>" />
<?php } ?>
<?php } ?>
<?php } ?>
<?php 
if ( zm_get_option( 'baidu_time' ) ) {
function baidu_structured_data() {
if ( is_single() || is_page() ) {
echo '<script type="application/ld+json">
{
	"@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
	"@id": "' . esc_url( get_the_permalink() ) . '",
	"appid": "' . esc_js( zm_get_option('daily_token') ) . '",
	"title": "' . esc_js( get_the_title() ) . '",
	"images": ["' . esc_url( og_post_img() ) . '"],
	"description": "' . esc_js( zm_og_excerpt() ) . '",
	"pubDate": "' . esc_js( get_the_time('Y-m-d\TH:i:s') ) . '",
	"upDate": "' . esc_js( get_the_modified_time('Y-m-d\TH:i:s') ) . '"
}
</script>';
}
}
add_action( 'head_other', 'baidu_structured_data' );
}
?>