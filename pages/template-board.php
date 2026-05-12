<?php
/*
Template Name: 留言板
Template Post Type: page
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div id="primary" class="content-area no-sidebar">
	<main id="main" class="be-main site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template/content', 'page' );
			if ( comments_open() || get_comments_number() ) {
				comments_template( '', true );
			}
		endwhile;
		?>
	</main>
</div>
<?php get_footer(); ?>
