<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 网址
class be_site_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'be_site_widget',
			'description'                 => '显示网址',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_site_widget', '网址收藏', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'show_favicon' => 1,
			'show_time'    => 1,
			'title_z'      => '',
			'show_icon'    => '',
			'show_svg'     => '',
			'cat'          => '',
			'numposts'     => 5,
			'title'        => '网址收藏',
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
		if ( $newWindow ) { $newWindow = "target='_blank'";
		}
		if ( ! $hideTitle && $title ) {
			if ( $titleUrl ) { $title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
			}
		}
		if ( ! empty( $title ) ) {
			echo $before_title . $title_w . $title . $after_title;
		}
		?>

		<?php if ( $instance['show_icon'] ) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
		<?php } ?>
		<?php if ( $instance['show_svg'] ) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><a href="<?php echo $titleUrl; ?>" target="_blank"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?><?php more_i(); ?></a></h3>
		<?php } ?>

		<div class="sites-widget<?php if ( ! $instance['show_favicon'] ) { ?> no-sites-cat-ico<?php } ?>">
			<ul>
				<?php
					global $post;
				$args = array(
					'post_type'    => 'sites',
					'showposts'    => $instance['numposts'],
					'post__not_in' => array( $post->ID ),
				);

				if ( $instance['cat'] ) {
					$args = array(
						'showposts'           => $instance['numposts'],
						'post_status'         => 'publish',
						'ignore_sticky_posts' => 1,
						'no_found_rows'       => true,
						'post__not_in'        => array( $post->ID ),
						'tax_query'           => array(
							array(
								'taxonomy' => 'favorites',
								'terms'    => $instance['cat'],
							),
						),
					);
				}
				$be_query = new WP_Query( $args );
				?>
				<?php while ( $be_query->have_posts() ) : $be_query->the_post(); ?>
					<li class="srm">
						<?php
							global $post;
							$sites_link = get_post_meta( get_the_ID(), 'sites_link', true );
							$sites_url  = get_post_meta( get_the_ID(), 'sites_url', true );
							$sites_ico  = get_post_meta( get_the_ID(), 'sites_ico', true );
						?>

						<?php if ( $instance['show_favicon'] ) { ?>
								<?php if ( get_post_meta( get_the_ID(), 'sites_ico', true ) ) { ?>
								<a href="<?php echo $sites_link; ?>" target="_blank" rel="external nofollow">
									<div class="sites-ico load">
										<img class="sites-img" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<?php echo $sites_ico; ?>" alt="<?php the_title(); ?>">
									</div>
								</a>
							<?php } else { ?>
								<a href="<?php echo $sites_link; ?>" target="_blank" rel="external nofollow">
									<div class="sites-ico load">
										<img class="sites-img" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<?php echo zm_get_option( 'favicon_api' ); ?><?php echo $sites_link; ?>" alt="<?php the_title(); ?>">
									</div>
								</a>
							<?php } ?>
						<?php } ?>

						<?php if ( $instance['show_time'] ) { ?><span class="date"><span><?php _e( '收录', 'begin' ); ?></span><time datetime="<?php echo get_the_date( 'Y-m-d' ); ?> <?php echo get_the_time( 'H:i:s' ); ?>"><?php the_time( 'm/d' ); ?></time></span><?php } ?>
						<?php the_title( sprintf( '<a class="sites-title-w" href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?>
					</li>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</ul>
		</div>

		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance                 = array();
		$instance['show_favicon'] = ! empty( $new_instance['show_favicon'] ) ? 1 : 0;
		$instance['show_time']    = ! empty( $new_instance['show_time'] ) ? 1 : 0;
		$instance['hideTitle']    = ! empty( $new_instance['hideTitle'] ) ? 1 : 0;
		$instance['newWindow']    = ! empty( $new_instance['newWindow'] ) ? 1 : 0;
		$instance['show_icon']    = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg']     = strip_tags( $new_instance['show_svg'] );
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['title_z']      = strip_tags( $new_instance['title_z'] );
		$instance['titleUrl']     = strip_tags( $new_instance['titleUrl'] );
		$instance['numposts']     = $new_instance['numposts'];
		$instance['cat']          = $new_instance['cat'];
		return $instance;
	}

	function form( $instance ) {
		$defaults     = $this->zm_defaults();
		$instance     = wp_parse_args( (array) $instance, $defaults );
		$instance     = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '网址收藏',
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
			<input type="checkbox" id="<?php echo $this->get_field_id( 'newWindow' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'newWindow' ); ?>" <?php checked( isset( $instance['newWindow'] ) ? $instance['newWindow'] : 0 ); ?> />
			<label for="<?php echo $this->get_field_id( 'newWindow' ); ?>">在新窗口打开标题链接</label>
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
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>">选择分类：
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'        => 'favorites',
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
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_favicon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_favicon' ) ); ?>" <?php checked( (bool) $instance['show_favicon'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_favicon' ) ); ?>">显示Favicon图标</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_time' ) ); ?>" <?php checked( (bool) $instance['show_time'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_time' ) ); ?>">显示日期</label>
		</p>
		<?php
	}
}

if ( is_array( zm_get_option( 'be_type' ) ) && in_array( 'sites', zm_get_option( 'be_type' ) ) ) {
	add_action( 'widgets_init', 'be_site_init' );
	function be_site_init() {
		register_widget( 'be_site_widget' );
	}
}
