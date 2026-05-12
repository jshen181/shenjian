<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 同分类文章
class same_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'same_post',
			'description' => '调用相同分类的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('same_post', '同分类文章', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs' => 1,
			'number'      => 5,
			'orderby'     => 'ASC',
			'title'       => '',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title_w  = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$title    = empty( $instance['title'] ) ? '' : $instance['title'];
		if ( ! empty( $title ) ) {
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		} else {
			if ( is_single() ) {
				if ( is_single() ) : global $post; $categories = get_the_category(); foreach ( $categories as $category ) : 
				$title = $category->name;
				endforeach; endif;
				wp_reset_query();
			}
		}
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number  = strip_tags( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$orderby = strip_tags( $instance['orderby'] ) ? absint( $instance['orderby'] ) : ASC;
?>

<?php if ( is_single() ) { ?>
<div class="<?php echo $instance['show_thumbs'] ? 'new_cat' : 'post_cat'; ?>">
	<ul>
		<?php 
			global $post;
			$cat   = get_the_category();
			$catid = '';
			if ( ! empty( $cat ) ) {
				$catid = $cat[0]->term_id;
			}

			$q = new WP_Query( array(
				'showposts'           => $number,
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'cat'                 => $catid,
				'post__not_in'        => array( $post->ID ),
				'orderby'             => $orderby,
				'order'               => $instance['orderby'],
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => true,
			) );
		?>
		<?php while ( $q->have_posts() ) : $q->the_post(); ?>
			<?php if ( $instance['show_thumbs'] ) { ?>
				<li class="cat-title">
				<span class="thumbnail">
					<?php echo zm_thumbnail(); ?>
				</span>
				<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
				<?php grid_meta(); ?>
				<?php views_span(); ?>
			</li>
			<?php } else { ?>
				<li class="srm the-icon"><?php the_title( sprintf( '<a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?></li>
			<?php } ?>

		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</ul>
	<div class="clear"></div>
</div>
<?php } ?>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_thumbs'] = !empty( $new_instance['show_thumbs'] ) ? 1 : 0;
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['number']      = strip_tags( $new_instance['number'] );
		$instance['orderby']     = strip_tags( $new_instance['orderby'] );
		return $instance;
	}
	function form( $instance ) {
		global $wpdb;
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$instance = wp_parse_args( ( array ) $instance, array( 'number' => '5' ) );
		$number = strip_tags( $instance['number'] );
		$instance = wp_parse_args(( array ) $instance, array( 'orderby' => 'ASC' ) );
		$orderby = strip_tags( $instance['orderby'] );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}

?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">自定义标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('orderby'); ?>">文章排序：</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" class="widefat">
			<option value="ASC"<?php selected( $instance['orderby'], 'ASC' ); ?>>旧的在上面</option>
			<option value="DESC"<?php selected( $instance['orderby'], 'DESC' ); ?>>新的在上面</option>
		</select>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>

	<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbs' ) ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', 'same_post_init' );
function same_post_init() {
	register_widget( 'same_post' );
}