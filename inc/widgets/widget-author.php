<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 作者墙
class Be_Author_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'author_widget',
			'作者墙',
			array(
				'classname' => 'author_widget',
				'description' => '显示所有作者头像',
				'customize_selective_refresh' => true,
			)
		);
	}

	public function zm_defaults() {
		return array(
			'exclude_author'  => '',
			'number'          => '8',
			'title'           => '作者墙',
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title_w . $title . $after_title; 
		}
	?>
	<div class="author_widget_box">
		<?php 

			$authors = get_users(
				array(
					'orderby' => 'post_count',
					'order'   => 'DESC',
					'number'  => $instance['number'],
					'exclude' => explode( ',', $instance['exclude_author'] )
				)
			);

			foreach ( $authors as $author ) { 
		?>

			<?php if ( count_user_posts( $author->ID ) > 0 ) { ?>
				<ul class="xl9">
					<li class="author_box">
						<a href="<?php echo get_author_posts_url( $author->ID ); ?>" target="_blank">
							<span class="load">
								<?php
									if ( zm_get_option('cache_avatar' ) ) {
										echo begin_avatar( $author->user_email, $size = 96, '', $author->display_name );
									} else {
										if ( !zm_get_option( 'avatar_load' ) ) {
											echo get_avatar( $author->user_email, $size = 96, '', $author->display_name );
										} else {
											echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="' . $author->display_name . '"  width="96" height="96" data-original="' . preg_replace( array('/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i'), array('', ''), get_avatar( $author->user_email, $size = 96, '', $author->display_name ) ) . '" />';
										}
									}
								?>
							</span>
							<span class="clear"></span>
							<?php echo $author->display_name; ?>
							<span class="author-count"><?php echo count_user_posts( $author->ID ); ?> <?php _e( '篇', 'begin' ); ?></span>
						</a>
					</li>
				</ul>
			<?php } ?>
		<?php } ?>
		<div class="clear"></div>
	</div>

	<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['exclude_author'] = $new_instance['exclude_author'];
		$instance['number'] = $new_instance['number'];
		return $instance;
	}

	public function form( $instance ) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['number']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_author' ); ?>">排除的作者ID，多个ID用半角英文逗号隔开：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'exclude_author' ); ?>" name="<?php echo $this->get_field_name( 'exclude_author' ); ?>" type="text" value="<?php echo $instance['exclude_author']; ?>" />
		</p>
	<?php }
}

function author_widget_init() {
	register_widget( 'Be_Author_Widget' );
}
add_action( 'widgets_init', 'author_widget_init' );