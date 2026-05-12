<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 评论置顶
class be_sticky_comments_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be-attachment-inf',
			'description' => '显示置顶的评论',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_sticky_comments_widget', '评论置顶', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'ellipsis'   => 0,
			'title_z'    => '',
			'show_icon'  => '',
			'show_svg'   => '',
			'number'     => 5,
			'title'      => '评论置顶',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		// 查询
		global $post;
		$query_args = apply_filters( 'sticky_comments_query', array(
			'number'      => $instance['number'],
			'status'      => 'approve',
			'post_status' => 'publish',
			'no_found_rows'       => true,
			'meta_query'  => array(
				array(
					'key'    => 'comment_sticky',
					'value'  => '1'
				)
			)
		) );

		$query    = new WP_Comment_Query;
		$comments = $query->query( $query_args );
		if ( $comments ) :

		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		?>

		<div id="message" class="message-widget gaimg<?php if ( ! $instance['ellipsis'] ) { ?> message-item<?php } ?>">
			<?php if ( $instance['show_icon'] ) { ?>
				<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
			<?php } ?>
			<?php if ( $instance['show_svg'] ) { ?>
				<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
			<?php } ?>
			<?php
				$output = '<ul id="sticky-comments-widget">';
				if ( $comments ) {
					foreach ( $comments as $comment ) {
						$output .= '<li class="sticky-comments-item load"><a href="' . get_permalink( $comment->comment_post_ID ) . '#anchor-comment-' . $comment->comment_ID . '" title="' . sprintf(__( '发表在', 'begin' )) . '：' . get_the_title( $comment->comment_post_ID ) . '" rel="external nofollow" ' . goal() . '>';
						if ( ! zm_get_option( 'avatar_load' ) ) {
							$output .= get_avatar( $comment->comment_author_email, '96', '', get_comment_author( $comment->comment_ID ) );
						} else {
							$output .= '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="' . get_comment_author( $comment->comment_ID ) . '" width="96" height="96" data-original="' . preg_replace( array( '/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i' ), array( '', '' ), get_avatar( $comment->comment_author_email, '96' ) ) . '" />';
						}
						$output .= '<span class="comment_author">';
						$output .= get_comment_author( $comment->comment_ID );
						$output .= '</span>';
						$output .= '<span class="say-comment">';
						$output .= convert_smilies( $comment->comment_content );
						$output .= '</span>';
						$output .= '</a></li>';
					}
				 }
				$output .= '</ul>';
				echo $output;
				endif;
			?>
		</div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['ellipsis'] = !empty( $new_instance['ellipsis'] ) ? 1 : 0;
		$instance['title_z'] = strip_tags( $new_instance['title_z'] );
		$instance['show_icon'] = strip_tags( $new_instance['show_icon'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '评论置顶';
		}
		$instance = wp_parse_args( ( array ) $instance, array( 'number' => '5' ) );
		$number = strip_tags( $instance['number'] );
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p>
		<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
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
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'ellipsis' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ellipsis' ) ); ?>" <?php checked( ( bool ) $instance["ellipsis"], true ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'ellipsis' ) ); ?>">简化样式</label>
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'be_sticky_comments_widget_init' );
function be_sticky_comments_widget_init() {
	register_widget( 'be_sticky_comments_widget' );
}