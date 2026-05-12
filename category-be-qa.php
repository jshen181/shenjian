<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * category Template: 问答列表
 */
get_header(); ?>

<section id="category-be-qa" class="content-area category-be-qa">
	<main id="main" class="be-main site-main domargin<?php indent(); ?>" role="main">
		<?php get_template_part( 'template/cat-top' ); ?>
		<?php be_exclude_child_cats(); ?>

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" class="post-item-list post scl qa-item" <?php aos_a(); ?>>
					<h2 class="entry-title qa-title">
						<span class="qa-toggle"></span>				
						<?php echo t_mark(); ?><?php the_title(); ?>
					</h2>
					<div class="qa-content">
						<div class="single-content">
							<?php the_content(); ?>
							<?php if ( current_user_can( 'edit_post', get_the_ID() ) ) : ?>
								<a class="be-qa-editor" href="<?php echo esc_url( get_permalink() ); ?>" target="_blank"><i class="be be-editor"></i></a>
								<div class="clear"></div>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endwhile; ?>

		<?php else : ?>
			<?php get_template_part( 'template/content', 'none' ); ?>

		<?php endif; ?>

	</main>

	<div class="pagenav-clear"><?php begin_pagenav(); ?></div>

</section>

<?php get_footer(); ?>
