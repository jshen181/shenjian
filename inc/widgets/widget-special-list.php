<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 专题模块
class special_list extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'special_list',
			'description' => '调用某个专题文章列表',
			'customize_selective_refresh' => true,
		);
		parent::__construct('special_list', '专题模块', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'tag_slug'    => '',
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'show_svg'    => '',
			'numposts'    => 5,
			'title'       => '专题模块',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
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
?>

<div class="special-list">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $instance['titleUrl']; ?>" rel="bookmark"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
	<?php } ?>
	<?php
		$args = array(
			'posts_per_page' => '1', 
			'post_type' => 'page', 
			'meta_key' => 'special',
			'meta_value' => $instance['tag_slug'],
			'ignore_sticky_posts' => true, 
			'post_status'    => 'publish',
			'no_found_rows'       => true,
			);
		$query = new WP_Query( $args );
	?>
	<?php while ( $query->have_posts() ) : $query->the_post(); ?>

	<div class="cover4x">
		<div class="cat-cover-main">
			<div class="cat-cover-img">
				<a href="<?php echo get_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>>
					<div class="special-mark bz fd"><?php _e( '专题', 'begin' ); ?></div>
					<figure class="cover-img">
						<?php
							$image = get_post_meta( get_the_ID(), 'thumbnail', true );
							echo '<img src="' . $image. '" alt="' . get_the_title() . '" />'; 
						?>
					</figure>
					<div class="cover-des-box"><div class="cover-des"><?php $description = get_post_meta( get_the_ID(), 'description', true );{echo $description;} ?></div></div>
				</a>
				<div class="clear"></div>
			</div>
			<a href="<?php echo get_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>>
				<div class="special-title-widget"><?php the_title(); ?>
				<span class="special-more">
					<?php 
						global $wpdb, $post;
						$special = get_post_meta( get_the_ID(), 'special', true );
						echo '<span class="special-list-count">';
						if ( get_tag_post_count( $special ) >= 1 ) {
							echo  _e( '共', 'begin' );
							echo get_tag_post_count( $special );
							echo  _e( '篇', 'begin' );
						} else {
							echo '未添加文章</span>';
						}
						echo '</span>  ';
					?>
					<?php _e( '更多', 'begin' ); ?>
				</span>
			</div>
			</a>
		</div>
	</div>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>

	<ul class="post_cat">
	<?php
		$args = array(
			'tag_id' => get_tag_id_slug($instance['tag_slug']),
			'posts_per_page' => $instance['numposts'],
			'ignore_sticky_posts' => true, 
			);
		$query = new WP_Query( $args );
	?>
	<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
		<?php the_title( sprintf( '<li class="srm the-icon"><a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a></li>' ); ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<?php _e( '未添加文章', 'begin' ); ?>
		<?php endif; ?>
	</ul>
</div>

<?php
	echo $after_widget;
}

function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance = array();
	$instance['hideTitle'] = !empty($new_instance['hideTitle']) ? 1 : 0;
	$instance['newWindow'] = !empty($new_instance['newWindow']) ? 1 : 0;
	$instance['show_icon'] = strip_tags($new_instance['show_icon']);
	$instance['show_svg'] = strip_tags($new_instance['show_svg']);
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['title_z'] = strip_tags($new_instance['title_z']);
	$instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
	$instance['numposts'] = $new_instance['numposts'];
	$instance['tag_slug'] = $new_instance['tag_slug'];
	return $instance;
}

function form( $instance ) {
	$defaults = $this -> zm_defaults();
	$instance = wp_parse_args( (array) $instance, $defaults );
	$instance = wp_parse_args( (array) $instance, array( 
		'title' => '专题模块',
		'titleUrl' => '',
		'numposts' => 5
	));
		$titleUrl = $instance['titleUrl'];
		$tag_slug = $instance['tag_slug'];
		 ?> 

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
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
			<label for="<?php echo $this->get_field_id('tag_slug'); ?>">专题标签别名：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('tag_slug'); ?>" name="<?php echo $this->get_field_name('tag_slug'); ?>" type="text" value="<?php echo $tag_slug; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
			<label for="<?php echo $this->get_field_id('newWindow'); ?>">在新窗口打开标题链接</label>
		</p>
<?php }
}

add_action( 'widgets_init', 'special_list_init' );
function special_list_init() {
	register_widget( 'special_list' );
}