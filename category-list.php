<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * category Template: 标题列表
 */
get_header(); ?>

<section id="category-list" class="content-area category-list">
	<main id="main" class="be-main site-main domargin" role="main">
		<?php get_template_part( 'template/cat-top' ); ?>
		<?php be_exclude_child_cats(); ?>

		<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" class="post-item-list post doclose scl" <?php aos_a(); ?>>
				<h2 class="entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" <?php echo goal(); ?>><?php echo t_mark(); ?>
						<?php the_title(); ?>
						<span class="archive-list-inf">
							<time datetime="<?php echo get_the_date('Y-m-d'); ?> <?php echo get_the_time('H:i:s'); ?>">
								<?php if ( zm_get_option( 'cat_layout_date_time' ) ) { ?>
									<?php the_time( 'm月d日' ); ?> <span class="list-time"><?php echo get_the_time('H:i'); ?></span>
								<?php } else { ?>
									<?php echo get_the_date(); ?>
								<?php } ?>
							</time>
							<?php views_span(); ?>
						</span>
					</a>
				</h2>
			</article>
		<?php endwhile; ?>

		<?php else : ?>
			<?php get_template_part( 'template/content', 'none' ); ?>

		<?php endif; ?>

	</main>

	<div class="pagenav-clear"><?php begin_pagenav(); ?></div>

</section>

<?php get_footer(); ?>