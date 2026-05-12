<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 书籍封面
class be_widget_novel extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'be_widget_novel',
			'description'                 => '调用书籍封面',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_widget_novel', '书籍封面', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_mark'    => 1,
			'cat_id'       => '',
			'enable_cache' => 0,
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		echo $before_widget;

		$enable_cache = ! empty( $instance['enable_cache'] );

		// 缓存：根据开关决定是否使用缓存
		if ( $enable_cache ) {
			$cache_key   = 'begin_widget_novel_' . md5( $instance['cat_id'] );
			$cached_html = get_transient( $cache_key );

			if ( false !== $cached_html ) {
				echo $cached_html;
				echo $after_widget;
				return;
			}

			ob_start();
		}
		?>


		<div class="widget-novel-cover">
			<?php
			$terms = get_terms(
				array(
					'taxonomy'   => 'category',
					'include'    => $instance['cat_id'],
					'hide_empty' => false,
					'orderby'    => 'include',
					'order'      => 'DESC',
				)
			);
			foreach ( $terms as $term ) {
				?>
				<div class="cms-novel-main">
					<div class="boxs1">
						<div class="cms-novel-box" <?php aos_a(); ?>>
							<div class="cms-novel-cove-img-box">
								<?php if ( zm_get_option( 'cat_cover' ) ) { ?>
									<div class="cms-novel-cove-img">
										<a class="thumbs-back sc" href="<?php echo esc_url( get_term_link( $term ) ); ?>" rel="bookmark" <?php echo goal(); ?>>
											<div class="novel-cove-img" data-src="<?php echo cat_cover_url( $term->term_id ); ?>"></div>
										</a>
										<?php if ( $instance['show_mark'] ) { ?>
											<div class="special-mark bz fd"><?php echo be_get_option( 'cms_novel_mark' ); ?></div>
										<?php } ?>
									</div>
								<?php } else { ?>
									<div class="cat-cover-tip">未启用分类封面</div>
								<?php } ?>

							</div>

							<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" rel="bookmark" <?php echo goal(); ?>>
								<div class="novel-cover-des">
									<h4 class="cat-novel-title"><?php echo $term->name; ?></h4>
									<div class="cat-novel-author">
										<?php if ( be_get_option( 'cms_novel_author' ) ) { ?>
											<?php if ( get_option( 'cat-author-' . $term->term_id ) ) { ?>
												<span><?php echo be_get_option( 'novel_author_t' ); ?></span>
												<?php echo get_option( 'cat-author-' . $term->term_id ); ?>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="cms-novel-des">
										<?php
										if ( get_option( 'cat-message-' . $term->term_id ) ) {
											$description = wpautop( get_option( 'cat-message-' . $term->term_id ) );
											echo wp_trim_words( $description, 30, '...' );
										} else {
											if ( category_description( $term->term_id ) ) {
												$description = category_description( $term->term_id );
												echo wp_trim_words( $description, 30, '...' );
											} else {
												echo '为分类添加描述或附加信息';
											}
										}
										?>
									</div>
								</div>
							</a>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php
		// 缓存：保存到缓存并显示
		if ( $enable_cache ) {
			$widget_output = ob_get_clean();
			set_transient( $cache_key, $widget_output, DAY_IN_SECONDS );
			echo $widget_output;
		}

		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance                 = array();
		$instance['show_mark']    = ! empty( $new_instance['show_mark'] ) ? 1 : 0;
		$instance['cat_id']       = strip_tags( $new_instance['cat_id'] );
		$instance['enable_cache'] = ! empty( $new_instance['enable_cache'] ) ? 1 : 0;

		// 缓存：清除旧缓存
		$cache_key = 'begin_widget_novel_' . md5( $instance['cat_id'] );
		delete_transient( $cache_key );

		return $instance;
	}
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_mark' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_mark' ) ); ?>" <?php checked( (bool) $instance['show_mark'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_mark' ) ); ?>">显示角标</label>
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'enable_cache' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'enable_cache' ) ); ?>" <?php checked( (bool) $instance['enable_cache'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'enable_cache' ) ); ?>">启用缓存</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_id' ); ?>">输入分类ID：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'cat_id' ); ?>" name="<?php echo $this->get_field_name( 'cat_id' ); ?>" type="text" value="<?php echo $instance['cat_id']; ?>" />
			多个ID用英文半角逗号","隔开，按先后排序
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

add_action( 'widgets_init', 'be_widget_novel_init' );
function be_widget_novel_init() {
	register_widget( 'be_widget_novel' );
}
