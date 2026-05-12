<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 同标签的文章
class be_tag_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_tag_post',
			'description' => '调用相同标签的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_tag_post', '同标签文章', $widget_ops);
	}

	public function zm_get_defaults() {
		return array(
			'show_thumbs'   => 1,
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'tag_id'        => '',
			'number'        => 5,
			'title'         => '同标签文章',
		);
	}


	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		$tag_id = strip_tags($instance['tag_id']);
?>

<?php if ($instance['show_thumbs']) { ?>
<div class="new_cat">
<?php } else { ?>
<div class="tag_post">
<?php } ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

	<ul>
		<?php if ($instance['tag_id']) { ?>
			<?php $recent = new WP_Query( array( 'posts_per_page' => $number, 'tag__in' => explode(',', $tag_id)) ); ?>
			<?php while($recent->have_posts()) : $recent->the_post(); ?>
			<li>
				<?php if ($instance['show_thumbs']) { ?>
					<span class="thumbnail">
						<?php echo zm_thumbnail(); ?>
					</span>
				<?php } ?>
				<?php if ($instance['show_thumbs']) { ?>
					<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
				<?php } else { ?>
					<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
				<?php } ?>
				<?php if ($instance['show_thumbs']) { ?>
					<?php grid_meta(); ?>
					<?php views_span(); ?>
				<?php } ?>
			</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php } else { ?>
			<li>未输入标签ID</li>
		<?php } ?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_thumbs'] = !empty($new_instance['show_thumbs']) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tag_id'] = strip_tags($new_instance['tag_id']);
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '同标签文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
		$tag_id = strip_tags($instance['tag_id']);
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
		<label for="<?php echo $this->get_field_id('tag_id'); ?>">输入调用的标签 ID：</label>
		<input class="widefat"  id="<?php echo $this->get_field_id( 'tag_id' ); ?>" name="<?php echo $this->get_field_name( 'tag_id' ); ?>" type="text" value="<?php echo $tag_id; ?>" size="15" /></p>
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'be_tag_post_init' );
function be_tag_post_init() {
	register_widget( 'be_tag_post' );
}