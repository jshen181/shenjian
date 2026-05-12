<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 专栏
class be_column_cover extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_column_cover',
			'description' =>'显示专栏封面列表',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_column_cover', '专栏', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_icon'  => '',
			'title_z'    => '',
			'show_svg'   => '',
			'title'      => '专栏',
			'special_id' => '',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$defaults = $this -> zm_defaults();
		$title_w = title_i_w();
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$hideTitle = ! empty( $instance['hideTitle'] ) ? true : false;
		$titleUrl = empty( $instance['titleUrl'] ) ? '' : $instance['titleUrl'];
		$newWindow = ! empty( $instance['newWindow'] ) ? true : false;
		if ( zm_get_option( 'more_im' ) ) {
			$more_i = '<span class="more-i more-im"><span></span><span></span><span></span></span>';
		} else {
			$more_i = '<span class="more-i"><span></span><span></span><span></span></span>';
		}
		echo $before_widget;
		if ( $newWindow ) $newWindow = "target='_blank'";
			if ( ! $hideTitle && $title ) {
				if ( $titleUrl ) $title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
			}
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title; 
?>
<div class="new_cat">
	<?php if ( $instance['titleUrl'] ) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon">
			<a href="<?php echo $instance['titleUrl']; ?>" rel="bookmark">
				<i class="t-icon <?php echo $instance['show_icon']; ?>"></i>
				<?php echo $instance['title_z']; ?>
				<?php more_i(); ?>
			</a>
		</h3>
	<?php } else { ?>
		<?php if ( $instance['show_icon'] ) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
		<?php } ?>
		<?php if ( $instance['show_svg'] ) { ?>
			<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
		<?php } ?>
	<?php } ?>
	<ul>
		<?php
			$special = array(
				'taxonomy'      => 'special',
				'show_count'    => 1,
				'orderby'       => 'include',
				'order'         => 'DESC',
				'include'       => $instance['special_id'],
				'hide_empty'    => 0,
				'hierarchical'  => 0
			);
			$cats = get_categories( $special );
		?>
		<?php foreach( $cats as $cat ) : ?>

			<li>
				<span class="thumbnail lazy">
					<?php if ( zm_get_option( 'cat_cover' ) ) { ?>
						<?php if ( zm_get_option( 'lazy_s' ) ) { ?>
							<a class="thumbs-back sc" href="<?php echo get_category_link( $cat->term_id ) ?>" rel="bookmark" <?php echo goal(); ?> data-src="<?php echo cat_cover_url( $cat->term_id ); ?>"></a>
						<?php } else { ?>
							<a class="thumbs-back sc" href="<?php echo get_category_link( $cat->term_id ) ?>" rel="bookmark" <?php echo goal(); ?> style="background-image: url(<?php echo cat_cover_url( $cat->term_id ); ?>);"></a>
						<?php } ?>
					<?php } else { ?>
						<div class="cat-cover-tip">未启用分类封面</div>
					<?php } ?>

				</span>
				<span class="new-title column-title-w">
					<a href="<?php echo get_category_link( $cat->term_id ) ?>" rel="bookmark" <?php echo goal(); ?>>
						<?php echo $cat->name; ?>
						<span class="special-des-w"><?php echo term_description( $cat->term_id ); ?></span>
					</a>
				</span>
				<span class="views"><?php echo $cat->count; ?><?php _e( '篇', 'begin' ); ?></span>
			</li>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</ul>
</div>

<?php
	echo $after_widget;
}

function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance = array();
	$instance['show_icon'] = strip_tags( $new_instance['show_icon'] );
	$instance['special_id'] = strip_tags( $new_instance['special_id'] );
	$instance['hideTitle'] = ! empty( $new_instance['hideTitle'] ) ? 1 : 0;
	$instance['newWindow'] = ! empty( $new_instance['newWindow'] ) ? 1 : 0;
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['title_z'] = strip_tags( $new_instance['title_z'] );
	$instance['titleUrl'] = strip_tags( $new_instance['titleUrl'] );
	return $instance;
}

function form( $instance ) {
	$defaults = $this -> zm_defaults();
	$instance = wp_parse_args( ( array ) $instance, $defaults );
	$instance = wp_parse_args( ( array ) $instance, 
		array( 
			'title' => '专栏',
			'titleUrl' => '',
		)
	);
	$titleUrl = $instance['titleUrl'];
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_z' ); ?>">图标标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_z' ); ?>" name="<?php echo $this->get_field_name( 'title_z' ); ?>" type="text" value="<?php echo $instance['title_z']; ?>" />
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
			<label for="<?php echo $this->get_field_id( 'titleUrl' ); ?>">标题链接：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'titleUrl' ); ?>" name="<?php echo $this->get_field_name( 'titleUrl' ); ?>" type="text" value="<?php echo $titleUrl; ?>" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'newWindow' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'newWindow' ); ?>" <?php checked( isset( $instance['newWindow'] ) ? $instance['newWindow'] : 0); ?> />
			<label for="<?php echo $this->get_field_id( 'newWindow' ); ?>">在新窗口打开标题链接</label>
		</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'special_id' ); ?>">输入专栏ID：</label>
		<textarea style="height:30px;" class="widefat" id="<?php echo $this->get_field_id( 'special_id' ); ?>" name="<?php echo $this->get_field_name( 'special_id' ); ?>"><?php echo stripslashes(htmlspecialchars( ( $instance['special_id'] ), ENT_QUOTES ) ); ?></textarea>
		多个ID用英文半角逗号","隔开，按先后排序
	</p>
<?php }
}

add_action( 'widgets_init', 'be_column_cover_init' );
function be_column_cover_init() {
	register_widget( 'be_column_cover' );
}