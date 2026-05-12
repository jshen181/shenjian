<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 网站公告
class bulletin_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'bulletin_cat',
			'description' => '显示最新公告',
			'customize_selective_refresh' => true,
		);
		parent::__construct('bulletin_cat', '网站公告', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs'   => 0,
			'show_time'    => 1,
			'title_z'      => '',
			'show_icon'    => '',
			'show_svg'     => '',
			'cat'          => '',
			'numposts'     => 5,
			'title'        => '网站公告',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults = $this -> zm_defaults();
		$title_w = title_i_w();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
		$hideTitle = !empty($instance['hideTitle']) ? true : false;
		$titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
		$newWindow = !empty($instance['newWindow']) ? true : false;
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

	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
	<?php } ?>

<div class="notice-cat<?php if ($instance['show_thumbs']) { ?> notice-shuo<?php } ?>">
	<ul>
		<?php
			$args = array(
				'post_type' => 'bulletin',
				'showposts' => $instance['numposts'], 
			);

			if ($instance['cat']) {
				$args = array(
					'showposts' => $instance['numposts'], 
					'post_status'    => 'publish',
					'ignore_sticky_posts' => 1, 
					'no_found_rows'       => true,
					'tax_query' => array(
						array(
							'taxonomy' => 'notice',
							'terms' => $instance['cat']
						),
					)
				);
			}
		?>
		<?php $be_query = new WP_Query($args); while ($be_query->have_posts()) : $be_query->the_post(); ?>
		<li>
			<?php if ($instance['show_thumbs']) { ?>
				<a href="<?php echo get_category_link( $instance['cat'] ); ?>" rel="bookmark" <?php echo goal(); ?>>
					<span class="meta-author-avatar shuo-avatar">
						<?php 
							if ( zm_get_option( 'cache_avatar' ) ) {
								echo begin_avatar( get_the_author_meta('email'), '96', '', get_the_author() );
							} else {
								echo get_avatar( get_the_author_meta('email'), '96', '', get_the_author() );
							}
						?>
					</span>

					<span class="shuo-w">
						<span class="shuo-entry-meta">
							<span class="shuo-author"><?php the_author(); ?></span>
							<span class="clear"></span>
							<span class="shuo-inf shuo-date"><?php time_ago( $time_type ='post' ); ?></span>
							<span class="shuo-inf shuo-time"><?php echo get_the_time( 'H:i:s' ); ?></span>
						</span>
						<span class="clear"></span>
						<?php 
							$content = get_the_content();
							$content = wp_strip_all_tags(str_replace( array('[',']'),array('<','>'),$content ) );
							echo wp_trim_words( $content, zm_get_option( 'words_n' ), '...' );
						?>
					</span>
				</a>
			<?php } else { ?>
				<?php if ($instance['show_time']) { ?><span class="date-n"><span class="day-n"><?php the_time('m') ?></span>/<span class="month-n"><?php the_time('d') ?></span></span><?php } else { ?><i class="be be-volumedown"></i><?php } ?>
				<?php the_title( sprintf( '<a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
			<?php } ?>
		</li>
		<?php endwhile; ?>
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
	$instance['show_time'] = !empty($new_instance['show_time']) ? 1 : 0;
	$instance['hideTitle'] = !empty($new_instance['hideTitle']) ? 1 : 0;
	$instance['newWindow'] = !empty($new_instance['newWindow']) ? 1 : 0;
	$instance['show_icon'] = strip_tags($new_instance['show_icon']);
	$instance['show_svg'] = strip_tags($new_instance['show_svg']);
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['title_z'] = strip_tags($new_instance['title_z']);
	$instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
	$instance['numposts'] = $new_instance['numposts'];
	$instance['cat'] = $new_instance['cat'];
	return $instance;
}

function form( $instance ) {
	$defaults = $this -> zm_defaults();
	$instance = wp_parse_args( (array) $instance, $defaults );
	$instance = wp_parse_args( (array) $instance, array( 
		'title' => '网站公告',
		'titleUrl' => '',
		'numposts' => 5,
		'cat' => 0));
		$titleUrl = $instance['titleUrl'];
		 ?> 

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title_z'); ?>">图标标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name('title_z'); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('titleUrl'); ?>">标题链接：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo $titleUrl; ?>" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
			<label for="<?php echo $this->get_field_id('newWindow'); ?>">在新窗口打开标题链接</label>
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
			<label for="<?php echo $this->get_field_id('cat'); ?>">选择分类：
			<?php wp_dropdown_categories(array('taxonomy' => 'notice', 'name' => $this->get_field_name('cat'), 'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" style="width: 80px;" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_time') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_time') ); ?>" <?php checked( (bool) $instance["show_time"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('show_time') ); ?>">显示时间</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">链接到分类</label>
		</p>
<?php }
}

add_action( 'widgets_init', 'bulletin_cat_init' );
function bulletin_cat_init() {
	register_widget( 'bulletin_cat' );
}