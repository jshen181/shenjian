<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 留言板
class be_pages_recent_comments extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_pages_recent_comments',
			'description' => '调用指定文章/页面留言',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_pages_recent_comments', '留言板', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'pages_id'      => '',
			'number'        => 5,
			'authornot'     => 1,
			'title'         => '留言板',
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
		$authornot = strip_tags($instance['authornot']) ? absint( $instance['authornot'] ) : 1;
?>

<div id="message" class="message-widget gaimg">
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>

	<ul>
		<?php 
		$no_comments = false;
		$avatar_size = 64;
		$comments_query = new WP_Comment_Query();
		$comments = $comments_query->query( array_merge( array( 'number' => $number, 'status' => 'approve', 'type' => 'comments', 'post_status' => 'publish', 'post_id' => $instance["pages_id"], 'author__not_in' => explode(',',$instance["authornot"]) ) ) );
		if ( $comments ) : foreach ( $comments as $comment ) : ?>

		<li>
			<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#anchor-comment-<?php echo $comment->comment_ID; ?>" title="<?php _e( '发表在', 'begin' ); ?>：<?php echo get_the_title($comment->comment_post_ID); ?>" rel="external nofollow" <?php echo goal(); ?>>
				<?php if (zm_get_option('cache_avatar')) { ?>
					<?php echo begin_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) ); ?>
				<?php } else { ?>
					<?php echo get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) ); ?>
				<?php } ?>
				<span class="comment_author"><strong><?php echo get_comment_author( $comment->comment_ID ); ?></strong></span>
				<?php echo convert_smilies($comment->comment_content); ?>
			</a>
		</li>

		<?php endforeach; else : ?>
			<li><?php _e('暂无留言', 'begin'); ?></li>
			<?php $no_comments = true;
		endif; ?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['pages_id'] = $new_instance['pages_id'];
		$instance['authornot'] = strip_tags($new_instance['authornot']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '留言板';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
		$instance = wp_parse_args((array) $instance, array('authornot' => '1'));
		$authornot = strip_tags($instance['authornot']);
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('title_z'); ?>">图标标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title_z'); ?>" name="<?php echo $this->get_field_name('title_z'); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
	</p>
	<p class="layoutflex">
		<label for="<?php echo $this->get_field_id('show_icon'); ?>">单色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_icon'); ?>" name="<?php echo $this->get_field_name('show_icon'); ?>" type="text" value="<?php echo $instance['show_icon']; ?>" />
		</label>
		<label for="<?php echo $this->get_field_id('show_svg'); ?>">彩色图标代码：
			<input class="widefat" id="<?php echo $this->get_field_id('show_svg'); ?>" name="<?php echo $this->get_field_name('show_svg'); ?>" type="text" value="<?php echo $instance['show_svg']; ?>" />
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('pages_id'); ?>">输入文章/页面ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('pages_id'); ?>" name="<?php echo $this->get_field_name('pages_id'); ?>" type="text" value="<?php echo $instance['pages_id']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('authornot'); ?>">排除的用户ID：</label>
		<p><input id="<?php echo $this->get_field_id( 'authornot' ); ?>" name="<?php echo $this->get_field_name( 'authornot' ); ?>" type="text" value="<?php echo $authornot; ?>" /></p>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

function be_pages_recent_comments_init() {
	register_widget( 'be_pages_recent_comments' );
}
add_action( 'widgets_init', 'be_pages_recent_comments_init' );