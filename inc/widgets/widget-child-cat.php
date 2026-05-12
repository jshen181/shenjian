<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 父子分类名称
class child_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'child_cat',
			'description' => '用于显示当前文章或者分类，同级分类',
			'customize_selective_refresh' => true,
		);
		parent::__construct('child_cat', '同级分类', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title'     => '同级分类',
		);
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$title_w = title_i_w();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);

		if ( get_post_type() == 'post' && get_category_children( get_category_id( the_category_ID( false ) ) ) != "" ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( ! empty( $title ) )
			echo $before_title . $title_w . $title . $after_title;
		}

?>

<?php if ( get_post_type() == 'post' && get_category_children( get_category_id( the_category_ID( false ) ) ) != "" ) { ?>
	<div class="be_widget_cat related-cat">
		<?php
			echo '<ul class="cat_list">';
			echo wp_list_categories( "child_of=".get_category_id( the_category_ID( false ) ). "&depth=0&hide_empty=0&use_desc_for_title=&hierarchical=0&title_li=&orderby=id&order=ASC" );
			echo '</ul>';
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
		$instance['title'] = strip_tags( $new_instance['title'] );
		// $instance['author_url'] = $new_instance['author_url'];
		return $instance;
	}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '同级分类';
		}
		global $wpdb;
		// $instance = wp_parse_args((array) $instance, array('author_url' => ''));
		// $author_url = $instance['author_url'];
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'child_cat_init' );
function child_cat_init() {
	register_widget( 'child_cat' );
}