<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 获取字符串的首字母
function getFirstCharter( $str ) {
	if ( empty( $str ) ) {
		return '';
	}

	if ( is_numeric( $str[0] ) ) {
		return $str[0];
	}

	$fchar = ord( $str[0] );
	if ( $fchar >= ord( 'A' ) && $fchar <= ord( 'z' ) ) {
		return strtoupper( $str[0] );
	}

	try {
		$s1 = iconv( 'UTF-8', 'GB2312//IGNORE//TRANSLIT', $str );
		if ( $s1 === false ) {
			return '';
		}

		$s2 = iconv( 'GB2312', 'UTF-8//IGNORE//TRANSLIT', $s1 );
		if ( $s2 === false ) {
			return '';
		}

		$s = ( $s2 == $str ) ? $s1 : $str;

		if ( strlen( $s ) < 2 ) {
			return '';
		}

		$asc = ord( $s[0] ) * 256 + ord( $s[1] ) - 65536;

		$ranges = array(
			'A' => array( -20319, -20284 ),
			'B' => array( -20283, -19776 ),
			'C' => array( -19775, -19219 ),
			'D' => array( -19218, -18711 ),
			'E' => array( -18710, -18527 ),
			'F' => array( -18526, -18240 ),
			'G' => array( -18239, -17923 ),
			'H' => array( -17922, -17418 ),
			'J' => array( -17417, -16475 ),
			'K' => array( -16474, -16213 ),
			'L' => array( -16212, -15641 ),
			'M' => array( -15640, -15166 ),
			'N' => array( -15165, -14923 ),
			'O' => array( -14922, -14915 ),
			'P' => array( -14914, -14631 ),
			'Q' => array( -14630, -14150 ),
			'R' => array( -14149, -14091 ),
			'S' => array( -14090, -13319 ),
			'T' => array( -13318, -12839 ),
			'W' => array( -12838, -12557 ),
			'X' => array( -12556, -11848 ),
			'Y' => array( -11847, -11056 ),
			'Z' => array( -11055, -10247 ),
		);

		foreach ( $ranges as $letter => $range ) {
			if ( $asc >= $range[0] && $asc <= $range[1] ) {
				return $letter;
			}
		}
	} catch ( Exception $e ) {
		return '';
	}

	return '';
}
