<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 视频
class be_video_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_video_widget',
			'description' => '调用最新视频文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_video_widget', '最新视频', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'cat'           => '',
			'number'        => 4,
			'show_title'    => '',
			'title'         => '最新视频',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
		$titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
		$newWindow = !empty($instance['newWindow']) ? true : false;
		$hideTitle = !empty($instance['hideTitle']) ? true : false;
		if ( zm_get_option('more_im') ) {
			$more_i = '<span class="more-i more-im"><span></span><span></span><span></span></span>';
		} else {
			$more_i = '<span class="more-i"><span></span><span></span><span></span></span>';
		}
		echo $before_widget;
		if ($newWindow) $newWindow = "target='_blank'";
			if (!$hideTitle && $title) {
				if ($titleUrl) $title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
			}
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 4;
?>
<?php if ($instance['show_icon']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
<?php } ?>
<?php if ($instance['show_svg']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
<?php } ?>
<div class="widget-flex-img">
	<?php
		$args = array(
			'post_type' => 'video',
			'showposts' => $number, 
			'post_status'    => 'publish',
			'no_found_rows'       => true,
			);

			if ($instance['cat']) {
				$args = array(
					'showposts' => $number, 
					'tax_query' => array(
						array(
							'taxonomy' => 'videos',
							'terms' => $instance['cat']
						),
					)
				);
			}
			$fl = isset( $instance['fl'] ) ? $instance['fl'] : '';
 		?>

	<div class="wimg-main">
		<?php $be_query = new WP_Query($args); while ($be_query->have_posts()) : $be_query->the_post(); ?>
			<div class="wimg-item wimg-<?php echo $fl; ?>">
				<div class="wimg-area">
					<?php if ( $instance['show_title'] ) { ?>
						<div class="img-title"><a href="<?php the_permalink() ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title( sprintf( '<span class="img-title-t over">' ), '</span>' ); ?></a></div>
					<?php } ?>
					<?php echo videos_thumbnail(); ?>
					<i class="be be-play"></i>
				</div>
			</div>
		<?php endwhile;?>
		<?php wp_reset_postdata(); ?>
		<div class="clear"></div>
	</div>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['hideTitle'] = !empty($new_instance['hideTitle']) ? 1 : 0;
		$instance['newWindow'] = !empty($new_instance['newWindow']) ? 1 : 0;
		$instance['show_title'] = !empty($new_instance['show_title']) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['cat'] = $new_instance['cat'];
		$instance['fl'] = ! empty( $new_instance['fl'] ) ? sanitize_text_field( $new_instance['fl'] ) : '';
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$selected_option = isset( $instance['fl'] ) ? $instance['fl'] : '2';
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '最新视频';
		}
	global $wpdb;
	$instance = wp_parse_args((array) $instance, array('number' => '4'));
	$number = strip_tags($instance['number']);
	$instance = wp_parse_args((array) $instance, array('titleUrl' => ''));
	$titleUrl = $instance['titleUrl'];
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
		<label for="<?php echo $this->get_field_id('titleUrl'); ?>">标题链接：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo $titleUrl; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('newWindow'); ?>">
			<input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
			在新窗口打开标题链接
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_title' ); ?>">
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_title' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" <?php checked( isset( $instance['show_title'] ) ? $instance['show_title'] : 0 ); ?> />
			文章标题
		</label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('cat'); ?>">选择分类：
			<?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1,	'taxonomy' => 'videos', 'selected'=>$instance['cat'])); ?>
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</label>
	</p>

	<ul class="be-widget-radio">
		<li>
			<input type="radio" id="<?php echo $this->get_field_id( '2' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="2" <?php checked( $selected_option, '2' ); ?>>
			<label for="<?php echo $this->get_field_id( '2' ); ?>">2栏</label>
		</li>
		<li>
			<input type="radio" id="<?php echo $this->get_field_id( '3' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="3" <?php checked( $selected_option, '3' ); ?>>
			<label for="<?php echo $this->get_field_id( '3' ); ?>">3栏</label>
		</li>
		<li>
			<input type="radio" id="<?php echo $this->get_field_id( '4' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="4" <?php checked( $selected_option, '4' ); ?>>
			<label for="<?php echo $this->get_field_id( '4' ); ?>">4栏</label>
		</li>
		<li>
			<input type="radio" id="<?php echo $this->get_field_id( '5' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="5" <?php checked( $selected_option, '5' ); ?>>
			<label for="<?php echo $this->get_field_id( '5' ); ?>">5栏</label>
		</li>
		<li>
			<input type="radio" id="<?php echo $this->get_field_id( '6' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="6" <?php checked( $selected_option, '6' ); ?>>
			<label for="<?php echo $this->get_field_id( '6' ); ?>">6栏</label>
		</li>
	</ul>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

if ( is_array( zm_get_option( 'be_type' ) ) && in_array( 'video', zm_get_option( 'be_type' ) ) ) {
	add_action( 'widgets_init', 'be_video_widget_init' );
	function be_video_widget_init() {
		register_widget( 'be_video_widget' );
	}
}