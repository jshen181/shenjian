<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 音频
class Be_AudioIgniter_Playlist_Widget extends WP_Widget {
	protected $defaults = array(
		'title'    => '',
		'playlist' => '',
	);

	public function __construct() {
		$widget_ops  = array( 'description' => esc_html__( '音频播放器', 'begin' ) );
		$control_ops = array();
		parent::__construct( 'be_audioigniter_playlist', $name = esc_html__( '音频播放器', 'begin' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$playlist = $instance['playlist'];

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$playlist = intval( $playlist );
		$post     = get_post( $playlist );

		if ( ! empty( $post ) && BeAudioIgniter()->post_type === $post->post_type ) {
			echo do_shortcode(
				sprintf( '[ai_playlist id="%s"]',
					$playlist
				)
			);
		}

		echo $args['after_widget'];

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['playlist'] = intval( $new_instance['playlist'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title    = $instance['title'];
		$playlist = $instance['playlist'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( '标题', 'begin' ); ?>：
			</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'playlist' ) ); ?>">
				<?php esc_html_e( '选择音频', 'begin' ); ?>：
			</label>
			<?php
				$this->dropdown_posts( array(
					'post_type'            => BeAudioIgniter()->post_type,
					'selected'             => $playlist,
					'class'                => 'widefat posts_dropdown',
					'show_option_none'     => '选择音频',
					'select_even_if_empty' => true,
				), $this->get_field_name( 'playlist' ) );
			?>
		</p>
		<?php
	}

	public function dropdown_posts( $args = '', $name = 'post_id' ) {
		$defaults = array(
			'depth'                 => 0,
			'post_parent'           => 0,
			'selected'              => 0,
			'echo'                  => 1,
			//'name'                => 'page_id', // With this line, get_posts() doesn't work properly.
			'id'                    => '',
			'class'                 => '',
			'show_option_none'      => '',
			'show_option_no_change' => '',
			'option_none_value'     => '',
			'post_type'             => 'post',
			'post_status'           => 'publish',
			'suppress_filters'      => false,
			'numberposts'           => -1,
			'select_even_if_empty'  => false, // If no posts are found, an empty <select> will be returned/echoed.
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
		if ( in_array( $r['post_type'], $hierarchical_post_types, true ) ) {
			$pages = get_pages( $r );
		} else {
			$pages = get_posts( $r );
		}

		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty( $id ) ) {
			$id = $name;
		}

		if ( ! empty( $pages ) || $select_even_if_empty == true ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "' class='" . esc_attr( $class ) . "'>\n";
			if ( $show_option_no_change ) {
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			}
			if ( $show_option_none ) {
				$output .= "\t<option value=\"" . esc_attr( $option_none_value ) . "\">$show_option_none</option>\n";
			}
			if ( ! empty( $pages ) ) {
				$output .= walk_page_dropdown_tree( $pages, $depth, $r );
			}
			$output .= "</select>\n";
		}

		$output = apply_filters( 'audioigniter_playlist_widget_dropdown_posts', $output, $name, $r );

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}
}

function register_beaudioigniter_playlist_widget() {
	register_widget( 'Be_AudioIgniter_Playlist_Widget' );
}
if ( zm_get_option( 'be_audio' ) ) {
add_action( 'widgets_init', 'register_beaudioigniter_playlist_widget' );
}