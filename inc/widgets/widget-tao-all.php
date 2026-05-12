<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 商品信息
class be_tao_all_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_tao_all_widget',
			'description' => '调用指定商品文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_tao_all_widget', '商品信息', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'post_id' => '',
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		echo $before_widget;
?>

<div class="tao_widget">
	<?php
		$args = array(
			'post__in'  => explode( ',', $instance['post_id']),
			'post_type' => 'any',
			'orderby'   => 'post__in', 
			'order'     => 'DESC',
			'ignore_sticky_posts' => true, 
			'post_status'    => 'publish',
			'no_found_rows'       => true,
		);
		$be_query = new WP_Query($args);
	?>

	<?php while ($be_query->have_posts()) : $be_query->the_post(); ?>
	<div class="boxs1">
		<div class="tao-h">
			<figure class="tao-h-img">
				<?php echo tao_thumbnail(); ?>
				<?php if ( get_post_meta( get_the_ID(), 'tao_img_t', true ) ) : ?>
					<div class="tao-dis"><?php $tao_img_t = get_post_meta( get_the_ID(), 'tao_img_t', true );{ echo $tao_img_t; } ?></div>
				<?php endif; ?>
			</figure>
			<div class="product-box">
				<?php the_title( sprintf( '<h2><a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
				<div class="ded">
					<ul class="price">
						<li class="pricex"><strong>￥ <?php $price = get_post_meta( get_the_ID(), 'pricex', true );{ echo $price; } ?>元</strong></li>
						<li class="pricey">
							<?php if ( !get_post_meta( get_the_ID(), 'pricey', true ) && !get_post_meta( get_the_ID(), 'spare_t', true ) ){ ?>
								已售：<?php views_tao(); ?>
							<?php } else { ?>
								<?php if ( get_post_meta( get_the_ID(), 'pricey', true ) ) : ?>
									<del>市场价：<?php $price = get_post_meta( get_the_ID(), 'pricey', true );{ echo $price; } ?>元</del>
								<?php endif; ?>

								<?php if ( get_post_meta( get_the_ID(), 'spare_t', true ) ) : ?>
									<?php $spare_t = get_post_meta( get_the_ID(), 'spare_t', true);{ echo $spare_t; } ?>
								<?php endif; ?>
							<?php } ?>
						</li>
					</ul>
					<div class="go-url">
						<div class="taourl">
							<?php if ( get_post_meta( get_the_ID(), 'taourl', true ) ) : ?>
								<?php
									if ( get_post_meta( get_the_ID(), 'm_taourl', true ) && wp_is_mobile() ) {
										$url = get_post_meta( get_the_ID(), 'm_taourl', true );
									} else {
										$url = get_post_meta( get_the_ID(), 'taourl', true );
									}
									echo '<div class="taourl"><a href=' . $url . ' rel="external nofollow" target="_blank" class="url">购买</a></div>';
								?>
							<?php endif; ?>
						</div>
						<div class="detail"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>>详情</a></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
	<?php endwhile;?>
	<?php wp_reset_postdata(); ?>
	<div class="clear"></div>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['post_id'] = strip_tags( $new_instance['post_id'] );
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		global $wpdb;
?>

	<p>
		<label for="<?php echo $this->get_field_id( 'post_id' ); ?>">选择分类：

		<p>
			<label for="<?php echo $this->get_field_id( 'post_id' ); ?>">输入文章ID：</label>
			<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['post_id'] ), ENT_QUOTES)); ?></textarea>
		</p>
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

if ( is_array( zm_get_option( 'be_type' ) ) && in_array( 'tao', zm_get_option( 'be_type' ) ) ) {
	add_action( 'widgets_init', 'be_tao_all_widget_init' );
	function be_tao_all_widget_init() {
		register_widget( 'be_tao_all_widget' );
	}
}
