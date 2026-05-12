<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 最新文章
class new_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'new_cat',
			'description'                 => '显示全部分类或某个分类的最新文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'new_cat', '最新文章', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_thumbs' => 1,
			'show_icon'   => 0,
			'show_time'   => 1,
			'cat_child'   => 1,
			'title_z'     => '',
			'show_svg'    => '',
			'numposts'    => 5,
			'cat'         => '',
			'title'       => '最新文章',
			'out_post'    => '',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults  = $this->zm_defaults();
		$title_w   = title_i_w();
		$instance  = wp_parse_args( (array) $instance, $defaults );
		$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$hideTitle = ! empty( $instance['hideTitle'] ) ? true : false;
		$titleUrl  = empty( $instance['titleUrl'] ) ? '' : $instance['titleUrl'];
		$newWindow = ! empty( $instance['newWindow'] ) ? true : false;
		if ( zm_get_option( 'more_im' ) ) {
			$more_i = '<span class="more-i more-im"><span></span><span></span><span></span></span>';
		} else {
			$more_i = '<span class="more-i"><span></span><span></span><span></span></span>';
		}
		echo $before_widget;
		if ( $newWindow ) {
			$newWindow = "target='_blank'";
		}
		if ( ! $hideTitle && $title ) {
			if ( $titleUrl ) {
				$title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
			}
		}
		if ( ! empty( $title ) ) {
			echo $before_title . $title_w . $title . $after_title;
		}

		if ( zm_get_option( 'cat_icon' ) && $instance['show_icon'] ) {
			echo '<h3 class="widget-title-icon cat-w-icon">';
			echo '<a href="' . $titleUrl . '" rel="bookmark" ' . $newWindow . '>';

			if ( get_option( 'zm_taxonomy_icon' . $instance['cat'] ) ) {
				echo '<i class="t-icon ' . zm_taxonomy_icon_code( $instance['cat'] ) . '"></i>';
			}

			if ( get_option( 'zm_taxonomy_svg' . $instance['cat'] ) ) {
				echo '<svg class="t-svg icon" aria-hidden="true"><use xlink:href="#' . zm_taxonomy_svg_code( $instance['cat'] ) . '"></use></svg>';
			}

			echo $instance['title_z'];
			more_i();
			echo '</a>';
			echo '</h3>';
		}

		echo '<div class="' . ( $instance['show_thumbs'] ? 'new_cat' : 'post_cat' ) . '">';
		echo '<ul>';

		global $post;

		if ( is_single() ) {
			$not = explode( ',', $instance['out_post'] . ',' . $post->ID );
		} else {
			$not = explode( ',', $instance['out_post'] );
		}

		$and = $instance['cat_child'] ? 'cat' : 'category__and';

		$q = new WP_Query(
			array(
				'ignore_sticky_posts' => 1,
				'showposts'           => $instance['numposts'],
				'post__not_in'        => $not,
				$and                  => $instance['cat'],
				'post_status'    => 'publish',
				'ignore_sticky_posts' => 1, 
				'no_found_rows'       => true,
			)
		);

		while ( $q->have_posts() ) :
			$q->the_post();
			if ( $instance['show_thumbs'] ) {
				echo '<li>';
				echo '<span class="thumbnail">';
				echo zm_thumbnail();
				echo '</span>';
				echo '<span class="new-title"><a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a></span>';
				echo grid_meta();
				echo views_span();
				echo '</li>';
			} else {
				$class = 'only-title';
				if ( $instance['show_time'] ) {
					$class .= ' only-title-date';
				}
				echo '<li class="' . $class . '">';
				if ( $instance['show_time'] ) {
					echo grid_meta();
				}
				echo '<a class="srm get-icon" href="' . esc_url( get_permalink() ) . '" rel="bookmark" ' . goal() . '>' . get_the_title() . '</a>';
				echo '</li>';
			}
		endwhile;
		wp_reset_postdata();

		echo '</ul>';
		echo '</div>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance                = array();
		$instance['show_thumbs'] = ! empty( $new_instance['show_thumbs'] ) ? 1 : 0;
		$instance['cat_child']   = ! empty( $new_instance['cat_child'] ) ? 1 : 0;
		$instance['show_icon']   = ! empty( $new_instance['show_icon'] ) ? 1 : 0;
		$instance['show_time']   = ! empty( $new_instance['show_time'] ) ? 1 : 0;
		$instance['hideTitle']   = ! empty( $new_instance['hideTitle'] ) ? 1 : 0;
		$instance['newWindow']   = ! empty( $new_instance['newWindow'] ) ? 1 : 0;
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['title_z']     = strip_tags( $new_instance['title_z'] );
		$instance['titleUrl']    = strip_tags( $new_instance['titleUrl'] );
		$instance['out_post']    = strip_tags( $new_instance['out_post'] );
		$instance['numposts']    = $new_instance['numposts'];
		$instance['cat']         = $new_instance['cat'];
		return $instance;
	}

	function form( $instance ) {
		$defaults     = $this->zm_defaults();
		$instance     = wp_parse_args( (array) $instance, $defaults );
		$instance     = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '最新文章',
				'titleUrl' => '',
				'numposts' => 5,
				'cat'      => 0,
			)
		);
			$titleUrl = $instance['titleUrl'];
		?>
			 
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_z' ); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'titleUrl' ); ?>">标题链接：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'titleUrl' ); ?>" name="<?php echo $this->get_field_name( 'titleUrl' ); ?>" type="text" value="<?php echo $titleUrl; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>">选择分类：
				<?php
				wp_dropdown_categories(
					array(
						'name'            => $this->get_field_name( 'cat' ),
						'show_option_all' => '全部分类',
						'hide_empty'      => 0,
						'hierarchical'    => 1,
						'selected'        => $instance['cat'],
					)
				);
				?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：
				<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'out_post' ); ?>">排除的文章ID：</label>
			<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'out_post' ); ?>" name="<?php echo $this->get_field_name( 'out_post' ); ?>"><?php echo stripslashes( htmlspecialchars( ( $instance['out_post'] ), ENT_QUOTES ) ); ?></textarea>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbs' ) ); ?>" <?php checked( (bool) $instance['show_thumbs'], true ); ?>>
				显示缩略图
			</label>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id( 'cat_child' ) ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'cat_child' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cat_child' ) ); ?>" <?php checked( (bool) $instance['cat_child'], true ); ?>>
				显示子分类
			</label>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id( 'newWindow' ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_id( 'newWindow' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'newWindow' ); ?>" <?php checked( isset( $instance['newWindow'] ) ? $instance['newWindow'] : 0 ); ?> />
				在新窗口打开标题链接
			</label>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo esc_attr( $this->get_field_id( 'show_time' ) ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_time' ) ); ?>" <?php checked( (bool) $instance['show_time'], true ); ?>>
				无缩略图显示时间
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_icon' ) ); ?>" <?php checked( (bool) $instance['show_icon'], true ); ?>>
				显示分类图标
			</label>
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'new_cat_init' );
function new_cat_init() {
	register_widget( 'new_cat' );
}
