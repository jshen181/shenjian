<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 限定时间内的文章
class be_specified_post extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'be_specified_post',
			'description' => '调用限定时间内的文章',
			'customize_selective_refresh' => true,
		);
		parent::__construct('be_specified_post', '限定时间文章', $widget_ops);
	}

	public function zm_defaults() {
		return array(
			'show_thumbs' => 1,
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'from_y'      => 2017,
			'from_m'      => 1,
			'from_d'      => 2,
			'to_y'        => 2017,
			'to_m'        => 5,
			'to_d'        => 31,
			'sp_cat'      => 0,
			'numposts'    => 5,
			'title'       => '限定时间文章',
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title_w . $title . $after_title; 
?>

<?php if ($instance['show_thumbs']) { ?>
<div class="new_cat">
<?php } else { ?>
<div class="mday_post">
<?php } ?>
	<?php if ($instance['show_icon']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon <?php echo $instance['show_icon']; ?>"></i><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<?php if ($instance['show_svg']) { ?>
		<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#<?php echo $instance['show_svg']; ?>"></use></svg><?php echo $instance['title_z']; ?></h3>
	<?php } ?>
	<ul>
		<?php
			$args = array(
			'ignore_sticky_posts' => 1,
				'date_query' => array(
					array(
						'after'     =>  array(
							'year'  => $instance['from_y'],
							'month' => $instance['from_m'],
							'day'   => $instance['from_d'],
						),
						'before'    => array(
							'year'  => $instance['to_y'],
							'month' => $instance['to_m'],
							'day'   => $instance['to_d'],
						),
						'inclusive' => true,
					),
				),
				'posts_per_page' => $instance['numposts'],
				'cat' => $instance['sp_cat'],
				'post_status'    => 'publish',
				'no_found_rows'       => true,
			);
			$query = new WP_Query( $args );
		?>
		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
			<li>
				<?php if ($instance['show_thumbs']) { ?>
					<span class="thumbnail"><?php echo zm_thumbnail(); ?></span>
					<span class="new-title"><a href="<?php the_permalink(); ?>" rel="bookmark" <?php echo goal(); ?>><?php the_title(); ?></a></span>
					<?php grid_meta(); ?>
					<?php views_span(); ?>
				<?php } else { ?>
					<?php the_title( sprintf( '<a class="srm get-icon" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
				<?php } ?>
				<div class="clear"></div>
			</li>
		<?php endwhile;?>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<li>
			<span class="new-title-no"><?php _e( '暂无文章', 'begin' ); ?></span>
		</li>
		<?php endif;?>
	</ul>
</div>

<?php
	echo $after_widget;
}

function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance['show_thumbs'] = !empty($new_instance['show_thumbs']) ? 1 : 0;
	$instance['title_z'] = strip_tags($new_instance['title_z']);
	$instance['show_icon'] = strip_tags($new_instance['show_icon']);
	$instance['show_svg'] = strip_tags($new_instance['show_svg']);
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['numposts'] = $new_instance['numposts'];
	$instance['sp_cat'] = $new_instance['sp_cat'];
	$instance['from_y'] = $new_instance['from_y'];
	$instance['from_m'] = $new_instance['from_m'];
	$instance['from_d'] = $new_instance['from_d'];
	$instance['to_y'] = $new_instance['to_y'];
	$instance['to_m'] = $new_instance['to_m'];
	$instance['to_d'] = $new_instance['to_d'];
	return $instance;
}

function form( $instance ) {
	$defaults = $this -> zm_defaults();
	$instance = wp_parse_args( (array) $instance, $defaults );
	$instance = wp_parse_args( (array) $instance, array( 
		'title' => '限定时间文章',
		'numposts' => 5,
		'from_y' => 2017,
		'from_m' => 1,
		'from_d' => 2,
		'to_y' => 2017,
		'to_m' => 5,
		'to_d' => 31,
		'sp_cat' => 0)); ?> 

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
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
		<h4 class="from_m_options_header">输入起止日期</h4>

		<p>
			<label for="<?php echo $this->get_field_id('from_y'); ?>" style="width: 33%;">从 
			<input id="<?php echo $this->get_field_id('from_y'); ?>" name="<?php echo $this->get_field_name('from_y'); ?>" type="text" value="<?php echo $instance['from_y']; ?>" size="3" /> 年 
			</label>
			<label for="<?php echo $this->get_field_id('from_m'); ?>" style="width: 33%;"></label>
			<input id="<?php echo $this->get_field_id('from_m'); ?>" name="<?php echo $this->get_field_name('from_m'); ?>" type="text" value="<?php echo $instance['from_m']; ?>" size="3" /> 月 
			<label for="<?php echo $this->get_field_id('from_d'); ?>" style="width: 33%;"></label>
			<input id="<?php echo $this->get_field_id('from_d'); ?>" name="<?php echo $this->get_field_name('from_d'); ?>" type="text" value="<?php echo $instance['from_d']; ?>" size="3" />日起
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('to_y'); ?>" style="width: 33%;">至 </label>
			<input id="<?php echo $this->get_field_id('to_y'); ?>" name="<?php echo $this->get_field_name('to_y'); ?>" type="text" value="<?php echo $instance['to_y']; ?>" size="3" /> 年 
			<label for="<?php echo $this->get_field_id('to_m'); ?>" style="width: 33%;"></label>
			<input id="<?php echo $this->get_field_id('to_m'); ?>" name="<?php echo $this->get_field_name('to_m'); ?>" type="text" value="<?php echo $instance['to_m']; ?>" size="3" /> 月 
			<label for="<?php echo $this->get_field_id('to_d'); ?>" style="width: 33%;"></label>
			<input id="<?php echo $this->get_field_id('to_d'); ?>" name="<?php echo $this->get_field_name('to_d'); ?>" type="text" value="<?php echo $instance['to_d']; ?>" size="3" /> 日止
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('sp_cat'); ?>">选择分类：
			<?php wp_dropdown_categories(array('name' => $this->get_field_name('sp_cat'), 'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['sp_cat'])); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示数量：</label>
			<input class="number-text-be" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
		</p>
<?php }
}
add_action( 'widgets_init', 'be_specified_post_init' );
function be_specified_post_init() {
	register_widget( 'be_specified_post' );
}
