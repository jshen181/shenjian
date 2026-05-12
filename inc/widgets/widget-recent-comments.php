<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 近期留言
class recent_comments extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'recent_comments',
			'description'                 => '带头像的近期留言',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'recent_comments', '近期留言', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'ellipsis'  => 1,
			'title_z'   => '',
			'show_icon' => '',
			'show_svg'  => '',
			'number'    => 5,
			'authornot' => 1,
			'title'     => '近期留言',
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
		$number    = strip_tags( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$authornot = strip_tags( $instance['authornot'] ) ? absint( $instance['authornot'] ) : 1;

		echo '<div id="message" class="message-widget gaimg' . ( ! $instance['ellipsis'] ? ' message-item' : '' ) . '">';

		if ( $instance['show_icon'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'] . '</h3>';
		}

		if ( $instance['show_svg'] ) {
			echo '<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#' . $instance['show_svg'] . '"></use></svg>' . $instance['title_z'] . '</h3>';
		}

		echo '<ul>';

		$no_comments    = false;
		$avatar_size    = 96;
		$comments_query = new WP_Comment_Query();

		if ( $instance['authornot'] ) {
			$comments = $comments_query->query(
				array_merge(
					array(
						'number'         => $number,
						'status'         => 'approve',
						'type'           => 'comments',
						'post_status'    => 'publish',
						'author__not_in' => explode( ',', $instance['authornot'] ),
					)
				)
			);
		} else {
			$comments = $comments_query->query(
				array_merge(
					array(
						'number'      => $number,
						'status'      => 'approve',
						'type'        => 'comments',
						'post_status' => 'publish',
					)
				)
			);
		}

		if ( $comments ) {
			foreach ( $comments as $comment ) {
				echo '<li class="message-item-li load">';
				echo '<a class="commentanchor" href="' . get_permalink( $comment->comment_post_ID ) . '#anchor-comment-' . $comment->comment_ID . '" title="' . __( '发表在', 'begin' ) . '：' . get_the_title( $comment->comment_post_ID ) . '" rel="external nofollow" ' . goal() . '>';

				if ( get_option( 'show_avatars' ) ) {
					if ( zm_get_option( 'cache_avatar' ) ) {
						echo begin_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) );
					} elseif ( ! zm_get_option( 'avatar_load' ) ) {
							echo get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) );
					} else {
						echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="' . get_comment_author( $comment->comment_ID ) . '"  width="30" height="30" data-original="' . preg_replace( array( '/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i' ), array( '', '' ), get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) ) ) . '" />';
					}
				}

				if ( zm_get_option( 'comment_vip' ) ) {
					$authoremail = get_comment_author_email( $comment );
					if ( email_exists( $authoremail ) ) {
						$commet_user_role  = get_user_by( 'email', $authoremail );
						$comment_user_role = $commet_user_role->roles[0];
						if ( $comment_user_role !== zm_get_option( 'roles_vip' ) ) {
							echo '<span class="comment_author">' . get_comment_author( $comment->comment_ID ) . '</span>';
						} else {
							echo '<span class="comment_author message-widget-vip">' . get_comment_author( $comment->comment_ID ) . '</span>';
						}
					} else {
						echo '<span class="comment_author">' . get_comment_author( $comment->comment_ID ) . '</span>';
					}
				} else {
					echo '<span class="comment_author">' . get_comment_author( $comment->comment_ID ) . '</span>';
				}

				echo '<span class="say-comment">' . convert_smilies( $comment->comment_content ) . '</span>';
				echo '</a></li>';
			}
		} else {
			echo '<li>' . __( '暂无留言', 'begin' ) . '</li>';
			$no_comments = true;
		}

		echo '</ul></div>';
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance              = array();
		$instance['title_z']   = strip_tags( $new_instance['title_z'] );
		$instance['ellipsis']  = ! empty( $new_instance['ellipsis'] ) ? 1 : 0;
		$instance['show_icon'] = strip_tags( $new_instance['show_icon'] );
		$instance['show_svg']  = strip_tags( $new_instance['show_svg'] );
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['number']    = strip_tags( $new_instance['number'] );
		$instance['authornot'] = strip_tags( $new_instance['authornot'] );
		return $instance;
	}
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '近期留言';
		}
		global $wpdb;
		$instance  = wp_parse_args( (array) $instance, array( 'number' => '5' ) );
		$number    = strip_tags( $instance['number'] );
		$instance  = wp_parse_args( (array) $instance, array( 'authornot' => '1' ) );
		$authornot = strip_tags( $instance['authornot'] );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
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
		<p style="display: flex;column-gap: 10px;">
			<label for="<?php echo $this->get_field_id( 'authornot' ); ?>" style="width: 50%;">排除管理员ID：
				<input id="<?php echo $this->get_field_id( 'authornot' ); ?>" style="width: 100%;" name="<?php echo $this->get_field_name( 'authornot' ); ?>" type="text" value="<?php echo $authornot; ?>" />
			</label>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>" style="width: 50%;">显示数量：
				<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" style="width: 100%;" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
			</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'ellipsis' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ellipsis' ) ); ?>" <?php checked( (bool) $instance['ellipsis'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ellipsis' ) ); ?>">简化样式</label>
		</p>

		<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}

function recent_comments_init() {
	register_widget( 'recent_comments' );
}
add_action( 'widgets_init', 'recent_comments_init' );
