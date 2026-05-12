<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 分类模块
class t_img_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 't_img_cat',
			'description' => '显示全部分类或某个分类的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 't_img_cat', '分类模块', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_time'   => 0,
			'show_icon'   => 0,
			'title_z'     => '',
			'numposts'    => 5,
			'cat'         => '',
			'title'       => '分类模块',
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
			if ( ! $hideTitle && $title ) {
				if ($titleUrl) $title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
			}
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title; 
?>

<div class="w-img-cat<?php if ($instance['show_time']) { ?> w-img-cat-time<?php } ?>">
	<?php if ( zm_get_option( 'cat_icon' ) && $instance['show_icon'] ) { ?>
		<h3 class="widget-title-icon cat-w-icon">
			<a href="<?php echo $titleUrl; ?>" rel="bookmark">
				<?php if ( get_option( 'zm_taxonomy_icon' . $instance['cat'] ) ) { ?><i class="t-icon <?php echo zm_taxonomy_icon_code( $instance['cat'] ); ?>"></i><?php } ?>
				<?php if ( get_option( 'zm_taxonomy_svg' . $instance['cat'] ) ) { ?><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo zm_taxonomy_svg_code( $instance['cat'] ); ?>"></use></svg><?php } ?>
				<?php echo $instance['title_z']; ?><?php more_i(); ?>
			</a>
		</h3>
	<?php } ?>
		<?php 
			global $post;
			if ( is_single() ) {
			$q =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts'           => '1',
				'post__not_in'        => array( $post->ID ),
				'category__and'       => $instance['cat'],
			));
			} else {
			$q =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts'           => '1',
				'category__and'       => $instance['cat']
			));
		} ?>
		<?php while ( $q->have_posts() ) : $q->the_post(); ?>
			<figure class="w-thumbnail"><?php echo zm_long_thumbnail(); ?></figure>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<ul class="title-img-cat">
		<?php 
			global $post;
			if ( is_single() ) {
			$q =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts'           => $instance['numposts'],
				'post__not_in'        => array( $post->ID ),
				'category__and'       => $instance['cat'],
			));
			} else {
			$q =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts'           => $instance['numposts'],
				'category__and'       => $instance['cat']
			));
		} ?>
		<?php while ( $q->have_posts() ) : $q->the_post(); ?>
		<?php if ( $instance['show_time'] ) { ?><span class="w-list-date"><?php the_time('m/d'); ?></span><?php } ?>
		<li class="srm the-icon"><?php the_title( sprintf( '<a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?></li>
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
	$instance['show_icon'] = ! empty( $new_instance['show_icon'] ) ? 1 : 0;
	$instance['show_time'] = ! empty( $new_instance['show_time'] ) ? 1 : 0;
	$instance['hideTitle'] = ! empty( $new_instance['hideTitle'] ) ? 1 : 0;
	$instance['newWindow'] = ! empty( $new_instance['newWindow'] ) ? 1 : 0;
	$instance['title'] = strip_tags( $new_instance['title']);
	$instance['title_z'] = strip_tags( $new_instance['title_z'] );
	$instance['titleUrl'] = strip_tags( $new_instance['titleUrl'] );
	$instance['numposts'] = $new_instance['numposts'];
	$instance['cat'] = $new_instance['cat'];
	return $instance;
}

function form( $instance ) {
	$defaults = $this -> zm_defaults();
	$instance = wp_parse_args( ( array ) $instance, $defaults );
	$instance = wp_parse_args( ( array ) $instance, array( 
		'title'    => '分类模块',
		'titleUrl' => '',
		'numposts' => 5,
		'cat' => 0 ) );
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
		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>">选择分类：
				<?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：
				<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
			</label>
		</p>

		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo esc_attr( $this->get_field_id('show_icon') ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_icon') ); ?>" <?php checked( (bool) $instance["show_icon"], true ); ?>>
				显示分类图标
			</label>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo esc_attr( $this->get_field_id('show_time') ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_time') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_time') ); ?>" <?php checked( (bool) $instance["show_time"], true ); ?>>
				显示时间
			</label>
		</p>

<?php }
}

add_action( 'widgets_init', 't_img_cat_init' );
function t_img_cat_init() {
	register_widget( 't_img_cat' );
}