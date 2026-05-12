<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 同分类热门文章
class cat_popular extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'cat_popular',
			'description' => '调用相同分类的热门文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('cat_popular', '本类热门', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs'   => 1,
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'number'        => 5,
			'metakey'        => 'views',
			'catid'        => '',
			'title'         => '本类热门',
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
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		$metakey = strip_tags($instance['metakey']) ? absint( $instance['metakey'] ) : 'views';
?>

<?php if ($instance['show_thumbs']) { ?>
<div id="hot" class="hot_commend">
<?php } else { ?>
<div id="hot_comment_widget">
<?php } ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<?php 
			global $post, $catid;
			$cat = get_the_category();
			if ( $instance['catid'] ) { 
				$catid = $instance['catid'];
			} else {
				foreach($cat as $key=>$category){
					$catid = $category->term_id;
				}
			}
			$q = new WP_Query( array(
				'showposts' => $number,
				'post_type' => 'any',
				'cat' => $catid,
				'post__not_in' => array($post->ID),
				'meta_key' => $instance['metakey'],
				'orderby' => 'meta_value_num',
				'order' => 'date',
				'ignore_sticky_posts' => 1, 
				'no_found_rows'       => true,
			) );
		?>
		<?php $i = 1; while ($q->have_posts()) : $q->the_post(); ?>
			<?php if ($instance['show_thumbs']) { ?>
			<li>
				<span class="thumbnail">
					<?php echo zm_thumbnail(); ?>
				</span>
				<span class="hot-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
				<?php views_span(); ?>
				<span class="be-like"><i class="be be-thumbs-up-o ri"></i><?php zm_get_current_count(); ?></span>
			</li>
			<?php } else { ?>
				<li class="srm"><span class="new-title"><span class='li-icon li-icon-<?php echo($i); ?>'><?php echo($i++); ?></span><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span></li>
			<?php } ?>
		<?php endwhile;?>
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
		$instance['metakey'] = strip_tags($new_instance['metakey']);
		$instance['catid'] = strip_tags($new_instance['catid']);
		$instance['title'] = strip_tags( $new_instance['title'] );
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
			$title = '本类热门';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
		$instance = wp_parse_args((array) $instance, array('metakey' => 'views'));
		$metakey = strip_tags($instance['metakey']);
		$catid = strip_tags($instance['catid']);
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
		<label for="<?php echo $this->get_field_id('metakey'); ?>">自定义栏目名称：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('metakey'); ?>" name="<?php echo $this->get_field_name('metakey'); ?>" type="text" value="<?php echo $instance['metakey']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('catid'); ?>">指定分类ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('catid'); ?>" name="<?php echo $this->get_field_name('catid'); ?>" type="text" value="<?php echo $instance['catid']; ?>" />
	</p>
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

function cat_popular_init() {
	register_widget( 'cat_popular' );
}
add_action( 'widgets_init', 'cat_popular_init' );