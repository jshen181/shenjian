<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 多功能小工具
class php_text extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'php_text',
			'description' => '支持HTML、JavaScript、短代码等',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'php_text', '增强文本', $widget_ops );
	}

	public function zm_defaults() {
		return array(
			'title_z'     => '',
			'show_icon'   => '',
			'show_svg'    => '',
			'text'        => '',
			'padding'     => '',
		);
	}

	function widget( $args, $instance ) {

		if (!isset($args['widget_id'])) {
			$args['widget_id'] = null;
		}

		extract($args);
		$title_w = title_i_w();
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
		$titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
		$cssClass = empty($instance['cssClass']) ? '' : $instance['cssClass'];
		$text = apply_filters('widget_enhanced_text', $instance['text'], $instance);
		$hideTitle = !empty($instance['hideTitle']) ? true : false;
		$padding = !empty($instance['padding']) ? true : false;
		$newWindow = !empty($instance['newWindow']) ? true : false;
		$filterText = !empty($instance['filter']) ? true : false;
		$bare = !empty($instance['bare']) ? true : false;
		if ( zm_get_option('more_im') ) {
			$more_i = '<span class="more-i more-im"><span></span><span></span><span></span></span>';
		} else {
			$more_i = '<span class="more-i"><span></span><span></span><span></span></span>';
		}
		if ( $cssClass ) {
			if ( strpos($before_widget, 'class') === false ) {
				$before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
			} else {
				$before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
			}
		}

		ob_start();
		echo $text;
		$text = ob_get_contents();
		ob_end_clean();
		$text = do_shortcode($text);

		if ( !empty( $text ) ) {
			echo $bare ? '' : $before_widget;
		if ($newWindow) $newWindow = 'target="_blank"';
			if (!$hideTitle && $title) {
				if ($titleUrl) $title = "<a href='$titleUrl' $newWindow>$title $more_i</a>";
				echo $bare ? $title : $before_title . $title_w . $title . $after_title;
			}
			if ($instance['show_icon']) {
				if ($instance['titleUrl']) {
					echo '<h3 class="widget-title-cat-icon cat-w-icon"><a href=' . $titleUrl . ' ' . $newWindow . '><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'], more_i() . '</a></h3>';
				} else {
					echo '<h3 class="widget-title-cat-icon cat-w-icon"><i class="t-icon ' . $instance['show_icon'] . '"></i>' . $instance['title_z'] . '</h3>';
				}
			}

			if ($instance['show_svg']) {
				if ($instance['titleUrl']) {
					echo '<h3 class="widget-title-cat-icon cat-w-icon"><a href=' . $titleUrl . '><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#'.$instance['show_svg'].'"></use></svg>' . $instance['title_z'], more_i() . '</a></h3>';
				} else {
					echo '<h3 class="widget-title-cat-icon cat-w-icon"><svg class="t-svg icon" aria-hidden="true"><use xlink:href="#'.$instance['show_svg'].'"></use></svg>' . $instance['title_z'] . '</h3>';
				}
			}

			if ( $instance['padding'] ) {
				echo $bare ? '' : '<div class="textwidget widget-text widget-text-padding">';
			} else {
				echo $bare ? '' : '<div class="textwidget widget-text">';
			}
			echo $filterText ? wpautop($text) : $text;
			echo $bare ? '' : '</div>' . $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = wp_filter_post_kses($new_instance['text']);
			$instance['title_z'] = strip_tags($new_instance['title_z']);
			$instance['show_icon'] = strip_tags($new_instance['show_icon']);
			$instance['show_svg'] = strip_tags($new_instance['show_svg']);
			$instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
			$instance['hideTitle'] = !empty($new_instance['hideTitle']) ? 1 : 0;
			$instance['padding'] = !empty($new_instance['padding']) ? 1 : 0;
			$instance['newWindow'] = !empty($new_instance['newWindow']) ? 1 : 0;
			$instance['filter'] = !empty($new_instance['filter']) ? 1 : 0;
			$instance['bare'] = !empty($new_instance['bare']) ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		$defaults = $this -> zm_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '增强文本',
			'titleUrl' => '',
			'text' => ''
		));
		$title = $instance['title'];
		$titleUrl = $instance['titleUrl'];
		$text = format_to_edit($instance['text']);
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('titleUrl'); ?>">标题链接：</label>
			<input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo $titleUrl; ?>" />
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
			<label for="<?php echo $this->get_field_id('text'); ?>">内容：</label>
			<textarea class="widefat monospace" rows="6" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		</p>

		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('hideTitle'); ?>">
				<input id="<?php echo $this->get_field_id('hideTitle'); ?>" class="checkbox" name="<?php echo $this->get_field_name('hideTitle'); ?>" type="checkbox" <?php checked(isset($instance['hideTitle']) ? $instance['hideTitle'] : 0); ?> />
				不显示标题
			</label>
		</p>
		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('newWindow'); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
				在新窗口打开标题链接
			</label>
		</p>

		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('filter'); ?>">
				<input id="<?php echo $this->get_field_id('filter'); ?>" class="checkbox" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />
				自动添加段落
			</label>
		</p>

		<p>
			<label class="alignleft" style="display: block; width: 50%; margin-bottom: 10px;" for="<?php echo $this->get_field_id('padding'); ?>">
				<input id="<?php echo $this->get_field_id('padding'); ?>" class="checkbox" name="<?php echo $this->get_field_name('padding'); ?>" type="checkbox" <?php checked(isset($instance['padding']) ? $instance['padding'] : 0); ?> />
				内边距
			</label>
		</p>
<?php }
}

function php_text_init() {
	register_widget( 'php_text' );
}
add_action( 'widgets_init', 'php_text_init' );
