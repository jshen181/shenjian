<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 关于本站
class about extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'about',
			'description' => '本站信息、RSS、微信、微博、QQ',
			'customize_selective_refresh' => true,
		);
		parent::__construct('about', '关于本站', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_social_icon'   => 1,
			'show_caption'       => 0,
			'show_inf'           => 1,
			'show_mixed'           => 0,
			'about_back'         => get_template_directory_uri() . '/img/default/options/user.jpg',
			'weixin'             => get_template_directory_uri() . '/img/favicon.png"',
			'about_img'          => get_template_directory_uri() . '/img/favicon.png"',
			'about_name'         => '网站名称',
			'about_the'          => '到小工具中更改此内容',
			'tsina'              => 'be be-stsina',
			'tsinaurl'           => '输入链接地址',
			'rss'                => 'be be-rss',
			'rssurl'             => home_url( '/' ) . 'feed/',
			'tqq'                => 'be be-qq',
			'tqqurl'             => '88888',
			'cqqurl'             => '',
		);
	}

	function enqueue_admin_scripts() {
		wp_enqueue_script( 'btn-widget-js', get_template_directory_uri() . '/inc/assets/js/addbtn.js', array( 'jquery', 'jquery-ui-sortable' ), null );
	}

	function widget($args, $instance) {
		extract($args);
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		echo $before_widget;
?>

<div id="feed_widget">
	<div class="feed-about">
		<?php if ( $instance[ 'show_mixed' ]  ) { ?>
			<div class="about-the-mixed">
				<div class="mixed-about-img"><img src="<?php echo $instance['about_img']; ?>" alt="name"/></div>
				<div class="mixed-about">
					<div class="about-name"><?php echo $instance['about_name']; ?></div>
					<span class="about-mixed-the"><?php echo $instance['about_the']; ?></span>
				</div>
			</div>
		<?php } else { ?>
			<?php if ( $instance[ 'about_back' ]  ) { ?>
				<div class="author-back" style="background-image: url('<?php echo $instance['about_back']; ?>');"></div>
			<?php } ?>
			<div class="about-main">
				<div class="about-img">
					<div class="about-img-box"><img src="<?php echo $instance['about_img']; ?>" alt="name"/></div>
				</div>
				<div class="clear"></div>
				<div class="about-name"><?php echo $instance['about_name']; ?></div>
				<div class="about-the<?php if ($instance['show_caption']) { ?> about-the-layout<?php } ?>"><?php echo $instance['about_the']; ?></div>
			</div>
		<?php } ?>
		<?php if ($instance['show_social_icon']) { ?>
			<div class="clear"></div>
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
											<div class="copy-success-weixin fd"><div class="copy-success-weixin-text"><span class="dashicons dashicons-saved"></span><?php _e( '已复制', 'begin' ); ?></div></div>
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

				<?php if ( $instance[ 'tqq' ] ) { ?>
					<?php if ( $instance[ 'cqqurl' ]  ) { ?>
						<div class="feed-t tqq"><a class="quoteqq" onclick="copyToClipboard(this)" title="<?php _e( '点击复制', 'begin' ); ?>" target=blank rel="external nofollow" href="<?php echo $instance['cqqurl']; ?>" target="_blank" rel="external nofollow"><i class="<?php echo $instance['tqq']; ?>"></i></a></div>
					<?php } else { ?>
						<div class="feed-t tqq"><a class="quoteqq" onclick="copyToClipboard(this)" title="<?php _e( '点击复制', 'begin' ); ?>" target=blank rel="external nofollow" href="https://wpa.qq.com/msgrd?V=3&uin=<?php echo $instance['tqqurl']; ?>&Site=QQ&Menu=yes"><i class="<?php echo $instance['tqq']; ?>"></i></a></div>
					<?php } ?>
				<?php } ?>

				<?php if ( $instance[ 'tsina' ] ) { ?>
					<div class="feed-t tsina"><a title="" href="<?php echo $instance['tsinaurl']; ?>" target="_blank" rel="external nofollow"><i class="<?php echo $instance['tsina']; ?>"></i></a></div>
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
		<?php } ?>
		<?php if ($instance['show_inf']) { ?>
			<div class="clear"></div>
			<div class="about-inf">
				<span class="about about-cn"><span><?php _e( '文章', 'begin' ); ?></span><?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?></span>
				<span class="about about-pn"><span><?php _e( '留言', 'begin' ); ?></span>
				<?php 
					$my_email = get_bloginfo ( 'admin_email' );
					global $wpdb;echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments where comment_author_email!='$my_email' And comment_author_email!=''");
				?>
				</span>
				<span class="about about-cn"><span><?php _e( '访客', 'begin' ); ?></span><?php echo all_view(); ?></span>
			</div>
		<?php } else { ?>
			<span class="social-clear"></span>
		<?php } ?>
	</div>
</div>

<?php
	echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = array();
		$instance = $old_instance;
		$instance = array();
		$instance['show_social_icon'] = !empty($new_instance['show_social_icon']) ? 1 : 0;
		$instance['show_caption'] = !empty($new_instance['show_caption']) ? 1 : 0;
		$instance['show_inf'] = !empty($new_instance['show_inf']) ? 1 : 0;
		$instance['show_mixed'] = !empty($new_instance['show_mixed']) ? 1 : 0;
		$instance['about_img'] = $new_instance['about_img'];
		$instance['about_name'] = $new_instance['about_name'];
		$instance['about_back'] = $new_instance['about_back'];
		$instance['about_the'] = $new_instance['about_the'];
		$instance['weixin'] = $new_instance['weixin'];
		$instance['tsina'] = $new_instance['tsina'];
		$instance['tsinaurl'] = $new_instance['tsinaurl'];
		$instance['rss'] = $new_instance['rss'];
		$instance['rssurl'] = $new_instance['rssurl'];
		$instance['tqq'] = $new_instance['tqq'];
		$instance['tqqurl'] = $new_instance['tqqurl'];
		$instance['cqqurl'] = $new_instance['cqqurl'];
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
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('weixin' => '' . get_template_directory_uri() . '/img/favicon.png"'));
		$weixin = $instance['weixin'];
		$instance = wp_parse_args((array) $instance, array('about_img' => '' . get_template_directory_uri() . '/img/favicon.png"'));
		$about_img = $instance['about_img'];
		$instance = wp_parse_args((array) $instance, array('about_name' => '网站名称'));
		$about_name = $instance['about_name'];
		$instance = wp_parse_args((array) $instance, array('about_back' => get_template_directory_uri() . '/img/default/options/user.jpg'));
		$about_back = $instance['about_back'];
		$instance = wp_parse_args((array) $instance, array('about_the' => '到小工具中更改此内容'));
		$about_the = $instance['about_the'];
		$instance = wp_parse_args((array) $instance, array('tsina' => 'be be-stsina'));
		$tsina = $instance['tsina'];
		$instance = wp_parse_args((array) $instance, array('tsinaurl' => '输入链接地址'));
		$tsinaurl = $instance['tsinaurl'];
		$instance = wp_parse_args((array) $instance, array('rss' => 'be be-rss'));
		$rss = $instance['rss'];
		$instance = wp_parse_args((array) $instance, array('rssurl' => home_url( '/' ) . 'feed/'));
		$rssurl = $instance['rssurl'];
		$instance = wp_parse_args((array) $instance, array('tqq' => 'be be-qq'));
		$tqq = $instance['tqq'];
		$instance = wp_parse_args((array) $instance, array('tqqurl' => '88888'));
		$tqqurl = $instance['tqqurl'];
		$instance = wp_parse_args((array) $instance, array('cqqurl' => ''));
		$cqqurl = $instance['cqqurl'];
?>

	<p>
		<label for="<?php echo $this->get_field_id('about_back'); ?>">背景图片：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'about_back' ); ?>" name="<?php echo $this->get_field_name( 'about_back' ); ?>" type="text" value="<?php echo $about_back; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	<p>
		<label for="<?php echo $this->get_field_id('about_img'); ?>">头像：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'about_img' ); ?>" name="<?php echo $this->get_field_name( 'about_img' ); ?>" type="text" value="<?php echo $about_img; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('about_name'); ?>">网站名称：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'about_name' ); ?>" name="<?php echo $this->get_field_name( 'about_name' ); ?>" type="text" value="<?php echo $about_name; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('about_the'); ?>">说明：
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id('about_the'); ?>" name="<?php echo $this->get_field_name('about_the'); ?>"><?php echo $about_the; ?></textarea>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id('show_caption') ); ?>" class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_caption') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_caption') ); ?>" <?php checked( (bool) $instance["show_caption"], true ); ?>>
			长说明
		</label>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id('show_mixed') ); ?>" class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_mixed') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_mixed') ); ?>" <?php checked( (bool) $instance["show_mixed"], true ); ?>>
			混排
		</label>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id('show_social_icon') ); ?>" class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_social_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_social_icon') ); ?>" <?php checked( (bool) $instance["show_social_icon"], true ); ?>>
			显示社交图标
		</label>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id('show_inf') ); ?>" class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_inf') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_inf') ); ?>" <?php checked( (bool) $instance["show_inf"], true ); ?>>
			显示站点信息
		</label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('weixin'); ?>">微信二维码（留空则不显示）：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'weixin' ); ?>" name="<?php echo $this->get_field_name( 'weixin' ); ?>" type="text" value="<?php echo $weixin; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
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
		<label for="<?php echo $this->get_field_id('cqqurl'); ?>">QQ号自定义链接：</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'cqqurl' ); ?>" name="<?php echo $this->get_field_name( 'cqqurl' ); ?>" type="text" value="<?php echo $cqqurl; ?>" />
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
function about_init() {
	register_widget( 'about' );
}
add_action( 'widgets_init', 'about_init' );