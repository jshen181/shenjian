<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ajax 日历
class be_ajax_calendar extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'ajax_calendar',
			'description' =>'Ajax方式显示更新日历',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'ajax_calendar', '更新日历', $widget_ops );
	}


	function widget( $args, $instance ) {
		extract($args);
		$defaults = array(
			'title'      => '更新日历',
			'start_year' => '2018',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$bengali= '0';
		$title_w = title_i_w();
		echo $before_widget;
		if ( $title )
			echo $before_title . $title_w . $title . $after_title;
			echo $this->calender_html($bengali,$instance["start_year"]);
		?>

		<?php
			echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['start_year'] = strip_tags( $new_instance['start_year'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'      => '更新日历',
			'start_year' => '2018',
		);

		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'start_year' ); ?>">起始年份</label>
			<input class="widefat" type="number"  id="<?php echo $this->get_field_id( 'start_year' ); ?>" name="<?php echo $this->get_field_name( 'start_year' ); ?>" value="<?php echo $instance['start_year']; ?>" />
		</p>
		<p>如显示的日期不准，尝试将常规设置中的时区，改为上海</p>
	<?php }

	function calender_html( $bengali, $start_year ) {
		global $wp_locale, $m, $monthnum, $year;
		$calender_html = '';
		$calender_html .= '<div class="ajax-ca-box"><div class="select-bg"></div>';
		$calender_html .= '<div class="ajax-ca">';
		$calender_html .= '<div class="select-ca">';
		$calender_html .= '<select name="month" class="my_month s-veil">';

		$month = array();
		for ( $i = 1; $i <= 12; $i++ ) {
			$monthnums = zeroise( $i, 2 );
			$month[$monthnums] = $wp_locale->get_month( $i );
		}

		if ( empty( $m ) || $m == '' ) {
			$nowm = $monthnum;
			$nowyear = $year;
			if( $monthnum==0 || $monthnum==null ){
				$nowm=date( 'm' );
			}
			if( $nowyear==0 || $nowyear==null ){
				$nowyear=date( 'Y' );
			}
		} else {
			$mmm = str_split( $m, 2 );
			$nowm = zeroise( intval( substr( $m, 4, 2 ) ), 2 );
			$nowyear = $mmm['0'] . $mmm['1'];
		}

		foreach ( $month as $k => $mu ) {
			if ( $k == $nowm ) {
				$calender_html.= '<option value="' . $k . '" selected="selected">' . $mu . '</option>';
			} else {
				$calender_html.= '<option value="' . $k . '">' . $mu . '</option>';
			}
		}
		$calender_html.= '</select>';

		$find     = array( "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
		$taryear  = date( "Y" );
		$yeararr  = array();
		$lassyear = $start_year;
		for ( $nowyearrr = $lassyear; $nowyearrr <= $taryear; $nowyearrr++ ) {
			$yeararr[$nowyearrr] = $nowyearrr;
		}

		$calender_html .= '<select name="Year" class="my_year s-veil">';

		foreach ( $yeararr as $k => $years ) {
			if ( $k == $nowyear ) {
				$calender_html .= '<option value="' . $k . '" selected="selected">' . $years . '' . sprintf( __( '年', 'begin' ) ) . '</option>';
			} else {
				$calender_html .= '<option value="' . $k . '">' . $years . '' . sprintf( __( '年', 'begin' ) ) . '</option>';
			}
		}
		$calender_html .= '</select>';

		$calender_html .= '</div>';
		$calender_html .= '<div class="clear"></div>';
		$calender_html .= '<div class="ajax-calendar">';
		$calender_html .= '<div class="be-calender">';
		$calender_html .= ajax_ac_calendar('', $bengali,false);
		$calender_html .= '</div>';
		$calender_html .= '<div class="clear"></div>';
		$calender_html .= '</div>';
		$calender_html .='</div>';
		$calender_html .='</div>';
		return $calender_html;
	}
}

add_action( 'wp_ajax_ajax_ac', 'ajax_ac_callback' );
add_action( 'wp_ajax_nopriv_ajax_ac', 'ajax_ac_callback' );

function ajax_ac_callback() {
	$ma = $_GET['ma'];
	$bn = $_GET['bn'];  
	ajax_ac_calendar( $bn, $ma );
	die();
}

function ajax_ac_calendar( $bn, $ma = null, $echo = true ) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;
	if($ma!=null){
		$m=$ma;
	}

	$cache = array();
	$key = md5( get_locale() . $m . $monthnum . $year );

	if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) ) {
		if ( is_array( $cache ) && isset( $cache[$key] ) ) {
			if ( $echo ) {
				echo apply_filters( 'get_calendar', $cache[$key] );
				return;
			} else {
				return apply_filters( 'get_calendar', $cache[$key] );
			}
		}
	}

	if ( ! is_array( $cache ) )
		$cache = array();

	if ( ! $posts ) {
		$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 1" );
		if ( ! $gotsome ) {
			$cache[$key] = '';
			wp_cache_set( 'get_calendar', $cache, 'calendar' );
			return;
		}
	}

	if ( isset( $_GET['w'] ) )
		$w = '' . intval( $_GET['w'] );

	$week_begins = intval( get_option( 'start_of_week' ) );

	if ( ! empty( $monthnum ) && ! empty( $year ) ) {
		$thismonth = '' . zeroise( intval( $monthnum ), 2);
		$thisyear = '' . intval( $year );
	} elseif ( ! empty( $w ) ) {
		$thisyear = '' . intval( substr( $m, 0, 4 ) );
		$d = ( ($w - 1 ) * 7 ) + 6;
		$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT( ( DATE_ADD( '{$thisyear}0101', INTERVAL $d DAY ) ), '%m' )" );
	} elseif ( ! empty( $m ) ) {
		$thisyear = '' . intval( substr( $m, 0, 4 ) );
		if ( strlen($m) < 6 )
			$thismonth = '01';
		else
			$thismonth = '' . zeroise( intval( substr( $m, 4, 2 ) ), 2 );
	} else {
		$thisyear  = gmdate( 'Y', current_time( 'timestamp' ) );
		$thismonth = gmdate( 'm', current_time( 'timestamp' ) );
	}

	$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );
	$last_day  = date( 't', $unixmonth );

	$calendar_output = '<table class="be-calendar-area"><thead><tr>';
	$myweek = array();

	for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
	}

	foreach ( $myweek as $wd ) {
		$day_name = $wp_locale->get_weekday_abbrev($wd);
		$wd = esc_attr( $wd );
		$calendar_output .= "\n\t\t<th class=\"$day_name\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '</tr></thead><tbody><tr>';

    // 有文章
	$dayswithposts = get_posts(array(
		'suppress_filters' => false,
		//'post_type'      => 'post',
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'monthnum'         => $thismonth,
		'year'             => $thisyear,
		'numberposts'      => -1,
	));

	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos( $_SERVER['HTTP_USER_AGENT'], 'camino' ) !== false || stripos( $_SERVER['HTTP_USER_AGENT'], 'safari' ) !== false )
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$daywithpost = array();
	$ak_titles_for_day = array();
	if ( $dayswithposts ) {
		foreach ( ( array ) $dayswithposts as $ak_post ) {
			$daywith = date( 'd', strtotime( $ak_post->post_date ) );
			if ( ! in_array( $daywith, $daywithpost ) ) {
				$daywithpost[] = $daywith;
			}
			$post_title = esc_attr( get_the_title( $ak_post ) );
			if ( empty( $ak_titles_for_day[$daywith] ) ) // first one
				$ak_titles_for_day[$daywith] = $post_title;
			else
				$ak_titles_for_day[$daywith] .= $ak_title_separator . $post_title;
		}
	}

	//print_r($daywithpost);
	//print_r($ak_titles_for_day);
	$pad = calendar_week_mod(date('w', $unixmonth) - $week_begins);
	if ( 0 != $pad )
		$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr($pad) . '" class="pad">&nbsp;</td>';
		$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {

		$dayrrr = array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31', );
		$addzeor=array( '1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31', );

		if ( isset( $newrow ) && $newrow )
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;
		if ( $day == gmdate('j', current_time( 'timestamp' ) ) && $thismonth == gmdate( 'm', current_time( 'timestamp' ) ) && $thisyear == gmdate( 'Y', current_time( 'timestamp' ) ) )
			$calendar_output .= '<td class="today"  >';
		else
		$calendar_output .= '<td>';

		// number
		$number = get_posts(array(
			'post_status' => 'publish',
			'date_query'    => array(
				'year'  => $thisyear,
				'month' => $thismonth,
				'day'   => $day,
			)
		));

		if ( in_array( $day, $daywithpost ) )
			// $calendar_output .= '<a href="' . get_day_link( $thisyear, $thismonth, $day ) . '" title="' . esc_attr( $ak_titles_for_day[$addzeor[$day]] ) . "\">$dayrrr[$day]</a>";
			$calendar_output .= '<a href="' . get_day_link( $thisyear, $thismonth, $day ) . '" data-hover="' . count( $number ) . '&nbsp;' . sprintf( __( '篇', 'begin' ) ) . '">' . $dayrrr[$day] . '</a>';
		else
			$calendar_output .= '<span class="notpost">' . $dayrrr[$day] . '</span>';
		$calendar_output .= '</td>';
		if ( 6 == calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
	if ( $pad != 0 && $pad != 7 )
		$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr($pad) . '">&nbsp;</td>';
	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";
	$cache[$key] = $calendar_output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );
	if ( $echo )
		echo apply_filters('get_calendar', $calendar_output);
	else
		return apply_filters('get_calendar', $calendar_output);
}

add_action( 'widgets_init', 'be_ajax_calendar_int' );
function be_ajax_calendar_int() {
	register_widget( 'be_ajax_calendar' );
}