<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 热评文章
class hot_comment extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'hot_comment',
			'description'                 => '调用评论最多的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'hot_comment', '热评文章', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_thumbs' => 1,
			'title_all'   => 1,
			'mycat'       => 0,
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'number'      => 5,
			'days'        => 90,
			'title'       => '热评文章',
			'e_cat'       => '',
			'out_post'    => '',
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
		$days   = strip_tags( $instance['days'] ) ? absint( $instance['days'] ) : 90;

		if ( $instance['show_thumbs'] ) {
			echo '<div class="new_cat">';
		} else {
			echo '<div id="hot_comment_widget" class="widget-li-icon' . ( ! $instance['title_all'] ? ' title-li-all' : '' ) . '">';
		}

		if ( $instance['show_icon'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'] . '</h3>';
		}

		if ( $instance['show_svg'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#' . $instance['show_svg'] . '"></use></svg>' . $instance['title_z'] . '</h3>';
		}

		echo '<ul>';

		if ( $instance['mycat'] ) {
			$cat = get_the_category();
			foreach ( $cat as $key => $category ) {
				$catid = $category->term_id;
			}
		} else {
			$catid = '';
		}

		$review = new WP_Query(
			array(
				'post_type'           => array( 'post' ),
				'showposts'           => $number,
				'cat'                 => $catid,
				'category__not_in'    => explode( ',', $instance['e_cat'] ),
				'post__not_in'        => explode( ',', $instance['out_post'] ),
				'ignore_sticky_posts' => true,
				'orderby'             => 'comment_count',
				'order'               => 'DESC',
				'date_query'          => array(
					array(
						'after' => '' . $days . 'month ago',
					),
				),
			)
		);

		$i = 0;
		while ( $review->have_posts() ) :
			$review->the_post();
			++$i;

			if ( $instance['show_thumbs'] ) {
				echo '<li>';
				echo '<span class="thumbnail">';
				echo zm_thumbnail();
				echo '</span>';
				echo '<span class="new-title"><a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></span>';
				echo grid_meta();
				echo '<span class="discuss">' . get_comments_number_text( '', '<i class="be be-speechbubble ri"></i>1 ', '<i class="be be-speechbubble ri"></i>%' ) . '</span>';
				echo '</li>';
			} elseif ( ! $instance['title_all'] ) {
					echo '<li class="title-all-item">';
					echo '<span class="li-icon li-icon-' . $i . '">' . $i . '</span>';
					echo '<span class="title-all">';
					echo '<a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a>';
					echo '<span class="title-all-inf">';
					echo grid_meta();
					echo '<span class="discuss">' . get_comments_number_text( '', '<i class="be be-speechbubble ri"></i>1 ', '<i class="be be-speechbubble ri"></i>%' ) . '</span>';
					echo '</span></span></li>';
			} else {
				echo '<li class="srm"><span class="li-icon li-icon-' . $i . '">' . $i . '</span><a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></li>';
			}
		endwhile;

		wp_reset_postdata();
		echo '</ul></div>';
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance                = array();
		$instance['show_thumbs'] = ! empty( $new_instance['show_thumbs'] ) ? 1 : 0;
		$instance['title_all']   = ! empty( $new_instance['title_all'] ) ? 1 : 0;
		$instance['mycat']       = ! empty( $new_instance['mycat'] ) ? 1 : 0;
		$instance['title_z']     = strip_tags( $new_instance['title_z'] );
		$instance['show_icon']   = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg']    = strip_tags( $new_instance['show_svg'] );
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['number']      = strip_tags( $new_instance['number'] );
		$instance['days']        = strip_tags( $new_instance['days'] );
		$instance['e_cat']       = strip_tags( $new_instance['e_cat'] );
		$instance['out_post']    = strip_tags( $new_instance['out_post'] );
		return $instance;
	}
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '热评文章';
		}
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, array( 'number' => '5' ) );
		$instance = wp_parse_args( (array) $instance, array( 'days' => '90' ) );
		$number   = strip_tags( $instance['number'] );
		$days     = strip_tags( $instance['days'] );
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
			<label for="<?php echo $this->get_field_id( 'days' ); ?>">时间限定：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="number" step="1" min="1" value="<?php echo $days; ?>" size="3" />
			<label>有图/无图：月/天</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'e_cat' ); ?>">排除的分类ID：</label>
			<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'e_cat' ); ?>" name="<?php echo $this->get_field_name( 'e_cat' ); ?>"><?php echo stripslashes( htmlspecialchars( ( $instance['e_cat'] ), ENT_QUOTES ) ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'out_post' ); ?>">排除的文章ID：</label>
			<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'out_post' ); ?>" name="<?php echo $this->get_field_name( 'out_post' ); ?>"><?php echo stripslashes( htmlspecialchars( ( $instance['out_post'] ), ENT_QUOTES ) ); ?></textarea>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbs' ) ); ?>" <?php checked( (bool) $instance['show_thumbs'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>">显示缩略图</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'title_all' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_all' ) ); ?>" <?php checked( (bool) $instance['title_all'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_all' ) ); ?>">截断标题</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'mycat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mycat' ) ); ?>" <?php checked( (bool) $instance['mycat'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'mycat' ) ); ?>">相同分类</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

function hot_comment_init() {
	register_widget( 'hot_comment' );
}
add_action( 'widgets_init', 'hot_comment_init' );
