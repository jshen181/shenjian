<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 最近浏览的文章
class Be_recently_viewed extends WP_Widget {
	public function __construct() {
		$widget_options = array(
			'classname'                   => 'be_recently_viewed',
			'description'                 => '显示访问者最近浏览的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_recently_viewed', '最近浏览的文章', $widget_options );
	}

	public function widget( $args, $instance ) {
		// 添加 cookie 数据验证
		$be_cookie_posts = null;
		if ( isset( $_COOKIE['astx_recent_posts'] ) ) {
			$cookie_data = wp_unslash( $_COOKIE['astx_recent_posts'] );
			// 验证是否为有效的 JSON 数据
			if ( is_string( $cookie_data ) && ! empty( $cookie_data ) ) {
				$decoded_data = json_decode( $cookie_data, true );
				if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded_data ) ) {
					// 验证数组中的每个元素是否为有效的文章 ID
					$valid_posts     = array_filter(
						$decoded_data,
						function ( $post_id ) {
							return is_numeric( $post_id ) && $post_id > 0 && get_post( $post_id );
						}
					);
					$be_cookie_posts = array_values( $valid_posts );
				}
			}
		}

		if ( ! empty( $be_cookie_posts ) ) {
			$be_cookie_posts = array_diff( $be_cookie_posts, array( get_the_ID() ) );
			global $post;
			$post_id = get_the_ID();
			if ( count( $be_cookie_posts ) > 0 ) :
				$widget_id      = $args['widget_id'];
				$title_w        = title_i_w();
				$widget_id      = str_replace( 'be_recently_viewed-', '', $widget_id );
				$widget_options = get_option( $this->option_name );
				$instance       = $widget_options[ $widget_id ];
				$title          = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '最近浏览的文章';
				$title          = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
				$number         = ( ! empty( $instance['numberofposts'] ) ) ? absint( $instance['numberofposts'] ) : 5;
				$show_thumbnail = isset( $instance['showthumbnail'] ) ? $instance['showthumbnail'] : true;
				$clear_cookie   = isset( $instance['clearcookie'] ) ? $instance['clearcookie'] : false;
				$show_time      = isset( $instance['show_time'] ) ? $instance['show_time'] : true;
				$selected_types = isset( $instance['selected_types'] ) && is_array( $instance['selected_types'] ) ? $instance['selected_types'] : array( 'post', 'page' );
				extract( $args, EXTR_SKIP );
				$count = 0;
				foreach ( $be_cookie_posts as $post_id ) {
					if ( $count >= $number || absint( $post_id ) == 0 ) {
						break;
					}
					$post = get_post( absint( $post_id ) );
					if ( isset( $post ) && in_array( $post->post_type, $selected_types ) ) {
						++$count;
						if ( $count == 1 ) {
							echo $before_widget;
							if ( $title ) {
								echo $before_title . $title_w . $title . $after_title;
							}
							echo '<ul class="' . ( $show_thumbnail ? 'new_cat' : 'post_cat' ) . '">';
							if ( $clear_cookie ) {
								echo '<div class="clear-cookie" title="' . esc_attr__( '清除', 'begin' ) . '"><span class="dashicons dashicons-dismiss"></span></div>';
								?>
								<script>
								document.addEventListener('DOMContentLoaded', function() {
									document.querySelector('.clear-cookie').addEventListener('click', function() {
										var xhr = new XMLHttpRequest();
										xhr.open('POST', '<?php echo admin_url( 'admin-ajax.php' ); ?>');
										xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
										xhr.onload = function() {
											if (xhr.status === 200) {
												location.reload();
											}
										};
										xhr.send('action=clear_recently_viewed_cookie');
									});
								});
								</script>
								<?php
							}
						}
						$permalink = esc_url( get_permalink( $post->ID ) );

						if ( $show_thumbnail ) :
							echo '<li>';
							echo '<span class="thumbnail">' . zm_thumbnail() . '</span>';
							echo '<span class="new-title">';
							echo '<a href="' . $permalink . '" rel="bookmark" ' . goal() . '>' . apply_filters( 'the_title', $post->post_title, $post->ID ) . '</a>';
							echo '</span>';
							grid_meta();
							views_span();
							echo '</li>';
						else :
							echo '<li class="only-title' . ( $show_time ? ' only-title-date' : '' ) . '">';
							if ( $show_time ) {
								grid_meta();
							}
							echo '<a class="srm get-icon" href="' . $permalink . '" rel="bookmark" ' . goal() . '>' . apply_filters( 'the_title', $post->post_title, $post->ID ) . '</a>';
							echo '</li>';
						endif;
					}
				}
				if ( $count > 0 ) {
					echo '</ul>' . $after_widget;
				}
			endif;
		}
	}

	public function form( $instance ) {
		$widget_id          = str_replace( 'be_recently_viewed-', '', $this->id );
		$title              = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '最近浏览的文章';
		$numberofposts      = isset( $instance['numberofposts'] ) ? absint( $instance['numberofposts'] ) : 5;
		$show_thumbnail     = isset( $instance['showthumbnail'] ) ? (bool) $instance['showthumbnail'] : true;
		$clearcookie        = isset( $instance['clearcookie'] ) ? (bool) $instance['clearcookie'] : false;
		$show_time          = isset( $instance['show_time'] ) ? (bool) $instance['show_time'] : true;
		$selected_types     = isset( $instance['selected_types'] ) && is_array( $instance['selected_types'] ) ? $instance['selected_types'] : array( 'post', 'page' );
		$custom_post_types  = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'names',
			'and'
		);
		$default_post_types = array(
			'post' => 'post',
			'page' => 'page',
		);
		$post_types         = array_merge( $custom_post_types, $default_post_types );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'numberofposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numberofposts' ); ?>" name="<?php echo $this->get_field_name( 'numberofposts' ); ?>" type="number" size="3" value="<?php echo $numberofposts; ?>"/>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_thumbnail ); ?> id="<?php echo $this->get_field_id( 'showthumbnail' ); ?>" name="<?php echo $this->get_field_name( 'showthumbnail' ); ?>">
			<label for="<?php echo $this->get_field_id( 'showthumbnail' ); ?>">显示缩略图</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_time ); ?> id="<?php echo $this->get_field_id( 'show_time' ); ?>" name="<?php echo $this->get_field_name( 'show_time' ); ?>">
			<label for="<?php echo $this->get_field_id( 'show_time' ); ?>">无图显示日期</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $clearcookie ); ?> id="<?php echo $this->get_field_id( 'clearcookie' ); ?>" name="<?php echo $this->get_field_name( 'clearcookie' ); ?>">
			<label for="<?php echo $this->get_field_id( 'clearcookie' ); ?>">清除按钮</label>
		</p>

		<p>
			<label>选择</label>
			<ul class="be-widget-radio">
				<?php
				$post_types = array_reverse( $post_types );
				foreach ( $post_types as $post_type ) {
					$obj = get_post_type_object( $post_type );
					echo '<li>';
					echo '<input type="checkbox" class="checkbox" id="be_checkbox_' . $post_type . '" name="' . $this->get_field_name( 'selected_types' ) . '[]" value="' . $post_type . '" ' . checked( in_array( $post_type, $selected_types ), true, false ) . '>';
					echo '<label style="margin-left: 5px;">' . $obj->labels->name . '</label>';
					echo '</li>';
				}
				?>
			</ul>
		</p>
		<?php if ( $widget_id != '__i__' ) { ?>
			<!--
			<p>
				<span class="shortcodeTtitle"><?php _e( '短代码:', 'text_domain' ); ?></span>
				<span class="shortcode">[be-recentlyviewed widget_id="<?php echo $widget_id; ?>"]</span>
			</p>
			-->
			<?php
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['title']          = isset( $new_instance['title'] ) ? $new_instance['title'] : '';
		$instance['numberofposts']  = isset( $new_instance['numberofposts'] ) ? absint( $new_instance['numberofposts'] ) : 5;
		$instance['showthumbnail']  = isset( $new_instance['showthumbnail'] ) ? (bool) $new_instance['showthumbnail'] : false;
		$instance['clearcookie']    = isset( $new_instance['clearcookie'] ) ? (bool) $new_instance['clearcookie'] : false;
		$instance['show_time']      = isset( $new_instance['show_time'] ) ? (bool) $new_instance['show_time'] : false;
		$instance['selected_types'] = isset( $new_instance['selected_types'] ) && is_array( $new_instance['selected_types'] ) ? $new_instance['selected_types'] : array();

		return $instance;
	}
}

// 注册小部件
function register_recently_viewed() {
	register_widget( 'Be_recently_viewed' );
}
add_action( 'widgets_init', 'register_recently_viewed' );


// 操作cookie
add_action( 'template_redirect', 'be_posts_visited' );
add_action( 'wp_footer', 'be_cookie_path_js' );
function be_cookie_path_js() {
	?>
	<script>
	window.cookiePath = '<?php echo COOKIEPATH; ?>';
	</script>
	<?php
}

// 修改 cookie 设置函数
function be_posts_visited() {
	if ( is_single() || is_page() ) {
		$cooki      = 'astx_recent_posts';
		$current_id = get_the_ID();

		// 验证当前文章 ID
		if ( ! $current_id || ! get_post( $current_id ) ) {
			return;
		}

		$be_posts = array();
		if ( isset( $_COOKIE[ $cooki ] ) ) {
			$cookie_data = wp_unslash( $_COOKIE[ $cooki ] );
			// 验证是否为有效的 JSON 数据
			if ( is_string( $cookie_data ) && ! empty( $cookie_data ) ) {
				$decoded_data = json_decode( $cookie_data, true );
				if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded_data ) ) {
					// 验证数组中的每个元素
					$be_posts = array_filter(
						$decoded_data,
						function ( $post_id ) {
							return is_numeric( $post_id ) && $post_id > 0 && get_post( $post_id );
						}
					);
					$be_posts = array_values( $be_posts );
				}
			}
		}

		// 删除当前文章（如果存在）
		$be_posts = array_diff( $be_posts, array( $current_id ) );
		// 添加当前文章到开头
		array_unshift( $be_posts, $current_id );

		// 限制存储的文章数量，防止 cookie 过大
		$be_posts = array_slice( $be_posts, 0, 50 );

		// 设置 cookie，添加 httponly 标志增加安全性
		setcookie(
			$cooki,
			json_encode( $be_posts ),
			array(
				'expires'  => time() + ( DAY_IN_SECONDS * 31 ),
				'path'     => COOKIEPATH,
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'samesite' => 'Strict',
			)
		);
	}
}

// 添加 AJAX 处理函数
add_action( 'wp_ajax_clear_recently_viewed_cookie', 'clear_recently_viewed_cookie' );
add_action( 'wp_ajax_nopriv_clear_recently_viewed_cookie', 'clear_recently_viewed_cookie' );

function clear_recently_viewed_cookie() {
	setcookie( 'astx_recent_posts', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
	wp_send_json_success();
}
