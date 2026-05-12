<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 关注我们
class feed_widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'feed_widget',
			'description' => 'RSS、微信、微博',
			'customize_selective_refresh' => true,
		);
		parent::__construct('feed', '关注我们', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'weixin'         => get_template_directory_uri() . '/img/favicon.png"',
			'tsina'          => 'be be-stsina',
			'tsinaurl'       => '输入链接地址',
			'tqq'            => 'be be-qq',
			'tqqurl'         => '88888',
			'rss'            => 'be be-rss',
			'rssurl'         => home_url( '/' ) . 'feed/',
			'title'          => '关注我们',
			'btn'            => array()
		);
	}

	function enqueue_admin_scripts() {
		wp_enqueue_script( 'btn-widget-js', get_template_directory_uri() . '/inc/assets/js/addbtn.js', array( 'jquery', 'jquery-ui-sortable' ), null );
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title_w = title_i_w();
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
?>

<div id="feed_widget">
	<div class="feed-rss">
		<?php if ( ! empty( $title ) ) { ?>
			<?php 
				if ( isset( $instance[ 'title' ] ) ) {
					$title = $instance[ 'title' ];
				} else {
					$title = '关注我们';
				}
			 ?>
			<h3 class="widget-title"><?php echo title_i_w(); ?><?php echo $title; ?></h3>
		<?php } ?>

		<div class="feed-about-box">
			<?php if ($instance['weixin']) { ?>
				<div class="feed-t weixin">
					<div class="weixin-b">
						<div class="weixin-qr fd">
							<div class="weixin-qr-about">
								<div class="copy-weixin">
									<img src="<?php echo $instance['weixin']; ?>" alt=" weixin"/>
									<div class="be-copy-text"><?php _e( '点击复制', 'begin' ); ?></div>
									<div class="weixinbox">
										<div class="btn-weixin-copy"></div>
										<div class="weixin-id"><?php echo zm_get_option( 'weixin_s_id' ); ?></div>
										<div class="copy-success-weixin fd"><div class="copy-success-weixin-text followus"><span class="dashicons dashicons-saved"></span><?php _e( '已复制', 'begin' ); ?></div></div>
									</div>
								</div>
								<div class="clear"></div>
							</div>
							<div class="arrow-down"></div>
						</div>
						<a><i class="be be-weixin"></i></a>
					</div>
				</div>
			<?php } ?>

			<?php if ( $instance[ 'tsina' ] ) { ?>
				<div class="feed-t tsina"><a title="" href="<?php echo $instance['tsinaurl']; ?>" target="_blank" rel="external nofollow"><i class="<?php echo $instance['tsina']; ?>"></i></a></div>
			<?php } ?>

			<?php if ( $instance[ 'tqq' ] ) { ?>
				<div class="feed-t tqq"><a class="quoteqq" href="https://wpa.qq.com/msgrd?V=3&uin=<?php echo $instance['tqqurl']; ?>&site=QQ&Menu=yes" onclick="copyToClipboard(this)" title="<?php _e( '点击复制', 'begin' ); ?>" target=blank rel="external nofollow"><i class="<?php echo $instance['tqq']; ?>"></i></a></div>
			<?php } ?>

			<?php if ( $instance[ 'rss' ] ) { ?>
				<div class="feed-t feed"><a title="" href="<?php echo $instance['rssurl']; ?>" target="_blank" rel="external nofollow"><i class="<?php echo $instance['rss']; ?>"></i></a></div>
			<?php } ?>

			<?php if ( ! empty( $instance['btn'] ) ): ?>
    			<?php $counter = 1; ?>
				<?php foreach ( $instance['btn'] as $item ) : ?>
					<div class="feed-t feed-btn feed-btn-<?php echo esc_html( $counter ); ?>">
						<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="external nofollow">
							<?php if ( $item['icon'] ) { ?><i class="<?php echo esc_html( $item['icon'] ); ?>"></i><?php } ?>
						</a>
					</div>
					<?php $counter++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
	</div>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['weixin']   = $new_instance['weixin'];
		$instance['tsina']    = $new_instance['tsina'];
		$instance['tsinaurl'] = $new_instance['tsinaurl'];
		$instance['tqq']      = $new_instance['tqq'];
		$instance['tqqurl']   = $new_instance['tqqurl'];
		$instance['rss']      = $new_instance['rss'];
		$instance['rssurl']   = $new_instance['rssurl'];
		$instance['btn']      = array();
		if ( ! empty( $new_instance['btn_icon'] ) ) {
			$protocols = wp_allowed_protocols();
			$protocols[] = 'skype';
			for ( $i=0; $i < ( count( $new_instance['btn_icon'] ) - 1 ); $i++ ) {
				$temp = array( 'icon' => $new_instance['btn_icon'][$i], 'url' => esc_url( $new_instance['btn_url'][$i], $protocols ) );
				$instance['btn'][] = $temp;
			}
		}
		return $instance;
	}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '关注我们';
		}
		global $wpdb;
		$instance = wp_parse_args( ( array ) $instance, array( 'weixin' => '' . get_template_directory_uri() . '/img/favicon.png"' ) );
		$weixin = $instance['weixin'];
		$instance = wp_parse_args( ( array ) $instance, array( 'tsina' => 'be be-stsina' ) );
		$tsina = $instance['tsina'];
		$instance = wp_parse_args( ( array ) $instance, array( 'tsinaurl' => '输入链接地址' ) );
		$tsinaurl = $instance['tsinaurl'];
		$instance = wp_parse_args( ( array ) $instance, array( 'tqq' => 'be be-qq' ) );
		$tqq = $instance['tqq'];
		$instance = wp_parse_args( ( array ) $instance, array( 'tqqurl' => '88888' ) );
		$tqqurl = $instance['tqqurl'];
		$instance = wp_parse_args( ( array ) $instance, array( 'rss' => 'be be-rss' ) );
		$rss = $instance['rss'];
		$instance = wp_parse_args( ( array ) $instance, array( 'rssurl' => home_url( '/' ) . 'feed/' ) );
		$rssurl = $instance['rssurl'];
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('weixin'); ?>">微信二维码（留空则不显示）：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'weixin' ); ?>" name="<?php echo $this->get_field_name( 'weixin' ); ?>" type="text" value="<?php echo $weixin; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('tsina'); ?>">新浪微博图标（留空则不显示）：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'tsina' ); ?>" name="<?php echo $this->get_field_name( 'tsina' ); ?>" type="text" value="<?php echo $tsina; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('tsina'); ?>">新浪微博地址：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'tsinaurl' ); ?>" name="<?php echo $this->get_field_name( 'tsinaurl' ); ?>" type="text" value="<?php echo $tsinaurl; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('tqq'); ?>">QQ图标（留空则不显示）：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'tqq' ); ?>" name="<?php echo $this->get_field_name( 'tqq' ); ?>" type="text" value="<?php echo $tqq; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('tqqurl'); ?>">QQ号：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'tqqurl' ); ?>" name="<?php echo $this->get_field_name( 'tqqurl' ); ?>" type="text" value="<?php echo $tqqurl; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('rss'); ?>">订阅图标（留空则不显示）：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" type="text" value="<?php echo $rss; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('rss'); ?>">订阅地址：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'rssurl' ); ?>" name="<?php echo $this->get_field_name( 'rssurl' ); ?>" type="text" value="<?php echo $rssurl; ?>" />
	</p>

		<ul class="btn_container btn-sortable">
			<?php if ( ! empty( $instance['btn'] ) ): ?>
				<?php foreach ( $instance['btn'] as $link ) : ?>
					<li>
						<?php $this->draw_btn( $this, $link ); ?>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>

		<p>
			<a href="#" class="add_icobtn button">添加更多</a>
		</p>

		<div class="icobtn_clone" style="display:none">
			<?php $this->draw_btn( $this ); ?>
		</div>
	<?php }

	function draw_btn( $widget, $selected = array( 'icon' => '', 'url' => '' ) ) { ?>
		<label class="btn-sw-icon">图标代码</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_icon' ) ); ?>[]" value="<?php echo esc_attr( $selected['icon'] ); ?>" style="width: 100%;margin: 5px 0;" />
		<label class="btn-sw-icon">链接地址</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_url' ) ); ?>[]" value="<?php echo esc_attr( $selected['url'] ); ?>" style="width: 100%;margin: 5px 0;" />
		<span class="remove-icobtn dashicons dashicons-no-alt"></span>

	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function feed_init() {
	register_widget( 'feed_widget' );
}
add_action( 'widgets_init', 'feed_init' );