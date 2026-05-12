<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 本周更新
class be_week_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_week_post',
			'description' => '本周更新的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_week_post', '本周更新', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs'   => 1,
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'number'        => 5,
			'title'         => '本周更新',
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
?>

<?php if ($instance['show_thumbs']) { ?>
<div class="new_cat">
<?php } else { ?>
<div class="mday_post">
<?php } ?>
	<ul>
		<?php
			$args = array(
				'ignore_sticky_posts' => 1,
				'date_query' => array(
					array(
						'year' => date( 'Y' ),
						'week' => date( 'W' ),
					),
				),
				'posts_per_page' => $number,
				'post_status'    => 'publish',
				'no_found_rows'       => true,
			);
			$query = new WP_Query( $args );
		?>
		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
			<li>
				<?php if ($instance['show_thumbs']) { ?>
					<span class="thumbnail"><?php echo zm_thumbnail(); ?></span>
					<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
					<span class="s-cat"><?php zm_category(); ?></span>
					<?php grid_meta(); ?>
				<?php } else { ?>
					<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
				<?php } ?>
				<div class="clear"></div>
			</li>
		<?php endwhile;?>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<li>
			<span class="new-title-no"><?php _e( '暂无更新', 'begin' ); ?></span>
		</li>
		<?php endif;?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_thumbs'] = !empty($new_instance['show_thumbs']) ? 1 : 0;
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
			$title = '本周更新';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
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

add_action( 'widgets_init', 'be_week_post_init' );
function be_week_post_init() {
	register_widget( 'be_week_post' );
}