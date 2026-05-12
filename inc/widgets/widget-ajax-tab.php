<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ajax组合小工具
if ( ! class_exists( 'ajax_widget' ) ) {
	class ajax_widget extends WP_Widget {
		function __construct() {
			add_action( 'wp_ajax_ajax_widget_content', array( $this, 'ajax_ajax_widget_content' ) );
			add_action( 'wp_ajax_nopriv_ajax_widget_content', array( $this, 'ajax_ajax_widget_content' ) );
			$widget_ops = array(
				'classname'   => 'widget_ajax',
				'description' => '最新文章、热评文章、推荐文章、热门文章等'
			);
			parent::__construct( 'ajax_widget', 'Ajax组合小工具', $widget_ops );
		}

		function form( $instance ) {
			$instance = wp_parse_args( ( array ) $instance, array( 
				'tabs'             => array( 'recent' => 1, 'popular' => 1, 'hot' => 1, 'review' => 1, 'random' => 1, 'recommend' => 1 ), 
				'tab_order'        => array( 'recent' => 1, 'popular' => 2, 'hot' => 3, 'review' => 4, 'random' => 5, 'recommend' => 6 ),
				'allow_pagination' => 1,
				'post_num'         => '5',
				'show_thumb'       => '1',
				'viewe_days'       => '90',
				'review_days'      => '3',
				'like_days'        => '90',
				'pcat'             => '',
			) );

			extract( $instance ); ?>

			<div class="ajax_options_form">

		        <h4>选择 | 排序</h4>

				<div class="ajax_select_tabs">
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_recent">
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_recent" name="<?php echo $this->get_field_name("tabs"); ?>[recent]" value="1" <?php if (isset($tabs['recent'])) { checked( 1, $tabs['recent'], true ); } ?> />
						最新文章
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_recent" name="<?php echo $this->get_field_name('tab_order'); ?>[recent]" type="number" min="1" step="1" value="<?php echo $tab_order['recent']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px" for="<?php echo $this->get_field_id("tabs"); ?>_popular">
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_popular" name="<?php echo $this->get_field_name("tabs"); ?>[popular]" value="1" <?php if (isset($tabs['popular'])) { checked( 1, $tabs['popular'], true ); } ?> />
						大家喜欢
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_popular" name="<?php echo $this->get_field_name('tab_order'); ?>[popular]" type="number" min="1" step="1" value="<?php echo $tab_order['popular']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_hot">
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_hot" name="<?php echo $this->get_field_name("tabs"); ?>[hot]" value="1" <?php if (isset($tabs['hot'])) { checked( 1, $tabs['hot'], true ); } ?> />
						热门文章
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_hot" name="<?php echo $this->get_field_name('tab_order'); ?>[hot]" type="number" min="1" step="1" value="<?php echo $tab_order['hot']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_review">
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_review" name="<?php echo $this->get_field_name("tabs"); ?>[review]" value="1" <?php if (isset($tabs['review'])) { checked( 1, $tabs['review'], true ); } ?> />
						热评文章
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_review" name="<?php echo $this->get_field_name('tab_order'); ?>[review]" type="number" min="1" step="1" value="<?php echo $tab_order['review']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_random">
						<input type="checkbox" class="checkbox ajax_enable_random" id="<?php echo $this->get_field_id("tabs"); ?>_random" name="<?php echo $this->get_field_name("tabs"); ?>[random]" value="1" <?php if (isset($tabs['random'])) { checked( 1, $tabs['random'], true ); } ?> />
						随机文章
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_random" name="<?php echo $this->get_field_name('tab_order'); ?>[random]" type="number" min="1" step="1" value="<?php echo $tab_order['random']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
					<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_recommend">
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_recommend" name="<?php echo $this->get_field_name("tabs"); ?>[recommend]" value="1" <?php if (isset($tabs['recommend'])) { checked( 1, $tabs['recommend'], true ); } ?> />
						推荐阅读
						<input id="<?php echo $this->get_field_id('tab_order'); ?>_recommend" name="<?php echo $this->get_field_name('tab_order'); ?>[recommend]" type="number" min="1" step="1" value="<?php echo $tab_order['recommend']; ?>" style="width: 48px; margin-left: 10px;" />
					</label>
				</div>
				<div class="clear"></div>

				<h4 class="ajax_advanced_options_header">选项</h4>

				<div class="ajax_advanced_options">
			        <p>
						<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo $this->get_field_id("allow_pagination"); ?>">
							<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("allow_pagination"); ?>" name="<?php echo $this->get_field_name("allow_pagination"); ?>" value="1" <?php if (isset($allow_pagination)) { checked( 1, $allow_pagination, true ); } ?> />
							显示翻页
						</label>
					</p>

					<p>
						<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo $this->get_field_id("show_thumb"); ?>">
							<input type="checkbox" class="checkbox ajax_show_thumbnails" id="<?php echo $this->get_field_id("show_thumb"); ?>" name="<?php echo $this->get_field_name("show_thumb"); ?>" value="1" <?php if (isset($show_thumb)) { checked( 1, $show_thumb, true ); } ?> />
							显示缩略图
						</label>
					</p>

					<div class="ajax_post_options">

						<p>
							<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('post_num'); ?>">文章显示数量
								<input class="number-text-be" id="<?php echo $this->get_field_id('post_num'); ?>" name="<?php echo $this->get_field_name('post_num'); ?>" type="number" min="1" step="1" value="<?php echo $post_num; ?>" />
							</label>
						</p>

						<p>
							<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('like_days'); ?>">大家喜欢限定
								<input class="number-text-be" id="<?php echo $this->get_field_id('like_days'); ?>" name="<?php echo $this->get_field_name('like_days'); ?>" type="number" min="1" step="1" value="<?php echo $like_days; ?>" /> 天
							</label>
						</p>

						<p>
							<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('viewe_days'); ?>">热门文章限定
								<input class="number-text-be" id="<?php echo $this->get_field_id('viewe_days'); ?>" name="<?php echo $this->get_field_name('viewe_days'); ?>" type="number" min="1" step="1" value="<?php echo $viewe_days; ?>" /> 天
							</label>
						</p>
					
						<p>
							<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('review_days'); ?>">热评文章限定
								<input class="number-text-be" id="<?php echo $this->get_field_id('review_days'); ?>" name="<?php echo $this->get_field_name('review_days'); ?>" type="number" min="1" step="1" value="<?php echo $review_days; ?>" /> 月
							</label>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id('pcat'); ?>">
								最新文章排除的分类ID
								<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'pcat' ); ?>" name="<?php echo $this->get_field_name( 'pcat' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['pcat'] ), ENT_QUOTES)); ?></textarea>
							</label>
						</p>
					</div>
				</div>
			</div>
		<?php }

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['tabs']             = $new_instance['tabs'];
			$instance['tab_order']        = $new_instance['tab_order']; 
			$instance['allow_pagination'] = isset( $new_instance['allow_pagination'] ) ? $new_instance['allow_pagination'] : '';
			$instance['post_num']         = $new_instance['post_num'];
			$instance['viewe_days']       =  $new_instance['viewe_days'];
			$instance['review_days']      =  $new_instance['review_days'];
			$instance['like_days']        =  $new_instance['like_days'];
			$instance['show_thumb']       = isset( $new_instance['show_thumb'] ) ? $new_instance['show_thumb'] : '';
			$instance['pcat']             = $new_instance['pcat'];

			$cache_key = 'begin_widget_ajax_tab_' . $this->number;
			delete_transient( $cache_key );
			return $instance;
		}
		function widget( $args, $instance ) {
			extract( $args );
			extract( $instance );
			$title_w = title_i_w();
			wp_enqueue_script( 'ajax_widget' );
			wp_enqueue_style( 'ajax_widget' );
			if ( empty( $tabs ) ) $tabs = array( 'recent' => 1, 'popular' => 1 );
			$tabs_count = count( $tabs );
			if ( $tabs_count <= 1 ) {
				$tabs_count = 1;
			} elseif ( $tabs_count > 5 ) {
				$tabs_count = 6;
			}

			$available_tabs = array(
				'recent'    => __( '最新文章', 'begin' ),
				'popular'   => __( '大家喜欢', 'begin' ),
				'hot'       => __( '热门文章', 'begin' ),
				'review'    => __( '热评文章', 'begin' ),
				'random'    => __( '随机文章', 'begin' ),
				'recommend' => __( '推荐阅读', 'begin' )
			);

			array_multisort( $tab_order, $available_tabs );
			?>


			<?php echo $before_widget; ?>

			<div class="ajax_widget_content" id="<?php echo $widget_id; ?>_content" data-widget-number="<?php echo esc_attr( $this->number ); ?>">
				<div class="ajax-tabs <?php echo "has-$tabs_count-"; ?>tabs">
					<?php foreach ( $available_tabs as $tab => $label ) { ?>
						<?php if ( ! empty( $tabs[$tab] ) ): ?>
							<span class="tab_title tab-first"><a href="#" title="<?php echo $label; ?>" id="<?php echo $tab; ?>-tab"></a></span>
						<?php endif; ?>
					<?php } ?> 
					<div class="clear"></div>
				</div>
				<!--end .tabs-->
				<div class="clear"></div>

				<div class="new_cat">
					<?php if ( ! empty( $tabs['popular'] ) ) : ?>
						<div id="popular-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $tabs['recent'] ) ) : ?>
						<div id="recent-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $tabs['recommend'] ) ) : ?>
						<div id="recommend-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $tabs['hot'] ) ) : ?>
						<div id="hot-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $tabs['review'] ) ) : ?>
						<div id="review-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $tabs['random'] ) ) : ?>
						<div id="random-tab-content" class="tab-content">
							<div class="tab-load">
								<ul>
									<h4><?php _e( '加载中...', 'begin' ); ?></h4>
									<?php if ( $show_thumb == 1 ) { ?>
										<?php echo tab_load_img( $post_num ); ?>
									<?php } else { ?>
										<?php echo tab_load_text( $post_num ); ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<div class="clear"></div>
				</div> <!--end .inside -->

				<div class="clear"></div>
			</div><!--end #tabber -->
			<?php 
				unset( $instance['tabs'], $instance['tab_order'] );
			?>

			<script type="text/javascript">
				jQuery(function($) { 
					$('#<?php echo $widget_id; ?>_content').data('args', <?php echo json_encode($instance); ?>);
				});
			</script>

			<?php echo $after_widget; ?>
			<?php 
		}

		function ajax_ajax_widget_content() {
			$tab    = $_POST['tab'];
			$args   = isset( $_POST['args'] ) ? $_POST['args'] : [];
			$number = isset( $_POST['widget_number'] ) ? intval( $_POST['widget_number'] ) : '';
			$page   = intval( $_POST['page'] );
			if ( $page < 1 )
				$page = 1;

			$cache_key = 'begin_widget_ajax_tab_' . $number . '_' . $tab . '_' . $page;
			$cached_content = get_transient( $cache_key );

			if ( false !== $cached_content ) {
				echo $cached_content;
				die();
			}

			ob_start();

			if ( ! is_array( $args ) || empty( $args ) ) { // json_encode() failed
				$ajax_widgets = new ajax_widget();
				$settings = $ajax_widgets->get_settings();

				if ( isset( $settings[ $number ] ) ) {
					$args = $settings[ $number ];
				} else {
					die('<ul><li>无法加载文章！</li></ul>');
				}
			}

			// sanitize args
			$post_num = ( empty( $args['post_num'] ) ? 5 : intval( $args['post_num'] ) );
			if ($post_num > 20 || $post_num < 1 ) { // max 20 posts
				$post_num = 5;
			}

			$viewe_days       = ( empty( $args['viewe_days']) ? 90 : intval( $args['viewe_days'] ) );
			$review_days      = ( empty( $args['review_days']) ? 3 : intval( $args['review_days'] ) );
			$like_days        = ( empty( $args['like_days']) ? 90 : intval( $args['like_days'] ) );
			$show_thumb       = ! empty( $args['show_thumb'] );
			$pcat             = strip_tags( $args['pcat']);
			$allow_pagination = ! empty( $args['allow_pagination'] );
			switch ($tab) { 

				// Recent Posts
				case "recent":
					?>
					<ul>
						<h4><?php _e( '最新文章', 'begin' ); ?></h4>
						<?php 
							$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : [];
							$recent = new WP_Query( array(
								'posts_per_page'      => $post_num,
								'post__not_in'        => array( $exclude ),
								'orderby'             => 'post_date',
								'order'               => 'desc',
								'post_status'         => 'publish',
								'category__not_in'    => explode(',',$pcat ),
								'paged'               => $page,
								'ignore_sticky_posts' => 1,
								'no_found_rows'       => true,
							));
						?>

						<?php $last_page = $recent->max_num_pages; while ( $recent->have_posts() ) : $recent->the_post(); ?>
							<li>
								<?php if ( $show_thumb == 1 ) { ?>
									<span class="thumbnail">
										<?php echo zm_thumbnail(); ?>
									</span>

									<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
									<?php grid_meta(); ?>
									<?php views_span(); ?>
								<?php } else { ?>
									<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
								<?php } ?>
								<div class="clear"></div>
							</li>
					<?php endwhile; wp_reset_postdata(); ?>
					</ul>
	                <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>
					<?php 
				break;

				// Popular Posts
				case "popular":
					?>
					<ul> 
						<h4><?php _e( '大家喜欢', 'begin' ); ?></h4>
						<?php 
							$date_query=array(
								array(
									'column' => 'post_date',
									'before' => date('Y-m-d H:i',time()),
									'after'  => date('Y-m-d H:i',time()-3600*24*$viewe_days)
								)
							);

							$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : [];
							$recent = new WP_Query(array(
								'meta_key'       => 'zm_like',
								'orderby'        => 'meta_value_num',
								'post__not_in'   => array( $exclude ),
								'posts_per_page' => $post_num,
								'date_query'     => $like_days,
								'paged'          => $page,
								'order'          => 'DESC',
								'ignore_sticky_posts' => 1,
								'no_found_rows'       => true,
							));

							$last_page = $recent->max_num_pages; if ( $recent->have_posts() ) : while ($recent->have_posts()) : $recent->the_post();
						?>
						<li>
							<?php if ( $show_thumb == 1 ) { ?>
								<span class="thumbnail">
									<?php echo zm_thumbnail(); ?>
								</span>

								<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
								<?php grid_meta(); ?>
								<span class="be-like"><i class="be be-thumbs-up-o ri"></i><?php zm_get_current_count(); ?></span>
							<?php } else { ?>
								<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
							<?php } ?>
							<div class="clear"></div>
						</li>
						<?php endwhile;?>
						<?php else : ?>
							<li class="be-none-w"><?php _e( '暂无被点赞的文章', 'begin' ); ?></li>
						<?php endif; ?>
						<?php wp_reset_postdata(); ?>
					</ul>

		            <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>

					<?php 
				break;

				// hot
				case "hot":
					?> 
					<ul> 
						<h4><?php _e( '热门文章', 'begin' ); ?></h4>
						<?php if ( zm_get_option( 'post_views' ) ) { ?>
						<?php 
							$date_query = array(
								array(
									'column' => 'post_date',
									'before' => date( 'Y-m-d H:i',time() ),
									'after'  => date( 'Y-m-d H:i',time()-3600*24*$viewe_days )
								)
							);

							$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : [];
							$recent = new WP_Query(array(
								'meta_key'       => 'views',
								'orderby'        => 'meta_value_num',
								'post__not_in'   => array( $exclude ),
								'post_status'    => 'publish',
								'posts_per_page' => $post_num,
								'date_query'     => $date_query,
								'paged'          => $page,
								'order'          => 'DESC',
								'ignore_sticky_posts' => 1,
								'no_found_rows'       => true,
							));

						$last_page = $recent->max_num_pages; if ( $recent->have_posts() ) : while ( $recent->have_posts() ) : $recent->the_post();
						?>

						<li>
							<?php if ( $show_thumb == 1 ) { ?>
								<span class="thumbnail">
									<?php echo zm_thumbnail(); ?>
								</span>

							<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
							<?php grid_meta(); ?>
							<?php views_span(); ?>
							<?php } else { ?>
								<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
							<?php } ?>
							<div class="clear"></div>
						</li>

						<?php endwhile;?>
						<?php else : ?>
							<li class="be-none-w"><?php _e( '暂无', 'begin' ); ?></li>
						<?php endif; ?>
						<?php wp_reset_postdata(); ?>
						<?php } else { ?>
							<li>需要启用文章浏览统计</a></li>
						<?php } ?>
					</ul>

		            <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>

					<?php 
				break;

				// Latest recommend
				case "recommend":
					?>
					<ul>
						<h4><?php _e( '推荐阅读', 'begin' ); ?></h4>
						<?php 
							$recent = new WP_Query(array(
							'meta_key'            => 'hot',
							'posts_per_page'      => $post_num,
							'ignore_sticky_posts' => 1,
							'post_status'         => 'publish',
							'paged'               => $page,
							'order'               => 'DESC',
							'no_found_rows'       => true,
							));

							$last_page = $recent->max_num_pages; if ( $recent->have_posts() ) : while ($recent->have_posts()) : $recent->the_post();
						?>
							<li>
								<?php if ( $show_thumb == 1 ) { ?>
									<span class="thumbnail">
										<?php echo zm_thumbnail(); ?>
									</span>

								<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
								<?php grid_meta(); ?>
								<?php views_span(); ?>
								<?php } else { ?>
									<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
								<?php } ?>
								<div class="clear"></div>
							</li>
						<?php endwhile;?>
						<?php else : ?>
							<li class="be-none-w">编辑文章，勾选“本站推荐小工具中”</li>
						<?php endif; ?>
						<?php wp_reset_postdata(); ?>
					</ul>

	                <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>

					<?php 
				break;

				// Latest comments
				case "random":
					?> 
					<ul>
						<h4><?php _e( '随机文章', 'begin' ); ?></h4>
						<?php 
							$recent = new WP_Query(array(
								'posts_per_page'      => $post_num,
								'orderby'             => 'rand',
								'post_status'         => 'publish',
								'category__not_in'    => explode( ',',$pcat ),
								'paged'               => $page,
								'ignore_sticky_posts' => 1,
								'no_found_rows'       => true,
							));
						?>
						<?php $last_page = $recent->max_num_pages; while ( $recent->have_posts() ) : $recent->the_post(); ?>

						<?php if ( $show_thumb == 1 ) { ?>
							<li>
								<span class="thumbnail">
									<?php echo zm_thumbnail(); ?>
								</span>
								<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
								<?php grid_meta(); ?>
								<?php views_span(); ?>
							</li>
						<?php } else { ?>
							<li class="srm the-icon"><?php the_title( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?></li>
						<?php } ?>

						<?php endwhile;?>
						<?php wp_reset_postdata(); ?>
					</ul>

	                <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>
					<?php 
				break;

				// Latest review
				case "review":
					?>

					<?php
						$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : [];
						$review = new WP_Query( array(
							'post_type'           => array( 'post' ),
							'posts_per_page'      => $post_num,
							'ignore_sticky_posts' => true,
							'no_found_rows'       => true,
							'post_status'         => 'publish',
							'orderby'             => 'comment_count',
							'post__not_in'        => array( $exclude ),
							'order'               => 'DESC',
							'paged'               => $page,
							'date_query' => array(
								array(
									'after' => ''.$review_days. 'month ago',
								),
							),
						) );
					?>

					<ul>
						<h4><?php _e( '热评文章', 'begin' ); ?></h4>
						<?php $last_page = $review->max_num_pages; while ( $review->have_posts() ): $review->the_post(); ?>
						<li>
							<?php if ( $show_thumb == 1 ) { ?>
								<span class="thumbnail">
									<?php echo zm_thumbnail(); ?>
								</span>

								<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></span>
								<?php grid_meta(); ?>
								<span class="discuss"><?php comments_number( '', '<i class="be be-speechbubble ri"></i>1 ', '<i class="be be-speechbubble ri"></i>%' ); ?></span>
							<?php } else { ?>
								<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
							<?php } ?>
							<div class="clear"></div>
						</li>
						<?php endwhile;?>
						<?php wp_reset_postdata(); ?>
					</ul>

	                <div class="clear"></div>
					<?php if ( $allow_pagination ) : ?>
						<?php $this->tab_pagination( $page, $last_page ); ?>
					<?php endif; ?>

					<?php 
				break;
			}
			die();
		}
		function tab_pagination( $page, $last_page ) { ?>
			<div class="ajax-pagination">
				<div class="clear"></div>
				<?php if ( $page > 1 ) : ?>
					<a href="#" class="previous"><span><i class="be be-roundleft"></i></span></a>
				<?php endif; ?>
				<?php if ( $page != $last_page ) : ?>
					<a href="#" class="next"><span><i class="be be-roundright"></i></span></a>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<input type="hidden" class="page_num" name="page_num" value="<?php echo $page; ?>" />
		<?php }
	}
}
add_action( 'widgets_init', 'ajax_widget_init' );
function ajax_widget_init() {
	register_widget( 'ajax_widget' );
}