<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// RSS
class be_widget_rss extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_widget_rss',
			'description' => '调用任意站点RSS和feed文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_widget_rss', 'RSS聚合', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs'=> 1,
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'show_time'   => 1,
			'feed'        => '',
			'numposts'    => 5,
			'title'       => 'RSS聚合',
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
?>

<div class="<?php echo $instance['show_thumbs'] ? 'new_cat rss-new' : 'post_cat rss-new-title'; ?>">
	<?php if ( $instance['show_icon'] ) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php
		include_once( ABSPATH . WPINC . '/feed.php' );
		$rss = fetch_feed( $instance['feed'] );
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) ) :
			$maxitems = $rss->get_item_quantity( $instance['numposts'] );
			$rss_items = $rss->get_items( 0, $maxitems );
		endif;
	?>
	<ul>
		<?php if ( $maxitems == 0 ) : ?>
			<li class="srm"><?php _e( '暂无文章', 'begin' ); ?></li>
		<?php else : ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<?php if ( $instance['show_thumbs'] ) { ?>
		  			<li>
						<span class="thumbnail">
							<?php echo rss_thumbnail( $item->get_content(), $item->get_permalink() ); ?>
						</span>
						<span class="new-title">
							<a href="<?php echo esc_url( $item->get_permalink() ); ?>" rel="external nofollow" target="_blank">
								<?php echo esc_html( $item->get_title() ); ?>
							</a>
						</span>
						<span class="date"><time datetime="<?php echo esc_html( date( 'Y-m-d H:i:s', strtotime( $item->get_date() ) ) ); ?>"><?php echo esc_html( date( 'm/d', strtotime( $item->get_date() ) ) ); ?></time></span>
					</li>
				<?php } else { ?>
					<li class="only-title<?php if ( $instance['show_time'] ) { ?> only-title-date<?php } ?>">
					<?php if ( $instance['show_time'] ) { ?><span class="date only-date"><time datetime="<?php echo esc_html( date( 'Y-m-d H:i:s', strtotime( $item->get_date() ) ) ); ?>"><?php echo esc_html( date( 'm/d', strtotime( $item->get_date() ) ) ); ?></time></span><?php } ?>
						<a class="srm get-icon" href="<?php echo esc_url( $item->get_permalink() ); ?>" rel="external nofollow" target="_blank">
							<?php echo esc_html( $item->get_title() ); ?>
						</a>
					</li>
				<?php } ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['show_thumbs'] = ! empty( $new_instance['show_thumbs'] ) ? 1 : 0;
		$instance['title_z'] = strip_tags($new_instance['title_z']);
		$instance['show_icon'] = strip_tags($new_instance['show_icon']);
		$instance['show_svg'] = strip_tags($new_instance['show_svg']);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['show_time']   = ! empty( $new_instance['show_time'] ) ? 1 : 0;
		$instance['feed'] = strip_tags( $new_instance['feed'] );
		$instance['numposts'] = $new_instance['numposts'];
		return $instance;
	}
	function form($instance) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = 'RSS聚合';
		}
		global $wpdb;
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
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
		<label for="<?php echo $this->get_field_id( 'feed' ); ?>">输入RSS feed URL：</label>
		<input class="widefat" id="<?php echo $this->get_field_id('feed'); ?>" name="<?php echo $this->get_field_name('feed'); ?>" type="text" value="<?php echo $instance['feed']; ?>" />
	</p>
	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>">显示缩略图
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbs' ) ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
		</label>
	</p>
	<p>
		<label class="alignleft" style="display: block; width: 50%; margin-bottom: 15px;" for="<?php echo esc_attr( $this->get_field_id('show_time') ); ?>">无缩略图显示时间
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_time' ) ); ?>" <?php checked( (bool) $instance["show_time"], true ); ?>>
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
		<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
	</p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

add_action( 'widgets_init', 'be_widget_rss_init' );
function be_widget_rss_init() {
	register_widget( 'be_widget_rss' );
}