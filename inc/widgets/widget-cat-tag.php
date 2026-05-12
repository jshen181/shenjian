<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 分类标签
class widget_cat_tag extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_cat_tag',
			'description' => '分类标签',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_cat_tag', '分类标签', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'cat_id'        => '',
			'number'        => 20,
			'title'         => '分类标签',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 20;
	if ( !is_page() && get_post_type() == 'post' ) {
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$cat_id = strip_tags($instance['cat_id']);
	}
?>

<?php if ( !is_page() && get_post_type() == 'post' ) { ?>
<?php if ($instance['show_icon']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
<?php } ?>
<?php if ($instance['show_svg']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
<?php } ?>

<div class="tagcloud tagcloud-cat">
	<?php 
		$category = get_the_category();
		$all_tags[] = '';
		if ( $instance['cat_id'] ) {
			$be_catid = $instance['cat_id'];
		} else {
			$be_catid = $category[0]->cat_ID;
		}
		$custom_query = new WP_Query ( array( 'cat' => $be_catid, 'posts_per_page' => -1 ) );

		if ( $custom_query->have_posts() ) :
			while ( $custom_query->have_posts() ) : $custom_query->the_post();
				$posttags = get_the_tags();
				if ( $posttags ) {
					foreach( $posttags as $tag ) {
						$all_tags[] = $tag->term_id;
					}
				}
			endwhile;

			$tags_arr = array_unique( $all_tags );
			$tags_str = implode( ",", $tags_arr );

			$args = array(
				'smallest'  => 12,
				'largest'   => 16,
				'unit'      => 'px',
				'number'    => $number,
				//'format'    => 'list',
				'include'   => $tags_str
			);
			wp_tag_cloud( $args );
		else :
			echo 'No found.';
		endif;
		wp_reset_postdata();
	?>
	<div class="clear"></div>
</div>
<?php } ?>

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
		$instance['cat_id'] = strip_tags($new_instance['cat_id']);
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '分类标签';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '20'));
		$number = strip_tags($instance['number']);
		$cat_id = strip_tags($instance['cat_id']);
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
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" style="width: 80px; margin-left: 10px;" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('cat_id'); ?>">输入分类ID：</label>
		<textarea style="height:50px;" class="widefat" id="<?php echo $this->get_field_id( 'cat_id' ); ?>" name="<?php echo $this->get_field_name( 'cat_id' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['cat_id'] ), ENT_QUOTES)); ?></textarea>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'widget_cat_tag_init' );
function widget_cat_tag_init() {
	register_widget( 'widget_cat_tag' );
}