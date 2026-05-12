<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function specs_getfirstchar( $s0 ) {
	// 参数验证
	if ( empty( $s0 ) || ! is_string( $s0 ) ) {
		return null;
	}

	$fchar = ord( $s0[0] );
	if ( $fchar >= ord( 'A' ) and $fchar <= ord( 'z' ) ) {
		return strtoupper( $s0[0] );
	}

	// 方案1：使用mb_convert_encoding替代iconv（通常更稳定）
	$s1 = mb_convert_encoding( $s0, 'GB2312', 'UTF-8' );
	$s2 = mb_convert_encoding( $s1, 'UTF-8', 'GB2312' );

	if ( $s2 == $s0 ) {
		$s = $s1;
	} else {
		$s = $s0;}

	// 确保字符串长度足够
	if ( strlen( $s ) < 2 ) {
		return strtoupper( $s0[0] );
	}

	$asc = ord( $s[0] ) * 256 + ord( $s[1] ) - 65536;
	if ( $asc >= -20319 and $asc <= -20284 ) {
		return 'A';
	}
	if ( $asc >= -20283 and $asc <= -19776 ) {
		return 'B';
	}
	if ( $asc >= -19775 and $asc <= -19219 ) {
		return 'C';
	}
	if ( $asc >= -19218 and $asc <= -18711 ) {
		return 'D';
	}
	if ( $asc >= -18710 and $asc <= -18527 ) {
		return 'E';
	}
	if ( $asc >= -18526 and $asc <= -18240 ) {
		return 'F';
	}
	if ( $asc >= -18239 and $asc <= -17923 ) {
		return 'G';
	}
	if ( $asc >= -17922 and $asc <= -17418 ) {
		return 'H';
	}
	if ( $asc >= -17417 and $asc <= -16475 ) {
		return 'J';
	}
	if ( $asc >= -16474 and $asc <= -16213 ) {
		return 'K';
	}
	if ( $asc >= -16212 and $asc <= -15641 ) {
		return 'L';
	}
	if ( $asc >= -15640 and $asc <= -15166 ) {
		return 'M';
	}
	if ( $asc >= -15165 and $asc <= -14923 ) {
		return 'N';
	}
	if ( $asc >= -14922 and $asc <= -14915 ) {
		return 'O';
	}
	if ( $asc >= -14914 and $asc <= -14631 ) {
		return 'P';
	}
	if ( $asc >= -14630 and $asc <= -14150 ) {
		return 'Q';
	}
	if ( $asc >= -14149 and $asc <= -14091 ) {
		return 'R';
	}
	if ( $asc >= -14090 and $asc <= -13319 ) {
		return 'S';
	}
	if ( $asc >= -13318 and $asc <= -12839 ) {
		return 'T';
	}
	if ( $asc >= -12838 and $asc <= -12557 ) {
		return 'W';
	}
	if ( $asc >= -12556 and $asc <= -11848 ) {
		return 'X';
	}
	if ( $asc >= -11847 and $asc <= -11056 ) {
		return 'Y';
	}
	if ( $asc >= -11055 and $asc <= -10247 ) {
		return 'Z';
	}
	return null;
}

function specs_pinyin( $zh ) {
	// 参数验证
	if ( empty( $zh ) || ! is_string( $zh ) ) {
		return '';
	}

	$ret = '';
	$i   = 0;

	// 使用mb_convert_encoding替代iconv，通常更稳定且错误处理更好
	$s1 = mb_convert_encoding( $zh, 'GB2312', 'UTF-8' );
	$s2 = mb_convert_encoding( $s1, 'UTF-8', 'GB2312' );

	if ( $s2 == $zh ) {
		$zh = $s1;}

	// 确保字符串不为空
	if ( strlen( $zh ) == 0 ) {
		return '';
	}

	$s1 = substr( $zh, $i, 1 );
	$p  = ord( $s1 );
	if ( $p > 160 ) {
		// 确保有足够的字符可以读取
		if ( strlen( $zh ) > $i + 1 ) {
			$s2     = substr( $zh, $i++, 2 );
			$result = specs_getfirstchar( $s2 );
			$ret   .= $result ? $result : '';
		}
	} else {
		$ret .= $s1;
	}
	return strtoupper( $ret );
}

function specs_show_tags() {
	if ( ! $output = get_option( 'specs_tags_list' ) ) {
		$categories = get_terms(
			'post_tag',
			array(
				'orderby'    => 'count',
				'hide_empty' => 1,
			)
		);
		$r          = array();
		foreach ( $categories as $v ) {
			for ( $i = 65; $i <= 90; $i++ ) {
				if ( specs_pinyin( $v->name ) == chr( $i ) ) {
					$r[ chr( $i ) ][] = $v;
				}
			}
			for ( $i = 48;$i <= 57;$i++ ) {
				if ( specs_pinyin( $v->name ) == chr( $i ) ) {
					$r[ chr( $i ) ][] = $v;
				}
			}
		}
		ksort( $r );
		$output = "<div class='list-inline-box' data-aos='fade-in'><ul class='list-inline' id='series-letter'>";
		for ( $i = 65;$i <= 90;$i++ ) {
			$tagi = @$r[ chr( $i ) ];
			if ( is_array( $tagi ) ) {
				$output .= "<li><a href='#" . chr( $i ) . "'>" . chr( $i ) . '</a></li>';
			} else {
				$output .= '<li>' . chr( $i ) . '</li>';
			}
		}
		for ( $i = 48;$i <= 57;$i++ ) {
			$tagi = @$r[ chr( $i ) ];
			if ( is_array( $tagi ) ) {
				$output .= "<li><a href='#" . chr( $i ) . "'>" . chr( $i ) . '</a></li>';
			} else {
				$output .= '<li>' . chr( $i ) . '</li>';
			}
		}
		$output .= "<div class='clear'></div></ul></div>";
		$output .= "<div class='clear'></div><ul id='all-series' class='list-unstyled'>";
		for ( $i = 65;$i <= 90;$i++ ) {
			$tagi = @$r[ chr( $i ) ];
			if ( is_array( $tagi ) ) {
				$output .= "<li id='" . chr( $i ) . "'><h4 class='series-name'>" . chr( $i ) . '</h4>';
				$output .= "<span class='series-link'>";
				foreach ( $tagi as $tag ) {
					$output .= "<a href='" . get_tag_link( $tag->term_id ) . "'>" . $tag->name . '<sup>' . $tag->count . '</sup></a>';
				}
				$output .= '</span>';
			}
		}
		for ( $i = 48;$i <= 57;$i++ ) {
			$tagi = @$r[ chr( $i ) ];
			if ( is_array( $tagi ) ) {
				$output .= "<li id='" . chr( $i ) . "'><h4 class='series-name' data-aos='fade-in'>" . chr( $i ) . '</h4>';
				$output .= "<span class='series-link'>";
				foreach ( $tagi as $tag ) {
					$output .= "<a href='" . get_tag_link( $tag->term_id ) . "' data-aos='fade-in'>" . $tag->name . '<sup>' . $tag->count . '</sup></a>';
				}
				$output .= '</span>';
			}
		}
		$output .= '</ul>';
		update_option( 'specs_tags_list', $output );
	}
	echo $output;
}

function clear_tags_cache() {
	update_option( 'specs_tags_list', '' );
}
add_action( 'save_post', 'clear_tags_cache' );

function specs_show() {
	// 获取并验证排除选项
	$letter_exclude = zm_get_option( 'letter_exclude' );
	$exclude_ids    = array();
	if ( ! empty( $letter_exclude ) && is_string( $letter_exclude ) ) {
		$exclude_ids = explode( ',', $letter_exclude );
	}

	if ( ! zm_get_option( 'letter_show_md' ) || ( zm_get_option( 'letter_show_md' ) == 'letter_cat' ) ) {
		$categories = get_terms(
			array( 'category' ),
			array(
				'exclude'    => $exclude_ids,
				'orderby'    => 'count',
				'hide_empty' => 1,
			)
		);
	}
	if ( zm_get_option( 'letter_show_md' ) == 'letter_tag' ) {
		$categories = get_terms(
			array( 'post_tag' ),
			array(
				'exclude'    => $exclude_ids,
				'orderby'    => 'count',
				'hide_empty' => 1,
			)
		);
	}
	$r = array();
	foreach ( $categories as $v ) {
		for ( $i = 65; $i <= 90; $i++ ) {
			if ( specs_pinyin( $v->name ) == chr( $i ) ) {
				$r[ chr( $i ) ][] = $v;
			}
		}
		for ( $i = 48;$i <= 57;$i++ ) {
			if ( specs_pinyin( $v->name ) == chr( $i ) ) {
				$r[ chr( $i ) ][] = $v;
			}
		}
	}
	ksort( $r );
	$output = "<div class='list-inline-box' data-aos='fade-in'><ul class='list-inline' id='series-letter'>";
	for ( $i = 65;$i <= 90;$i++ ) {
		$tagi = @$r[ chr( $i ) ];
		if ( is_array( $tagi ) ) {
			$output .= "<li><a href='#" . chr( $i ) . "'>" . chr( $i ) . '</a></li>';
		} else {
			$output .= '<li>' . chr( $i ) . '</li>';
		}
	}
	for ( $i = 48;$i <= 57;$i++ ) {
		$tagi = @$r[ chr( $i ) ];
		if ( is_array( $tagi ) ) {
			$output .= "<li><a href='#" . chr( $i ) . "'>" . chr( $i ) . '</a></li>';
		} else {
			$output .= '<li>' . chr( $i ) . '</li>';
		}
	}
	$output .= "<div class='clear'></div></ul></div>";
	$output .= "<div class='clear'></div><ul id='all-series' class='list-unstyled'>";
	for ( $i = 65;$i <= 90;$i++ ) {
		$tagi = @$r[ chr( $i ) ];
		if ( is_array( $tagi ) ) {
			$output .= "<li id='" . chr( $i ) . "'><h4 class='series-name' data-aos='fade-in'>" . chr( $i ) . '</h4>';
			$output .= "<span class='series-link'>";
			foreach ( $tagi as $tag ) {
				$output .= "<a href='" . get_tag_link( $tag->term_id ) . "' data-aos='fade-in'>" . $tag->name . '</a>';
			}
			$output .= '</span>';
		}
	}
	for ( $i = 48;$i <= 57;$i++ ) {
		$tagi = @$r[ chr( $i ) ];
		if ( is_array( $tagi ) ) {
			$output .= "<li id='" . chr( $i ) . "'><h4 class='series-name'>" . chr( $i ) . '</h4>';
			$output .= "<span class='series-link'>";
			foreach ( $tagi as $tag ) {
				$output .= "<a href='" . get_tag_link( $tag->term_id ) . "'>" . $tag->name . '</a>';
			}
			$output .= '</span>';
		}
	}
	$output .= '</ul>';
	echo $output;
}

function specs_post_count_by_tag( $arg, $type = 'include' ) {
	$args = array(
		$type => $arg,
	);
	$tags = get_tags( $args );
	if ( $tags ) {
		foreach ( $tags as $tag ) {
			return $tag->count;
		}
	}
}
