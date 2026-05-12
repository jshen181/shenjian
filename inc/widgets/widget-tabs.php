<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Tab
class be_tabs extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_tabs',
			'description' => '最新文章、热评文章、热门文章、最近留言',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'be_tabs', '组合小工具', $widget_ops );
	}

	public function zm_get_defaults() {
		return array(
			'title'            => '',
			'tabs_category'    => 1,
			'tabs_date'        => 1,
			// Recent posts
			'recent_enable'     => 1,
			'recent_thumbs'     => 1,
			'recent_cat_id'     => '0',
			'recent_num'        => '5',
			// Popular posts
			'popular_enable'    => 1,
			'popular_thumbs'    => 1,
			'popular_cat_id'    => '0',
			'popular_time'      => '0',
			'popular_num'       => '5',
			// Recent comments
			'comments_enable'   => 1,
			'comments_avatars'  => 1,
			'comments_num'      => '5',
			'authornot'        => '1',
			// viewe
			'viewe_enable'     => 1,
			'viewe_thumbs'     => 1,
			'viewe_number'     => '5',
			'viewe_days'       => '90',
		);
	}

	private function _create_tabs($tabs,$count) {
		$titles = array(
			'recent'    => __( '最新文章', 'begin' ),
			'popular'   => __( '热评文章', 'begin' ),
			'viewe'     => __( '热门文章', 'begin' ),
			'comments'  => __( '最近留言', 'begin' )
		);
		$icons = array(
			'recent'   => 'be be-file',
			'popular'  => 'be be-favoriteoutline',
			'viewe'     => 'be be-eye',
			'comments' => 'be be-speechbubble'
		);

		$output = sprintf('<div class="zm-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<span class="zm-tab tab-%1$s"><a href="javascript:"><i class="%3$s"></i><span>%4$s</span></a></span>',
				$tab,
				$tab . '-' . $this -> number,
				$icons[$tab],
				$titles[$tab]
			);
		}
		$output .= '</div>';
		return $output;
	}

	public function widget($args, $instance) {
		extract( $args );
		$title_w = title_i_w();
		$defaults = $this -> zm_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = apply_filters('widget_title',$instance['title']);
		$title = empty( $title ) ? '' : $title;
		$output = $before_widget."\n";
		if ( ! empty( $title ) )
			$output .= $before_title . $title_w . $title . $after_title;
		ob_start();

	$tabs = array();
	$count = 0;
	$order = array(
		'recent'    => 1,
		'popular'   => 2,
		'viewe'     => 3,
		'comments'  => 4
	);
	asort($order);
	foreach ( $order as $key => $value ) {
		if ( $instance[$key.'_enable'] ) {
			$tabs[] = $key;
			$count++;
		}
	}
	if ( $tabs && ($count > 1) ) {
		$output .= $this->_create_tabs($tabs,$count);
	}
?>

	<div class="zm-tabs-container">
		<?php 
			global $post;
			if ( is_single() ) {
			$recent =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts' => $instance['recent_num'],
				'post__not_in' => array($post->ID),
				'cat' => $instance['recent_cat_id'],
			));
			} else {
			$recent =  new WP_Query(array(
				'ignore_sticky_posts' => 1,
				'showposts' => $instance['recent_num'],
				'cat' => $instance['recent_cat_id'],
			));
		} ?>
		<div class="new_cat">
			<ul id="tab-recent-<?php echo $this -> number ?>" class="zm-tab group <?php if ($instance['recent_thumbs']) { echo 'thumbs-enabled'; } ?>" style="display:block;">
				<h4><?php _e( '最新文章', 'begin' ); ?></h4>
				<?php while ($recent->have_posts()): $recent->the_post(); ?>
				<li>
					<?php if ($instance['recent_thumbs']) { ?>
						<span class="thumbnail">
							<?php echo zm_thumbnail(); ?>
						</span>
						<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
						<?php grid_meta(); ?>
						<?php views_span(); ?>
					<?php } else { ?>
						<?php the_title( sprintf( '<a class="get-icon" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
					<?php } ?>
				</li>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</ul>
		</div>

		<?php
			$popular = new WP_Query( array(
				'post_type'             => array( 'post' ),
				'showposts'             => $instance['popular_num'],
				'cat'                   => $instance['popular_cat_id'],
				'ignore_sticky_posts'   => true,
				'orderby'               => 'comment_count',
				'order'                 => 'DESC',
				'date_query' => array(
					array(
						'after' => $instance['popular_time'],
					),
				),
			) );
		?>

		<div class="new_cat">
			<ul id="tab-popular-<?php echo $this -> number ?>" class="zm-tab group <?php if ($instance['popular_thumbs']) { echo 'thumbs-enabled'; } ?>">
				<h4><?php _e( '热评文章', 'begin' ); ?></h4>
				<?php while ( $popular->have_posts() ): $popular->the_post(); ?>
				<li>
					<?php if ($instance['popular_thumbs']) { ?>
						<span class="thumbnail">
							<?php echo zm_thumbnail(); ?>
						</span>
						<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark <?php echo goal(); ?>" <?php echo goal(); ?>><?php the_title(); ?></a></span>
						<?php grid_meta(); ?>
						<span class="discuss"><?php comments_number( '', '<i class="be be-speechbubble ri"></i>1 ', '<i class="be be-speechbubble ri"></i>%' ); ?></span>
					<?php } else { ?>
						<a class="get-icon" href="<?php the_permalink(); ?>" rel="bookmark <?php echo goal(); ?>" <?php echo goal(); ?>><?php the_title(); ?></a>
					<?php } ?>
				</li>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</ul>
		</div>

		<div class="new_cat">
			<ul id="tab-viewe-<?php echo $this -> number ?>" class="zm-tab group">
				<h4><?php _e( '热门文章', 'begin' ); ?></h4>
				<?php if (zm_get_option('post_views')) { ?>
					<?php if ($instance['viewe_thumbs']) { ?>
						<?php get_timespan_most_viewed_img('post',$instance["viewe_number"],$instance["viewe_days"], true, true); ?>
					<?php } else { ?>
						<?php get_timespan_most_viewed('post',$instance["viewe_number"],$instance["viewe_days"], true, true); ?>
					<?php } ?>
					<?php wp_reset_query(); ?>
				<?php } else { ?>
					<li>需要启用文章浏览统计</a></li>
				<?php } ?>
			</ul>
		</div>

		<?php $comments = get_comments(array('number'=>$instance["comments_num"],'status'=>'approve','post_status'=>'publish')); ?>
		<div class="message-tab message-widget gaimg load">
			<ul>
				<h4><?php _e( '最近留言', 'begin' ); ?></h4>
				<?php 
			
				$no_comments = false;
				$avatar_size = 96;
				$comments_query = new WP_Comment_Query();
				$comments = $comments_query->query( array_merge( array( 'number' => $instance["comments_num"], 'status' => 'approve', 'type' => 'comments', 'post_status' => 'publish', 'author__not_in' => explode(',',$instance["authornot"]) ) ) );
				if ( $comments ) : foreach ( $comments as $comment ) : ?>

				<li>
					<a class="commentanchor" href="<?php echo get_permalink($comment->comment_post_ID); ?>#anchor-comment-<?php echo $comment->comment_ID; ?>" title="<?php _e( '发表在', 'begin' ); ?>：<?php echo get_the_title($comment->comment_post_ID); ?>" rel="external nofollow" <?php echo goal(); ?>>
						<?php if ($instance['comments_avatars']) : ?>
						<?php if (zm_get_option('cache_avatar')) { ?>
							<?php echo begin_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) ); ?>
						<?php } else { ?>
							<?php if ( !zm_get_option( 'avatar_load' ) ) {
								echo get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) );
							} else {
								echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. get_comment_author( $comment->comment_ID ) .'" width="30" height="30" data-original="' . preg_replace(array('/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i'), array('', ''), get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) )) . '" />';
							} ?>
						<?php } ?>
						<?php endif; ?>
						<?php if ( zm_get_option( 'comment_vip' ) ) { ?>
							<?php
								$authoremail = get_comment_author_email( $comment );
								if ( email_exists( $authoremail ) ) {
									$commet_user_role = get_user_by( 'email', $authoremail );
									$comment_user_role = $commet_user_role->roles[0];
										if ( $comment_user_role !== zm_get_option('roles_vip') ) {
											echo '<span class="comment_author">' . get_comment_author( $comment->comment_ID ) . '</span>';
										} else {
											echo '<span class="comment_author message-widget-vip">' . get_comment_author( $comment->comment_ID ) . '</span>';
										}
								} else {
									echo '<span class="comment_author">' . get_comment_author( $comment->comment_ID ) . '</span>';
								}
							?>
						<?php } else { ?>
							<span class="comment_author"><?php echo get_comment_author( $comment->comment_ID ); ?></span>
						<?php } ?>
						<?php echo convert_smilies($comment->comment_content); ?>
					</a>
				</li>

				<?php endforeach; else : ?>
					<li><?php _e('暂无留言', 'begin'); ?></li>
					<?php $no_comments = true;
				endif; ?>
			</ul>
		</div>

	</div>

<?php
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	}

	public function update($new,$old) {
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);
		$instance['tabs_category'] = !empty($new['tabs_category']) ? 1 : 0;
		$instance['tabs_date'] = !empty($new['tabs_date']) ? 1 : 0;
	// Recent posts
		$instance['recent_thumbs'] = !empty($new['recent_thumbs']) ? 1 : 0;
		$instance['recent_cat_id'] = strip_tags($new['recent_cat_id']);
		$instance['recent_num'] = strip_tags($new['recent_num']);
	// Popular posts
		$instance['popular_thumbs'] = !empty($new['popular_thumbs']) ? 1 : 0;
		$instance['popular_cat_id'] = strip_tags($new['popular_cat_id']);
		$instance['popular_time'] = strip_tags($new['popular_time']);
		$instance['popular_num'] = strip_tags($new['popular_num']);
	// Recent comments
		$instance['comments_avatars'] = !empty($new['comments_avatars']) ? 1 : 0;
		$instance['comments_num'] = strip_tags($new['comments_num']);
		$instance['authornot'] = strip_tags($new['authornot']);
	// viewe
		$instance['viewe_thumbs'] = !empty($new['viewe_thumbs']) ? 1 : 0;
		$instance['viewe_number'] = strip_tags($new['viewe_number']);
		$instance['viewe_days'] = strip_tags($new['viewe_days']);
		return $instance;
	}

/*  Widget form
/* ------------------------------------ */
	public function form($instance) {
		$defaults = $this -> zm_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

	<style>.widget .widget-inside .zm-options-tabs hr {margin: 20px -15px;border-top: 1px solid #dadada;}</style>

	<div class="zm-options-tabs">
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">标题：</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
		</p>

		<h4>最新文章</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('recent_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('recent_thumbs') ); ?>" <?php checked( (bool) $instance["recent_thumbs"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('recent_thumbs') ); ?>">显示缩略图</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'recent_num' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'recent_num' ); ?>" name="<?php echo $this->get_field_name( 'recent_num' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['recent_num']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id("recent_cat_id") ); ?>">选择分类：</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("recent_cat_id"), 'selected' => $instance["recent_cat_id"], 'show_option_all' => '全部分类', 'show_count' => true ) ); ?>
		</p>

		<hr>
		<h4>热评文章</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('popular_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('popular_thumbs') ); ?>" <?php checked( (bool) $instance["popular_thumbs"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('popular_thumbs') ); ?>">显示缩略图</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'popular_num' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'popular_num' ); ?>" name="<?php echo $this->get_field_name( 'popular_num' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['popular_num']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id("popular_cat_id") ); ?>">选择分类：</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("popular_cat_id"), 'selected' => $instance["popular_cat_id"], 'show_option_all' => '全部分类', 'show_count' => true ) ); ?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id("popular_time") ); ?>">选择时间段：</label>
			<select id="<?php echo esc_attr( $this->get_field_id("popular_time") ); ?>" name="<?php echo esc_attr( $this->get_field_name("popular_time") ); ?>">
				<option value="0"<?php selected( $instance["popular_time"], "0" ); ?>>全部</option>
				<option value="1 year ago"<?php selected( $instance["popular_time"], "1 year ago" ); ?>>一年内</option>
				<option value="1 month ago"<?php selected( $instance["popular_time"], "1 month ago" ); ?>>一月内</option>
				<option value="1 week ago"<?php selected( $instance["popular_time"], "1 week ago" ); ?>>一周内</option>
				<option value="1 day ago"<?php selected( $instance["popular_time"], "1 day ago" ); ?>>24小时内</option>
			</select>
		</p>

		<hr>
		<h4>热门文章</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('viewe_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('viewe_thumbs') ); ?>" <?php checked( (bool) $instance["viewe_thumbs"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('viewe_thumbs') ); ?>">显示缩略图</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'viewe_number' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'viewe_number' ); ?>" name="<?php echo $this->get_field_name( 'viewe_number' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['viewe_number']; ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'viewe_days' ); ?>">时间限定（天）：</label>
			<input class="number-text-be-d" id="<?php echo $this->get_field_id( 'viewe_days' ); ?>" name="<?php echo $this->get_field_name( 'viewe_days' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['viewe_days']; ?>" size="3" />
		</p>

		<hr>
		<h4>最新留言</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('comments_avatars') ); ?>" name="<?php echo esc_attr( $this->get_field_name('comments_avatars') ); ?>" <?php checked( (bool) $instance["comments_avatars"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('comments_avatars') ); ?>">显示头像</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id("authornot") ); ?>">排除的用户ID：</label>
			<input id="<?php echo esc_attr( $this->get_field_id("authornot") ); ?>" name="<?php echo esc_attr( $this->get_field_name("authornot") ); ?>" type="text" value="<?php echo absint($instance["authornot"]); ?>" size='3' />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'comments_num' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'comments_num' ); ?>" name="<?php echo $this->get_field_name( 'comments_num' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['comments_num']; ?>" size="3" />
		</p>
	</div>
<?php
}
}

function be_tabs_init() {
	register_widget( 'be_tabs' );
}
add_action( 'widgets_init', 'be_tabs_init' );
