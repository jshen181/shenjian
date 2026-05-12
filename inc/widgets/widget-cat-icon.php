<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 图标分类目录
class widget_cat_icon extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_cat_icon',
			'description' => '分类目录可以显示分类图标',
			'customize_selective_refresh' => true,
		);
		parent::__construct('widget_cat_icon', '分类目录', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_icon_no'   => 0,
			'e_cat'          => '',
			'title_z'        => '',
			'show_icon'      => '',
			'show_svg'       => '',
			'hide_empty'     => 0,
			'title'          => '分类目录',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title_w = title_i_w();
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$e_cat = strip_tags($instance['e_cat']);
?>


<div class="be_widget_cat<?php if ($instance['show_icon_no']) { ?> widget-cat-ico<?php } ?>">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<?php
			if ( $instance['hide_empty'] ) {
				$hide_empty = 0;
			} else {
				$hide_empty = 1;
			}
			$terms = get_terms(
				array(
					'taxonomy'   => array( 'category', 'taobao', 'gallery', 'videos', 'products', 'notice' ),
					'exclude' => $e_cat,
					'hide_empty' => $hide_empty,
					'orderby'    => 'id',
					'order'      => 'ASC',
				)
			);
			foreach ( $terms as $term ) {
		?>

		<li>
			<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" rel="bookmark" <?php echo goal(); ?>>
				<?php if (zm_get_option('cat_icon') && $instance['show_icon_no']) { ?>
					<?php if (get_option('zm_taxonomy_icon'. $term->term_id )) { ?><i class="widget-icon <?php echo zm_taxonomy_icon_code( $term->term_id ); ?>"></i><?php } ?>
					<?php if (get_option('zm_taxonomy_svg'. $term->term_id )) { ?><svg class="widget-icon icon" aria-hidden="true"><use xlink:href="#<?php echo zm_taxonomy_svg_code( $term->term_id ); ?>"></use></svg><?php } ?>
				<?php } ?>
				<?php echo $term->name; ?>
			</a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_icon_no'] = !empty($new_instance['show_icon_no']) ? 1 : 0;
		$instance['hide_empty'] = !empty($new_instance['hide_empty']) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['e_cat'] = strip_tags($new_instance['e_cat']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '分类目录';
		}
		global $wpdb;
		$e_cat = strip_tags($instance['e_cat']);
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
		<label for="<?php echo $this->get_field_id('e_cat'); ?>">输入排除的分类ID：</label>
		<textarea style="height:50px;" class="widefat" id="<?php echo $this->get_field_id( 'e_cat' ); ?>" name="<?php echo $this->get_field_name( 'e_cat' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['e_cat'] ), ENT_QUOTES)); ?></textarea>
	</p>
	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_empty') ); ?>" <?php checked( (bool) $instance["hide_empty"], true ); ?>>
			显示空分类
		</label>
	</p>
	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id('show_icon_no') ); ?>">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_icon_no') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_icon_no') ); ?>" <?php checked( (bool) $instance["show_icon_no"], true ); ?>>
			显示分类图标
		</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'widget_cat_icon_init' );
function widget_cat_icon_init() {
	register_widget( 'widget_cat_icon' );
}