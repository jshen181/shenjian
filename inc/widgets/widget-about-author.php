<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 关于作者小工具
class about_author extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'about_author',
			'description'                 => '只显示在正文和作者页面',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'about_author', '关于作者', $widget_ops );
	}

	// 默认配置
	public function zm_defaults() {
		return array(
			'show_inf'     => 1,
			'show_des'     => 0,
			'show_meta'    => 1,
			'show_posts'   => 1,
			'show_comment' => 1,
			'show_fans'    => 1,
			'show_views'   => 1,
			'show_like'    => 1,
			'show_role'    => 1,
			'line_clamp'   => 1,
			'author_back'  => get_template_directory_uri() . '/img/default/options/user.jpg',
		);
	}

	// 小工具前端显示
	function widget( $args, $instance ) {
		// 获取变量
		extract( $args );
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		// 只在作者页面和单篇文章页面显示
		if ( ! is_author() && ! is_single() ) {
			return;
		}

		// 获取作者数据
		global $wpdb;
		$author_id = absint( get_the_author_meta( 'ID' ) );
		if ( empty( $author_id ) ) {
			return;
		}

		// 安全的数据库查询
		$comment_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $wpdb->comments 
            WHERE comment_approved='1' 
            AND user_id = %d 
            AND comment_type NOT IN ('trackback','pingback')",
				$author_id
			)
		);
		$comment_count = $comment_count ?? 0;

		// 获取作者信息
		$author_url          = get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename' ) );
		$author_display_name = get_the_author_meta( 'display_name' ) ?? '';
		$author_last_name    = get_the_author_meta( 'last_name' ) ?? '';
		$author_description  = get_the_author_meta( 'description' ) ?? '';
		$author_email        = get_the_author_meta( 'user_email' ) ?? '';
		$author_weixin       = get_the_author_meta( 'weixin' ) ?? '';
		$author_weixinqr     = get_the_author_meta( 'weixinqr' ) ?? '';
		$author_phone        = get_the_author_meta( 'phone' ) ?? '';
		$author_qq           = get_the_author_meta( 'qq' ) ?? '';

		echo $before_widget;

		// 渲染HTML
		$this->render_widget_html(
			$instance,
			$author_id,
			$comment_count,
			array(
				'author_url'          => $author_url,
				'author_display_name' => $author_display_name,
				'author_last_name'    => $author_last_name,
				'author_description'  => $author_description,
				'author_email'        => $author_email,
				'author_weixin'       => $author_weixin,
				'author_weixinqr'     => $author_weixinqr,
				'author_phone'        => $author_phone,
				'author_qq'           => $author_qq,
			)
		);

		echo $after_widget;
	}

	// 渲染小工具HTML
	private function render_widget_html( $instance, $author_id, $comment_count, $author_data ) {
		?>
		<div id="about_author_widget">
			<a class="author-the-url" href="<?php echo esc_url( $author_data['author_url'] ); ?>"></a>
			<div class="author-meta-box">
				<?php if ( ! empty( $instance['author_back'] ) ) : ?>
					<div class="author-back" style="background-image: url('<?php echo esc_url( $instance['author_back'] ); ?>');"></div>
				<?php endif; ?>
				
				<div class="author-meta">
					<?php $this->render_author_avatar( $author_data['author_email'], $author_data['author_display_name'] ); ?>
					
					<h4 class="author-the">
						<?php
						if ( get_bloginfo( 'language' ) === 'en-US' && ! empty( $author_data['author_last_name'] ) ) {
							echo esc_html( $author_data['author_last_name'] );
						} else {
							echo esc_html( $author_data['author_display_name'] );
						}
						?>
					</h4>
					
					<?php if ( $instance['show_role'] ) : ?>
						<div class="show-the-role">
							<div class="the-role the-role1 ease"><?php echo esc_html( get_author_roles() ); ?></div>
						</div>
					<?php endif; ?>
					
					<?php if ( zm_get_option( 'follow_btn' ) ) : ?>
						<div class="follow-btn"><?php be_follow_btn( 'main' ); ?></div>
					<?php endif; ?>
					
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
				
				<div class="author-th">
					<?php $this->render_author_description( $instance, $author_data['author_description'] ); ?>
					<?php $this->render_author_contact( $instance, $author_data ); ?>
					<?php $this->render_author_stats( $instance, $author_id, $comment_count ); ?>
					
					<div class="author-m">
						<span class="author-more-ico"><i class="be be-more"></i></span>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	// 渲染作者头像
	private function render_author_avatar( $author_email, $author_name ) {
		if ( get_option( 'show_avatars' ) ) :
			?>
			<div class="author-avatar">
				<div class="author-avatar-box load">
					<?php if ( zm_get_option( 'cache_avatar' ) ) : ?>
						<?php echo begin_avatar( $author_email, 96, '', $author_name ); ?>
					<?php else : ?>
						<?php be_avatar_author(); ?>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
			</div>
		<?php else : ?>
			<div class="author-avatar-place"><i class="be be-timerauto"></i></div>
			<?php
		endif;
	}

	// 渲染作者描述
	private function render_author_description( $instance, $author_description ) {
		if ( ! $instance['show_des'] ) {
			return;
		}
		?>
		<div class="author-description<?php echo $instance['line_clamp'] ? ' clamp' : ''; ?>">
			<?php
			if ( ! empty( $author_description ) ) {
				echo esc_html( $author_description );
			} else {
				_e( '暂无', 'begin' );
			}
			?>
		</div>
		<?php
	}

	// 渲染作者联系方式
	private function render_author_contact( $instance, $author_data ) {
		if ( ! $instance['show_meta'] ) {
			return;
		}
		?>
		<div class="feed-about-box">
			<?php $this->render_weixin_contact( $author_data ); ?>
			<?php $this->render_phone_contact( $author_data ); ?>
			<?php $this->render_qq_contact( $author_data ); ?>
		</div>
		<?php
	}

	// 渲染微信联系方式
	private function render_weixin_contact( $author_data ) {
		if ( empty( $author_data['author_weixin'] ) ) {
			return;
		}
		?>
		<div class="feed-t weixin">
			<div class="weixin-b">
				<div class="weixin-qr fd">
					<div class="weixin-qr-about">
						<div class="copy-weixin">
							<?php if ( ! empty( $author_data['author_weixinqr'] ) ) : ?>
								<img src="<?php echo esc_url( $author_data['author_weixinqr'] ); ?>" alt="weixin"/>
							<?php endif; ?>
							<div class="be-copy-text"><?php _e( '点击复制', 'begin' ); ?></div>
							<div class="weixinbox">
								<div class="btn-weixin-copy"></div>
								<div class="weixin-id"><?php echo esc_html( $author_data['author_weixin'] ); ?></div>
								<div class="copy-success-weixin fd">
									<div class="copy-success-weixin-text aboutweixin">
										<span class="dashicons dashicons-saved"></span>
										<?php _e( '已复制', 'begin' ); ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="arrow-down"></div>
				</div>
				<a><i class="be be-weixin"></i></a>
			</div>
		</div>
		<?php
	}

	// 渲染电话联系方式
	private function render_phone_contact( $author_data ) {
		if ( empty( $author_data['author_phone'] ) ) {
			return;
		}
		?>
		<div class="feed-t weixin author-phone">
			<div class="weixin-b">
				<div class="weixin-qr fd">
					<div class="weixin-qr-about">
						<div class="copy-weixin">
							<div class="phone-number">
								<div class="be-copy-text"><?php _e( '点击复制', 'begin' ); ?></div>
								<?php echo esc_html( $author_data['author_phone'] ); ?>
							</div>
							<div class="weixinbox">
								<div class="btn-weixin-copy"></div>
								<div class="weixin-id"><?php echo esc_html( $author_data['author_phone'] ); ?></div>
								<div class="copy-success-weixin fd">
									<div class="copy-success-weixin-text telphone">
										<span class="dashicons dashicons-saved"></span>
										<?php _e( '已复制', 'begin' ); ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="arrow-down"></div>
				</div>
				<a><i class="be be-phone"></i></a>
			</div>
		</div>
		<?php
	}

	// 渲染QQ联系方式
	private function render_qq_contact( $author_data ) {
		if ( empty( $author_data['author_qq'] ) ) {
			return;
		}
		?>
		<div class="feed-t tqq">
			<a class="quoteqq" 
				href="<?php echo esc_url( 'https://wpa.qq.com/msgrd?V=3&uin=' . $author_data['author_qq'] . '&Site=QQ&Menu=yes' ); ?>" 
				onclick="copyToClipboard(this)" 
				title="<?php _e( '点击复制', 'begin' ); ?>" 
				target="_blank" 
				rel="external nofollow">
				<i class="be be-qq"></i>
			</a>
		</div>
		<?php
	}

	// 渲染作者统计信息
	private function render_author_stats( $instance, $author_id, $comment_count ) {
		if ( ! $instance['show_inf'] ) {
			return;
		}

		$is_english = ( get_bloginfo( 'language' ) === 'en-US' );
		?>
		<div class="author-th-inf<?php echo $is_english ? ' author-th-inf-en' : ''; ?>">
			<?php if ( $instance['show_posts'] ) : ?>
				<div class="author-n author-nickname">
					<?php _e( '文章', 'begin' ); ?><br />
					<span><?php echo absint( count_user_posts( $author_id, array( 'post', 'picture', 'video', 'tao', 'sites' ), false ) ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $instance['show_comment'] ) : ?>
				<div class="author-n">
					<?php _e( '评论', 'begin' ); ?><br />
					<span><?php echo absint( $comment_count ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $instance['show_views'] && zm_get_option( 'post_views' ) ) : ?>
				<div class="author-n">
					<?php _e( '浏览', 'begin' ); ?><br />
					<span><?php author_posts_views( $author_id ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $instance['show_fans'] && zm_get_option( 'follow_btn' ) ) : ?>
				<div class="author-n">
					<?php _e( '粉丝', 'begin' ); ?><br />
					<span><?php echo absint( get_fans_count( $author_id ) ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $instance['show_like'] ) : ?>
				<div class="author-n author-th-views">
					<?php _e( '点赞', 'begin' ); ?><br />
					<span><?php echo absint( like_posts_views( $author_id ) ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	// 更新小工具设置
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// 处理布尔值选项
		$boolean_fields = array(
			'show_inf',
			'show_meta',
			'show_posts',
			'show_comment',
			'show_fans',
			'show_views',
			'show_like',
			'show_role',
			'line_clamp',
			'show_des',
		);

		foreach ( $boolean_fields as $field ) {
			$instance[ $field ] = ! empty( $new_instance[ $field ] ) ? 1 : 0;
		}

		// 处理文本字段
		$instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['author_back'] = esc_url_raw( $new_instance['author_back'] ?? '' );

		return $instance;
	}

	// 小工具设置表单
	function form( $instance ) {
		$defaults = $this->zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		global $wpdb;
		$instance    = wp_parse_args( (array) $instance, array( 'author_back' => get_template_directory_uri() . '/img/default/options/user.jpg' ) );
		$author_back = $instance['author_back'];
		// $instance = wp_parse_args((array) $instance, array('author_url' => ''));
		// $author_url = $instance['author_url'];
		?>
	<p>
		<label for="<?php echo $this->get_field_id( 'author_back' ); ?>">背景图片：</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'author_back' ); ?>" name="<?php echo $this->get_field_name( 'author_back' ); ?>" type="text" value="<?php echo $author_back; ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
	</p>
	<div class="widgets-ins">
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_des' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_des' ) ); ?>" <?php checked( (bool) $instance['show_des'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_des' ) ); ?>">显示说明</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'line_clamp' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line_clamp' ) ); ?>" <?php checked( (bool) $instance['line_clamp'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'line_clamp' ) ); ?>">截断说明</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_meta' ) ); ?>" <?php checked( (bool) $instance['show_meta'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>">联系方式</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_inf' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_inf' ) ); ?>" <?php checked( (bool) $instance['show_inf'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_inf' ) ); ?>">显示信息</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_posts' ) ); ?>" <?php checked( (bool) $instance['show_posts'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>">文章</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_comment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_comment' ) ); ?>" <?php checked( (bool) $instance['show_comment'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_comment' ) ); ?>">评论</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_views' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_views' ) ); ?>" <?php checked( (bool) $instance['show_views'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_views' ) ); ?>">浏览</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_fans' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_fans' ) ); ?>" <?php checked( (bool) $instance['show_fans'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_fans' ) ); ?>">粉丝</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_like' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_like' ) ); ?>" <?php checked( (bool) $instance['show_like'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_like' ) ); ?>">点赞</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_role' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_role' ) ); ?>" <?php checked( (bool) $instance['show_role'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_role' ) ); ?>">角色</label>
		</p>
	</div>
	<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
		<?php
	}
}
function about_author_init() {
	register_widget( 'about_author' );
}
add_action( 'widgets_init', 'about_author_init' );
