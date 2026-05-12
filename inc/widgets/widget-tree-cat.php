<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 目录树
class Tree_Cat_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'tree-cat-widget',
			'description' => '以目录树方式显示子分类目录',
			'customize_selective_refresh' => true,
		);
		parent::__construct('Tree_Cat_Widget', '目录树', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'      => '',
			'show_icon'    => '',
			'show_tree'    => '1',
			'show_svg'     => '',
			'count'        => '1',
			'e_cat'        => '',
			'hide_empty'     => '',
			'title'        => '目录树',
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
		$e_cat = strip_tags($instance['e_cat']);
		$c = ! empty( $instance['count'] ) ? '1' : '0';
?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

<ul class="tree-cat<?php if ( ! $instance['count'] ) { ?> tree-cat-count-no<?php } ?>">
	<?php
		if ( $instance['hide_empty'] ) {
			$hide_empty = 0;
		} else {
			$hide_empty = 1;
		}

		if ( $instance['count'] ) {
			$show_count = 1;
		} else {
			$show_count = 0;
		}
		$args = wp_list_categories( array(
			'exclude'            => $e_cat,
			'hide_empty'         => $hide_empty,
			'echo'               => false,
			'show_count'         => $show_count,
			'use_desc_for_title' => 0,
			'include'            => '',
			'title_li'           => ''
		) );

		$args = preg_replace( '~\((\d+)\)(?=\s*+<)~', '<sup class="count">$1</sup>', $args );
		echo $args;
	?>
</ul>
<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['hide_empty'] = !empty($new_instance['hide_empty']) ? 1 : 0;
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['e_cat'] = strip_tags($new_instance['e_cat']);
		$instance['count'] = ! empty( $new_instance['count'] ) ? 1 : 0;
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '目录树';
		}
		global $wpdb;
		$e_cat = strip_tags($instance['e_cat']);
		$count = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id( 'count' ); ?>">显示文章数</label><br />
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_empty') ); ?>" <?php checked( (bool) $instance["hide_empty"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>">显示空分类</label>
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
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'Tree_Cat_Widget_init' );
function Tree_Cat_Widget_init() {
	register_widget( 'Tree_Cat_Widget' );
}