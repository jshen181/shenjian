<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 调用指定ID文章
class ids_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'ids_post',
			'description' => '调用指定ID的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('ids_post', '指定文章', $widget_ops);
	}

	public function zm_get_defaults() {
		return array(
			'show_thumbs'   => 1,
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'id_post'       => '',
			'title'         => '指定文章',
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
		$id_post = strip_tags($instance['id_post']);
?>


<?php if ($instance['show_thumbs']) { ?>
<div class="new_cat">
<?php } else { ?>
<div class="ids_post">
<?php } ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
	<?php
		$args = array(
			'post__in'  => explode( ',', $id_post ), 
			'orderby'   => 'post__in', 
			'order'     => 'DESC',
			'ignore_sticky_posts' => true, 
			);
		$query = new WP_Query( $args );
	?>
	<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
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
				<span class="widget-cat"><i class="be be-sort"></i><?php zm_category(); ?></span>
			<?php } ?>
		</li>
		<?php endwhile; ?>
		<?php else : ?>
			<p>请输入文章ID</p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
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
		$instance['id_post'] = strip_tags($new_instance['id_post']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '指定文章';
		}
		global $wpdb;
		$id_post = strip_tags($instance['id_post']);
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
		<label for="<?php echo $this->get_field_id('id_post'); ?>">输入文章ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'id_post' ); ?>" name="<?php echo $this->get_field_name( 'id_post' ); ?>" type="text" value="<?php echo $id_post; ?>" size="15" />
		多个ID用英文半角逗号","隔开，按先后排序
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'ids_post_init' );
function ids_post_init() {
	register_widget( 'ids_post' );
}