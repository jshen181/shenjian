<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 专题
class widget_special extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_special',
			'description' => '调用专题封面',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_special', '专题封面', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_tags'   => 0,
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'pages_id'    => '',
			'title'       => '专题封面',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$postid = $instance['pages_id'];
?>

<div class="widget-cat-cover">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

	<?php if ($instance['pages_id']) { ?>
		<?php 
			global $post;
			$posts = get_posts( array( 'post_type' => 'any', 'orderby' => 'include', 'order' => 'DESC', 'include' => $instance['pages_id']) ); if ($posts) : foreach( $posts as $post ) : setup_postdata( $post );
		?>
		<div class="cover4x">
			<div class="cat-cover-main">
				<div class="cat-cover-img thumbs-b lazy">
					<?php $image = get_post_meta(get_the_ID(), 'thumbnail', true); ?>
					<a class="thumbs-back" href="<?php echo get_permalink( $post->ID ); ?>" rel="bookmark" <?php echo goal(); ?> data-src="<?php echo $image; ?>">
						<div class="special-mark fd"><?php _e( '专题', 'begin' ); ?></div>
						<div class="cover-des-box">
							<?php 
								$special = get_post_meta( get_the_ID(), 'special', true );
								echo '<div class="special-count fd">';
								echo get_tag_post_count( $special );
								echo  _e( '篇', 'begin' );
								echo '</div>';
							?>
							<div class="cover-des">
								<div class="cover-des-main over">
									<?php
									$description = get_post_meta( get_the_ID(), 'description', true );
									if ( get_post_meta( get_the_ID(), 'description', true ) ) { ?>
										<?php echo $description; ?>
									<?php } else { ?>
									<?php echo get_the_title($post->ID); ?>
									<?php } ?>
								</div>
							</div>
						</div>
					</a>
					<h4 class="cat-cover-title"><?php echo get_the_title($post->ID); ?></h4>
				</div>
			</div>
		</div>
		<?php endforeach; endif; ?>
		<?php wp_reset_query(); ?>
	<?php } else { ?>
		<ul><li><?php _e( '未添加专题ID', 'begin' ); ?></li></ul>
	<?php } ?>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['pages_id'] = strip_tags($new_instance['pages_id']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '专题封面';
		}
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('title_z'); ?>">图标标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name('title_z'); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
	</p>
	<p class="layoutflex">
		<label for="<?php echo $this->get_field_id('show_icon'); ?>">单色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_icon'); ?>" name="<?php echo $this->get_field_name('show_icon'); ?>" type="text" value="<?php echo $instance['show_icon']; ?>" />
		</label>
		<label for="<?php echo $this->get_field_id('show_svg'); ?>">彩色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_svg'); ?>" name="<?php echo $this->get_field_name('show_svg'); ?>" type="text" value="<?php echo $instance['show_svg']; ?>" />
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'pages_id' ); ?>">输入专题页面ID：</label>
		<textarea style="height:30px;" class="widefat" id="<?php echo $this->get_field_id( 'pages_id' ); ?>" name="<?php echo $this->get_field_name( 'pages_id' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['pages_id'] ), ENT_QUOTES)); ?></textarea>
		多个ID用英文半角逗号","隔开，按先后排序
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'widget_special_init' );
function widget_special_init() {
	register_widget( 'widget_special' );
}