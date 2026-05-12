<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 分类封面
class widget_cover extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_cover',
			'description' => '以图片封面形式显示分类',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_cover', '分类封面', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_tags'     => 0,
			'show_cat_ico'  => 1,
			'cat_id'        => '',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		echo $before_widget;
		$postid = $instance['cat_id'];
?>

<div class="widget-cat-cover">
		<?php
			if ( $instance['show_tags'] ) {
				$terms = get_tags(
					array(
						'include'    => $instance['cat_id'],
						'hide_empty' => false,
						'orderby'    => 'include', 
						'order'      => 'DESC',
					)
				);
			} else {
				$terms = get_terms(
					array(
						'taxonomy'   => 'category',
						'include'    => $instance['cat_id'],
						'hide_empty' => false,
						'orderby'    => 'include', 
						'order'      => 'DESC',
					)
				);
			}
			foreach ( $terms as $term ) {
		?>
		<div class="cat-rec-widget">
			<div class="cat-rec-main">
				<?php if ( zm_get_option( 'cat_cover' ) ) { ?>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" rel="bookmark">
						<div class="boxs1">
							<div class="cat-rec-content ms" <?php aos_a(); ?>>
								<div class="cat-rec lazy<?php if ( get_option( 'zm_taxonomy_svg' . $term->term_id ) ) { ?> cat-rec-ico-svg<?php } else { ?> cat-rec-ico-img<?php } ?>">
									<?php if ( $instance['show_cat_ico'] ) { ?>
										<?php if ( zm_get_option( 'cat_icon' ) ) { ?>
											<?php if ( !get_option('zm_taxonomy_svg' . $term->term_id ) ) { ?>
												<?php if ( get_option('zm_taxonomy_icon' . $term->term_id ) ) { ?><i class="cat-rec-icon fd <?php echo zm_taxonomy_icon_code( $term->term_id ); ?>"></i><?php } ?>
											<?php } else { ?>
												<?php if ( get_option('zm_taxonomy_svg' . $term->term_id ) ) { ?><svg class="cat-rec-svg fd icon" aria-hidden="true"><use xlink:href="#<?php echo zm_taxonomy_svg_code( $term->term_id ); ?>"></use></svg><?php } ?>
											<?php } ?>
										<?php } ?>
									<?php } else { ?>
										<?php if ( zm_get_option( 'cat_cover' ) ) { ?>
											<div class="cat-rec-back" data-src="<?php echo cat_cover_url( $term->term_id ); ?>"></div>
										<?php } ?>
									<?php } ?>
								</div>
								<h4 class="cat-rec-title"><?php echo $term->name; ?></h4>
								<?php if ( category_description( $term->term_id ) ) { ?>
									<div class="cat-rec-des"><?php echo category_description( $term->term_id ); ?></div>
								<?php } else { ?>
									<div class="cat-rec-des"><?php _e( '暂无描述', 'begin' ); ?></div>
								<?php } ?>
								<div class="rec-adorn-s"></div><div class="rec-adorn-x"></div>
								<div class="clear"></div>
							</div>
						</div>
					</a>
				<?php } else { ?>
					<div class="cat-cover-tip">未启用分类封面</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_tags'] = !empty($new_instance['show_tags']) ? 1 : 0;
		$instance['show_cat_ico'] = !empty($new_instance['show_cat_ico']) ? 1 : 0;
		$instance['cat_id'] = strip_tags($new_instance['cat_id']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		global $wpdb;
?>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_cat_ico') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_cat_ico') ); ?>" <?php checked( (bool) $instance["show_cat_ico"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_cat_ico') ); ?>">图标模式</label>
	</p>

	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_tags') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_tags') ); ?>" <?php checked( (bool) $instance["show_tags"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_tags') ); ?>">调用标签</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'cat_id' ); ?>">输入分类或标签ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>" type="text" value="<?php echo $instance['cat_id']; ?>" />
		多个ID用英文半角逗号","隔开，按先后排序
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'widget_cover_init' );
function widget_cover_init() {
	register_widget( 'widget_cover' );
}