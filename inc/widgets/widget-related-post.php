<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 相关文章
class related_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'related_post',
			'description'                 => '显示相关文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'related_post', '相关文章', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_thumbs' => 1,
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'number'      => 5,
			'title'       => '相关文章',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title_w  = title_i_w();
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title    = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title_w . $title . $after_title;
		}
		$number = strip_tags( $instance['number'] ) ? absint( $instance['number'] ) : 5;

		echo $instance['show_thumbs'] ? '<div class="new_cat">' : '<div class="post_cat">';

		if ( $instance['show_icon'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'] . '</h3>';
		}
		if ( $instance['show_svg'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#' . $instance['show_svg'] . '"></use></svg>' . $instance['title_z'] . '</h3>';
		}

		echo '<ul>';
		$post_num = $number;
		global $post;
		$tmp_post = $post;
		$tags     = '';
		$i        = 0;
		if ( get_the_tags( $post->ID ) ) {
			foreach ( get_the_tags( $post->ID ) as $tag ) {
				$tags .= $tag->slug . ',';
			}
			$tags    = strtr( rtrim( $tags, ',' ), ' ', '-' );
			$myposts = get_posts( 'numberposts=' . $post_num . '&tag=' . $tags . '&exclude=' . $post->ID );
			foreach ( $myposts as $post ) {
				setup_postdata( $post );

				if ( $instance['show_thumbs'] ) {
					echo '<li>
						<span class="thumbnail">' . zm_thumbnail() . '</span>
						<span class="new-title"><a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></span>';
					grid_meta();
					views_span();
					echo '</li>';
				} else {
					echo '<li class="srm the-icon"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></li>';
				}

				$i += 1;
			}
		}
		if ( $i < $post_num ) {
			$post = $tmp_post;
			setup_postdata( $post );
			$cats      = '';
			$post_num -= $i;
			foreach ( get_the_category( $post->ID ) as $cat ) {
				$cats .= $cat->cat_ID . ',';
			}
			$cats    = strtr( rtrim( $cats, ',' ), ' ', '-' );
			$myposts = get_posts( 'numberposts=' . $post_num . '&category=' . $cats . '&exclude=' . $post->ID );
			foreach ( $myposts as $post ) {
				setup_postdata( $post );

				if ( $instance['show_thumbs'] ) {
					echo '<li>
						<span class="thumbnail">' . zm_thumbnail() . '</span>
						<span class="new-title"><a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></span>';
					grid_meta();
					views_span();
					echo '</li>';
				} else {
					echo '<li class="srm"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></li>';
				}
			}
		}
		$post = $tmp_post;
		setup_postdata( $post );
		echo '</ul></div>';
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance                = array();
		$instance['show_thumbs'] = ! empty( $new_instance['show_thumbs'] ) ? 1 : 0;
		$instance['title_z']     = strip_tags( $new_instance['title_z'] );
		$instance['show_icon']   = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg']    = strip_tags( $new_instance['show_svg'] );
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['number']      = strip_tags( $new_instance['number'] );
		return $instance;
	}
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '相关文章';
		}
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, array( 'number' => '5' ) );
		$number   = strip_tags( $instance['number'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_z' ); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
		</p>
		<p class="layoutflex">
			<label for="<?php echo $this->get_field_id( 'show_icon' ); ?>">单色图标代码：
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_icon' ); ?>" name="<?php echo $this->get_field_name( 'show_icon' ); ?>" type="text" value="<?php echo $instance['show_icon']; ?>" />
			</label>
			<label for="<?php echo $this->get_field_id( 'show_svg' ); ?>">彩色图标代码：
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_svg' ); ?>" name="<?php echo $this->get_field_name( 'show_svg' ); ?>" type="text" value="<?php echo $instance['show_svg']; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbs' ) ); ?>" <?php checked( (bool) $instance['show_thumbs'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>">显示缩略图</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

function related_post_init() {
	register_widget( 'related_post' );
}
add_action( 'widgets_init', 'related_post_init' );
