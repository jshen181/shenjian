<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 读者墙
class readers extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'readers',
			'description' => '最活跃的读者',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'readers', '读者墙', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title_z'       => '',
			'show_icon'     => '',
			'show_svg'      => '',
			'number'        => 6,
			'days'          => 90,
			'exclude'       => 1,
			'title'         => '读者墙',
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
		$number = strip_tags( $instance['number']) ? absint( $instance['number'] ) : 6;
		$days = strip_tags( $instance['days']) ? absint( $instance['days'] ) : 90;
		$exclude = strip_tags( $instance['exclude'] ) ? absint( $instance['exclude'] ) : 1;
?>
<?php if ($instance['show_icon']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
<?php } ?>
<?php if ($instance['show_svg']) { ?>
	<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
<?php } ?>
<div id="readers_widget" class="readers">
	<?php
		global $wpdb;
		$exclude_id = "'" . implode( "','", explode( ',', $instance['exclude'] ) ) . "'";
		$counts = wp_cache_get( 'mostactive' );
		if ( false === $counts ) {
			$counts = $wpdb->get_results("
				SELECT COUNT(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email
				FROM {$wpdb->prefix}comments
				WHERE comment_date > date_sub( NOW(), INTERVAL $days DAY )
				AND comment_approved = '1'
				AND comment_author_email != 'example@example.com'
				AND comment_author_email != ''
				AND comment_type not in ('trackback','pingback')
				 " . ( ! empty( $instance['exclude'] ) ? "AND user_id NOT IN ( $exclude_id )" : "" ) . "
				GROUP BY comment_author_email
				ORDER BY cnt DESC
				LIMIT $number
			");
		}
		$mostactive = '';
		if ( $counts ) {
			wp_cache_set( 'mostactive', $counts );
			foreach ( $counts as $count ) {
				$confirm_url = esc_url( get_permalink( zm_get_option( 'comment_link_go_id' ) ) );
				$count_url = esc_url( $count->comment_author_url );

				if ( empty( $count_url ) || ! filter_var( $count_url, FILTER_VALIDATE_URL ) ) {
					$url = '';
				} else {
					$url = zm_get_option( 'comment_link_go' ) ? $confirm_url . '?target=' . esc_url( $count_url ) : esc_url( $count_url );
				}

				if ( zm_get_option( 'cache_avatar' ) ) {
					$mostactive .= '<div class="readers-avatar load"><span class="dzq"><a href="' . $url . '" target="_blank" rel="external nofollow">' . begin_avatar($count->comment_author_email, 96, '', $count->comment_author) . '</a></span><span class="readers-inf"><strong>' . $count->comment_author . ' </strong>' . $count->cnt . '个脚印</span></div>';
				} else {
					if ( !zm_get_option( 'avatar_load' ) ) {
						$mostactive .= '<div class="readers-avatar"><span class="dzq"><a href="' . $url . '" target="_blank" rel="external nofollow">' . get_avatar($count->comment_author_email, 96, '', $count->comment_author) . '</a></span><span class="readers-inf"><strong>' . $count->comment_author . ' </strong>' . $count->cnt . '个脚印</span></div>';
					} else {
						$mostactive .= '<div class="readers-avatar load"><span class="dzq"><a href="' . $url . '" target="_blank" rel="external nofollow"><img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. $count->comment_author .'"  width="96" height="96" data-original="' . preg_replace(array('/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i'), array('', ''), get_avatar( $count->comment_author_email, $size = 96, '', $count->comment_author )) . '" /></a></span><span class="readers-inf"><strong>' . $count->comment_author . ' </strong>' . $count->cnt . '个脚印</span></div>';
					}
				}
			}
			echo $mostactive;
		}
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
		$instance['days'] = strip_tags($new_instance['days']);
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '读者墙';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '6'));
		$instance = wp_parse_args((array) $instance, array('days' => '90'));
		$instance = wp_parse_args((array) $instance, array('exclude' => '1'));
		$number = strip_tags($instance['number']);
		$days = strip_tags($instance['days']);
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
	<p style="display: flex;column-gap: 10px;">
		<label for="<?php echo $this->get_field_id( 'number' ); ?>" style="width: 50%;">显示数量：
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</label>
		<label for="<?php echo $this->get_field_id( 'days' ); ?>" style="width: 50%;">时间限定（天）：
			<input class="number-text-be-d" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="number" step="1" min="1" value="<?php echo $days; ?>" size="3" />
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('exclude'); ?>">排除的用户ID：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $instance['exclude']; ?>" />
		
	</p>

	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

function readers_init() {
	register_widget( 'readers' );
}
add_action( 'widgets_init', 'readers_init' );