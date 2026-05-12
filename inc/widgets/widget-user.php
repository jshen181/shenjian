<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 用户墙
class Be_Registered_user_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'readers',
			'description' => '最近注册的用户',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'Be_Registered_user_Widget', '用户墙', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title_z'  => '',
			'show_icon'=> '',
			'show_svg' => '',
			'number'   => 12,
			'exclude'  => 1,
			'title'    => '用户墙',
			'orderby'  => 'DESC',
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title;
		$number = strip_tags( $instance['number']) ? absint( $instance['number'] ) : 12;
		$exclude = strip_tags( $instance['exclude'] ) ? absint( $instance['exclude'] ) : 1;
		$orderby = strip_tags($instance['orderby']) ? absint( $instance['orderby'] ) : DESC;
?>
<?php if ($instance['show_icon']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
<?php } ?>
<?php if ($instance['show_svg']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
<?php } ?>
<div id="readers_widget" class="readers">
<?php

	$args = array(
		'orderby' => 'registered',
		'order'   => $instance['orderby'],
		'number'  => $number,
		'exclude' => explode( ',', $instance['exclude'] ),
	);

	$latest_users = get_users( $args );
	$mostactive = '';
	foreach ( $latest_users as $user ) {
		if ( ! zm_get_option( 'avatar_load' ) ) {
			$mostactive .= '<div class="readers-avatar">
			<span class="dzq">
			<a href="' . get_author_posts_url( $user->ID ) . '" target="_blank" rel="external nofollow">' . get_avatar( $user->ID, '96', '', get_the_author_meta( 'nickname', $user->ID ) ) . '</a>
			</span>
			<span class="readers-inf"><strong>' . get_the_author_meta( 'nickname', $user->ID ) . ' </strong></span></div>';
		} else {
			$mostactive .= '<div class="readers-avatar load">
			<span class="dzq">
			<a href="' . get_author_posts_url( $user->ID ) . '" target="_blank" rel="external nofollow"><img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. get_the_author_meta( 'nickname', $user->ID ) .'" width="96" height="96" data-original="' . preg_replace( array( '/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i' ), array( '', '' ), get_avatar( $user->ID, 96, '', get_the_author_meta( 'nickname', $user->ID ) ) ) . '" /></a>
			</span>
			<span class="readers-inf"><strong>' . get_the_author_meta( 'nickname', $user->ID ) . ' </strong></span></div>';
		}
	}
	echo $mostactive;
?>
<div class="clear"></div>
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
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$instance['orderby'] = strip_tags($new_instance['orderby']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '用户墙';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '12'));
		$instance = wp_parse_args((array) $instance, array('exclude' => '1'));
		$number = strip_tags($instance['number']);
		$instance = wp_parse_args((array) $instance, array('orderby' => 'DESC'));
		$orderby = strip_tags($instance['orderby']);
 ?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
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
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('exclude'); ?>">排除管理员及他用户ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $instance['exclude']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('orderby'); ?>">排序：</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" class="widefat">
			<option value="DESC"<?php selected( $instance['orderby'], 'DESC' ); ?>>新的在上面</option>
			<option value="ASC"<?php selected( $instance['orderby'], 'ASC' ); ?>>旧的在上面</option>
		</select>
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

function Be_Registered_user_Widget_init() {
	register_widget( 'Be_Registered_user_Widget' );
}
add_action( 'widgets_init', 'Be_Registered_user_Widget_init' );
