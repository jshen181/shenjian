<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 自定义按钮
class BE_ICO_BTN_Widget extends WP_Widget {
	public $defaults;
	function __construct() {
		$widget_ops  = array(
			'classname'   => 'btn-widget',
			'description' => '用于添加自定义链接按钮',
		);
		$control_ops = array( 'id_base' => 'btn_widget' );
		parent::__construct( 'btn_widget', '自定义按钮', $widget_ops, $control_ops );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		$this->defaults = array(
			'title'      => '',
			'content'    => '',
			'column'     => '2',
			'height'     => 80,
			'font_color' => '#fff',
			'target'     => '_blank',
			'btn'        => array(),
		);

		// 允许自定义修改默认参数
		// $this->defaults = apply_filters( 'btn_widget_modify_defaults', $this->defaults );
	}

	function enqueue_admin_scripts() {
		wp_enqueue_script( 'btn-widget-js', get_template_directory_uri() . '/inc/assets/js/addbtn.js', array( 'jquery', 'jquery-ui-sortable' ), null );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		// $title_w = title_i_w();
		// $title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;

		// if ( ! empty( $title ) ) {
			// echo $before_title . $title_w . wp_kses_post( $title ) . $after_title;
		// }
		?>

		<?php if ( ! empty( $instance['content'] ) ) : ?>
			<?php echo wp_kses_post( wpautop( $instance['content'] ) ); ?> 
		<?php endif; ?>

		<?php if ( ! empty( $instance['btn'] ) ) : ?>
			<?php
				$btn_style = 'height: ' . esc_attr( $instance['height'] ) . 'px;color: ' . esc_attr( $instance['font_color'] );
				$target    = $instance['target'] == '_blank' ? 'target="' . esc_attr( $instance['target'] ) . '" rel="noopener"' : 'target="' . esc_attr( $instance['target'] ) . '"';
			?>

			<ul class="btn-widget-ul">
				<?php foreach ( $instance['btn'] as $item ) : ?>
					<li class="btn-widget-li <?php echo esc_attr( 'btn-column-' . $instance['column'] ); ?>">
						<a class="btn-widget-item boxs1" style="background-color: 
						<?php
						if ( ! $item['img'] ) {
							?>
							<?php echo esc_attr( $item['bg_color'] ); ?>
							<?php
						} else {
							?>
							var(--be-bg-white)<?php } ?>;background-image: url(<?php echo esc_html( $item['img'] ); ?>);<?php echo $btn_style; ?>" href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $target; ?> >
						<span class="btn-widget-content">
							<?php
							if ( $item['icon'] ) {
								?>
								<i class="<?php echo esc_html( $item['icon'] ); ?>"></i><?php } ?>
							<?php
							if ( $item['text'] ) {
								?>
								<span class="btn-widget-title"><?php echo esc_html( $item['text'] ); ?></span><?php } ?>
						</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// $instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		$instance['content'] = isset( $new_instance['content'] ) ? wp_kses_post( $new_instance['content'] ) : '';
		if ( is_null( $instance['content'] ) ) {
			$instance['content'] = '';
		}
		$instance['column']     = $new_instance['column'];
		$instance['height']     = absint( $new_instance['height'] );
		$instance['font_color'] = $new_instance['font_color'];
		$instance['target']     = $new_instance['target'];
		$instance['btn']        = array();
		if ( ! empty( $new_instance['btn_icon'] ) ) {
			$protocols   = wp_allowed_protocols();
			$protocols[] = 'skype';
			for ( $i = 0; $i < ( count( $new_instance['btn_icon'] ) - 1 ); $i++ ) {
				$temp              = array(
					'icon'     => $new_instance['btn_icon'][ $i ],
					'text'     => $new_instance['btn_text'][ $i ],
					'img'      => $new_instance['btn_img'][ $i ],
					'url'      => esc_url( $new_instance['btn_url'][ $i ], $protocols ),
					'bg_color' => $new_instance['btn_bg_color'][ $i ],
				);
				$instance['btn'][] = $temp;
			}
		}
		return $instance;
	}

	function form( $instance ) {
		$instance               = wp_parse_args( (array) $instance, $this->defaults );
		$instance['title']      = isset( $instance['title'] ) && is_string( $instance['title'] ) ? $instance['title'] : '';
		$instance['content']    = isset( $instance['content'] ) && is_string( $instance['content'] ) ? $instance['content'] : '';
		$instance['column']     = isset( $instance['column'] ) && is_string( $instance['column'] ) ? $instance['column'] : '2';
		$instance['height']     = isset( $instance['height'] ) ? absint( $instance['height'] ) : 80;
		$instance['font_color'] = isset( $instance['font_color'] ) && is_string( $instance['font_color'] ) ? $instance['font_color'] : '#fff';
		$instance['target']     = isset( $instance['target'] ) && is_string( $instance['target'] ) ? $instance['target'] : '_blank';
		$instance['btn']        = is_array( $instance['btn'] ) ? $instance['btn'] : array();
		?>
		<!--
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">标题：</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		-->
		<p style="display: block; margin-bottom: 15px;">
			<span class="btn-option-label btn-option-fl">分栏</span>
			<span class="btn-option-radio-wrapper">
				<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'column' ) ); ?>" value="1" <?php checked( $instance['column'], '1' ); ?>/>1栏</label>
				<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'column' ) ); ?>" value="2" <?php checked( $instance['column'], '2' ); ?>/>2栏</label>
				<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'column' ) ); ?>" value="3" <?php checked( $instance['column'], '3' ); ?>/>3栏</label>
				<label class="btn-option-radio"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'column' ) ); ?>" value="4" <?php checked( $instance['column'], '4' ); ?>/>4栏</label>
			</span>
		</p>

		<p>
			<label class="btn-option-label" for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>">高度</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" value="<?php echo absint( $instance['height'] ); ?>" class="small-text" /> px
		</p>

		<p>
			<label class="btn-option-label" for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>">打开方式</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>">
				<option value="_blank" <?php selected( '_blank', $instance['target'] ); ?>>新窗口</option>
				<option value="_self" <?php selected( '_self', $instance['target'] ); ?>>同一窗</option>
			</select>
		</p>

		<p>
			<label class="btn-option-label" for="<?php echo esc_attr( $this->get_field_id( 'font_color' ) ); ?>">文字颜色</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'font_color' ) ); ?>" class="color-picker" type="text" name="<?php echo esc_attr( $this->get_field_name( 'font_color' ) ); ?>" value="<?php echo $instance['font_color']; ?>" />
		</p>

		<!--
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>">说明：</label>
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" rows="2" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" class="widefat"><?php echo wp_kses_post( $instance['content'] ); ?></textarea>
		</p>
		-->
		<h4 class="btn-icons-title">添加按钮 </h4>

		<ul class="btn_container btn-sortable">
			<?php foreach ( $instance['btn'] as $link ) : ?>
				<li>
					<?php $this->draw_btn( $this, $link ); ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<p>
			<a href="#" class="add_icobtn button">添加</a>
		</p>

		<div class="icobtn_clone" style="display:none">
			<?php $this->draw_btn( $this ); ?>
		</div>
		<?php
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".color-picker").wpColorPicker();
			});
		</script>
		<?php
	}

	function draw_btn( $widget, $selected = array(
		'icon'     => '',
		'url'      => '',
		'text'     => '',
		'url'      => '',
		'img'      => '',
		'bg_color' => '#c40000',
	) ) {
		?>
		<label class="btn-sw-icon">标题文字</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_text' ) ); ?>[]" value="<?php echo esc_attr( $selected['text'] ); ?>" style="width: 100%;margin: 5px 0;" />
		<label class="btn-sw-icon">链接地址</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_url' ) ); ?>[]" value="<?php echo esc_attr( $selected['url'] ); ?>" style="width: 100%;margin: 5px 0;" />
		<label class="btn-sw-icon">图标代码（可选）</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_icon' ) ); ?>[]" value="<?php echo esc_attr( $selected['icon'] ); ?>" style="width: 100%;margin: 5px 0;" />
		<label class="btn-sw-icon">背景图片（可选）</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_img' ) ); ?>[]" value="<?php echo esc_attr( $selected['img'] ); ?>" style="width: 80%;margin: 5px 0;" />
		<input class="widgets-upload-btn button" type="button" value="选择" style="width: 18%;margin: 5px 0;"/>
		<label class="btn-sw-icon" style="margin: 0 0 5px 0;">背景颜色（保存后选择）</label>
		<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'btn_bg_color' ) ); ?>[]" class="color-picker" value="<?php echo esc_attr( $selected['bg_color'] ); ?>" style="width: 100%;" />
		<span class="remove-icobtn dashicons dashicons-no-alt"></span>
		<?php
	}
}

function be_ico_btn_widget_init() {
	register_widget( 'BE_ICO_BTN_Widget' );
}
add_action( 'widgets_init', 'be_ico_btn_widget_init' );
