<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 分类文章（图片）
class img_cat extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'img_cat',
			'description'                 => '以图片形式调用一个分类的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'img_cat', '分类图片', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_icon'  => 0,
			'title_z'    => '',
			'show_icon'  => '',
			'show_svg'   => '',
			'numposts'   => 4,
			'cat_child'  => 1,
			'cat'        => '',
			'show_title' => 1,
			'title'      => '分类图片',
			'out_post'   => '',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults  = $this->zm_defaults();
		$title_w   = title_i_w();
		$instance  = wp_parse_args( (array) $instance, $defaults );
		$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$titleUrl  = empty( $instance['titleUrl'] ) ? '' : $instance['titleUrl'];
		$hideTitle = ! empty( $instance['hideTitle'] ) ? true : false;
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

		echo '<div class="widget-flex-img">';

		global $post;
		if ( is_single() ) {
			$not = explode( ',', $instance['out_post'] . ',' . $post->ID );
		} else {
			$not = explode( ',', $instance['out_post'] );
		}

		$and = $instance['cat_child'] ? 'cat' : 'category__and';

		$q  = new WP_Query(
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
		$fl = isset( $instance['fl'] ) ? $instance['fl'] : '';

		echo '<div class="wimg-main">';

		while ( $q->have_posts() ) :
			$q->the_post();
			echo '<div class="wimg-item wimg-' . $fl . '">';
			echo '<div class="wimg-area">';

			if ( $instance['show_title'] ) {
				echo '<div class="img-title">';
				echo '<a href="' . get_permalink() . '" rel="bookmark" ' . goal() . '>';
				the_title( '<span class="img-title-t over">', '</span>' );
				echo '</a></div>';
			}

			echo zm_thumbnail();
			echo '</div></div>';
		endwhile;

		wp_reset_postdata();
		echo '<div class="clear"></div>';
		echo '</div></div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['show_icon']  = ! empty( $new_instance['show_icon'] ) ? 1 : 0;
		$instance['hideTitle']  = ! empty( $new_instance['hideTitle'] ) ? 1 : 0;
		$instance['newWindow']  = ! empty( $new_instance['newWindow'] ) ? 1 : 0;
		$instance['show_title'] = ! empty( $new_instance['show_title'] ) ? 1 : 0;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['title_z']    = strip_tags( $new_instance['title_z'] );
		$instance['titleUrl']   = strip_tags( $new_instance['titleUrl'] );
		$instance['out_post']   = strip_tags( $new_instance['out_post'] );
		$instance['cat_child']  = ! empty( $new_instance['cat_child'] ) ? 1 : 0;
		$instance['numposts']   = $new_instance['numposts'];
		$instance['cat']        = $new_instance['cat'];
		$instance['fl']         = ! empty( $new_instance['fl'] ) ? sanitize_text_field( $new_instance['fl'] ) : '';
		return $instance;
	}

	function form( $instance ) {
		$defaults        = $this->zm_defaults();
		$instance        = wp_parse_args( (array) $instance, $defaults );
		$selected_option = isset( $instance['fl'] ) ? $instance['fl'] : '2';
		$instance        = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '分类图片',
				'titleUrl' => '',
				'title_z'  => '',
				'numposts' => 4,
				'cat'      => 0,
			)
		);
			$titleUrl    = $instance['titleUrl'];
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
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
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
		<ul class="be-widget-radio">
			<li>
				<input type="radio" id="<?php echo $this->get_field_id( '2' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="2" <?php checked( $selected_option, '2' ); ?>>
				<label for="<?php echo $this->get_field_id( '2' ); ?>">2栏</label>
			</li>
			<li>
				<input type="radio" id="<?php echo $this->get_field_id( '3' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="3" <?php checked( $selected_option, '3' ); ?>>
				<label for="<?php echo $this->get_field_id( '3' ); ?>">3栏</label>
			</li>
			<li>
				<input type="radio" id="<?php echo $this->get_field_id( '4' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="4" <?php checked( $selected_option, '4' ); ?>>
				<label for="<?php echo $this->get_field_id( '4' ); ?>">4栏</label>
			</li>
			<li>
				<input type="radio" id="<?php echo $this->get_field_id( '5' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="5" <?php checked( $selected_option, '5' ); ?>>
				<label for="<?php echo $this->get_field_id( '5' ); ?>">5栏</label>
			</li>
			<li>
				<input type="radio" id="<?php echo $this->get_field_id( '6' ); ?>" name="<?php echo $this->get_field_name( 'fl' ); ?>" value="6" <?php checked( $selected_option, '6' ); ?>>
				<label for="<?php echo $this->get_field_id( '6' ); ?>">6栏</label>
			</li>
		</ul>
		<p>
			<label for="<?php echo $this->get_field_id( 'out_post' ); ?>">排除的文章ID：</label>
			<textarea style="height:35px;" class="widefat" id="<?php echo $this->get_field_id( 'out_post' ); ?>" name="<?php echo $this->get_field_name( 'out_post' ); ?>"><?php echo stripslashes( htmlspecialchars( ( $instance['out_post'] ), ENT_QUOTES ) ); ?></textarea>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'newWindow' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'newWindow' ); ?>" <?php checked( isset( $instance['newWindow'] ) ? $instance['newWindow'] : 0 ); ?> />
			<label for="<?php echo $this->get_field_id( 'newWindow' ); ?>">在新窗口打开标题链接</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'cat_child' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cat_child' ) ); ?>" <?php checked( (bool) $instance['cat_child'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat_child' ) ); ?>">显示子分类</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_icon' ) ); ?>" <?php checked( (bool) $instance['show_icon'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>">显示分类图标</label>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_title' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" <?php checked( isset( $instance['show_title'] ) ? $instance['show_title'] : 0 ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>">文章标题</label>
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'img_cat_init' );
function img_cat_init() {
	register_widget( 'img_cat' );
}
