<?php
define( 'WP_KEYWORDLINK_OPTION', 'wp_keywordlinkoption' );
define( 'WP_GLOBAL_OPTION', 'wp_global_option' );
define( 'WP_PAGELIMIT_OPTION', 'wp_pagelimit_option' );

function wp_be_keywordlink_admininit() {
	add_management_page( '关键词', "<span class='bem'></span>关键词", 'manage_options', 'keywordlink', 'wp_be_keywordlink_optionpage' );
}

function wp_be_keywordlink_topbarmessage( $msg ) {
	echo '<div class="updated fade" id="message"><p>' . $msg . '</p></div>';
}

function wp_be_keywordlink_showdefinitions() {
	$links = get_option( WP_KEYWORDLINK_OPTION );
	// 显示条数和搜索
	if ( $links ) {
		$wp_pagelimit = get_option( WP_PAGELIMIT_OPTION );
		if ( $wp_pagelimit ) {
			$page_limit = $wp_pagelimit;
		} else {
			$page_limit = '50';
		}
		$page_num = 1;
		for ( $j = 1;$j <= count( $links );$j += $page_limit ) {
			++$page_num;
		}

		if ( isset( $_POST['wp_page'] ) && $_POST['wp_page'] ) {
			$wp_page = $_POST['wp_page'];
		} else {
			$wp_page = 1;
		}
		?>

		<p>
			<form name=wp_page_form id=wp_page_form method="post" action="">
				每页显示 
				<input type="number" name=page_limit value=<?php echo $page_limit; ?> size=3 />
				<input type="submit" class="button" value="保存" />

				<span class="search-k">
					<input type="text" name=search_k value="" size=18 />
					<input type="submit" id="search-submit" class="button" value="搜索">
				</span>
				<span style="margin-left: 10px;"><?php echo count($links); ?> 条</span>
			</form>
		</p>

		<?php
		if ( isset( $_POST['search_k'] ) && $_POST['search_k'] ) {
		} else {
			$links = array_slice( $links, ( $wp_page - 1 ) * $page_limit, $page_limit );
		}

		echo "<table class='wp-list-table widefat keyword-link-list'>\n";
		echo "<thead><tr><th>关键词</th><th>链接</th><th>属性</th><th style='width: 110px;'>操作</th></tr></thead>\n";
		$cnt = ( $wp_page - 1 ) * $page_limit;
		if ( isset( $_POST['search_k'] ) && $_POST['search_k'] ) {
			$cnt = 0; }
		foreach ( $links as $keyword => $details ) {
			list($link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc) = explode( '|', $details );
			$cleankeyword = stripslashes( $keyword );
			if ( ! $desc ) {
				$desc = $cleankeyword; }
			$desc = stripslashes( $desc );

			if ( isset( $_POST['search_k'] ) && $_POST['search_k'] ) {
				$search_k = $_POST['search_k'];
				if ( preg_match( "/$search_k/Ui", $cleankeyword ) ) {
					if ( $cnt++ % 2 ) {
						echo '<tr class=alternate>';
					} else {
						echo '<tr>';
					}
					echo "<td><a title=\"$desc\">$cleankeyword</a></td><td><a href='$link'>";
					echo '</a></td>';

					echo '<td>';
					if ( $zh_CN ) {
						echo ' 中文 ';
					}
					if ( $newwindow ) {
						echo ' | 新窗口打开';
					}
					if ( $firstonly ) {
						echo ' | 仅匹配一个';
					}
					if ( $nofollow ) {
						echo ' | Nofollow属性';
					}
					if ( $docomments ) {
						echo ' | 匹配评论';
					}
					if ( $ignorecase ) {
						echo ' | 忽略大小写';
					}
					if ( $isaffiliate ) {
						echo ' | 内链';
					}
					echo '</td>';

					$urlsave_keyword = $keyword;
					$urlsave_url     = $link;
					$urlsave_desc    = $desc;

					echo '<td>';
					echo "<input class='action-button' type=button value=编辑 onClick=\"javascript:WPEditKeyword('$urlsave_keyword','$urlsave_url',";
					echo ( ( $nofollow == '0' ) ? '0' : '1' ) . ',' . ( ( $firstonly == '0' ) ? '0' : '1' ) . ',' . ( ( $newwindow == '0' ) ? '0' : '1' ) . ',' . ( ( $ignorecase == '0' ) ? '0' : '1' ) . ',' . ( ( $isaffiliate == '0' ) ? '0' : '1' ) . ',' . ( ( $docomments == '0' ) ? '0' : '1' ) . ',' . ( ( $zh_CN == '0' ) ? '0' : '1' ) . ');"/>';
					echo "<input class='action-button' type=button value=删除 onClick=\"javascript:WPDeleteKeyword('$urlsave_keyword');\" />\n";
					echo '</td>';
					echo "</tr>\n";
				}
			} else {
				if ( $cnt++ % 2 ) {
					echo '<tr class=alternate>';
				} else {
					echo '<tr>';
				}
				echo "<td>$cleankeyword</td><td><a href='$link' target='_blank'>";
				echo wp_trim_words( $link, 7, '...' );
				echo '</a></td>';

				/* show attributes */
				echo '<td>';
				if ( $zh_CN ) {
					echo ' 中文 ';
				}
				if ( $newwindow ) {
					echo ' | 新窗口打开';
				}
				if ( $firstonly ) {
					echo ' | 仅匹配一个';
				}
				if ( $nofollow ) {
					echo ' | Nofollow属性';
				}
				if ( $docomments ) {
					echo ' | 匹配评论';
				}
				if ( $ignorecase ) {
					echo ' | 忽略大小写';
				}
				if ( $isaffiliate ) {
					echo ' | 内链';
				}
				echo '</td>';

				$urlsave_keyword = $keyword;
				$urlsave_url     = $link;
				$urlsave_desc    = $desc;

				echo '<td>';
				echo "<input type=button class='action-button' value=编辑 onClick=\"javascript:WPEditKeyword('$urlsave_keyword','$urlsave_url','$urlsave_desc',";
				echo ( ( $nofollow == '0' ) ? '0' : '1' ) . ',' . ( ( $firstonly == '0' ) ? '0' : '1' ) . ',' . ( ( $newwindow == '0' ) ? '0' : '1' ) . ',' . ( ( $ignorecase == '0' ) ? '0' : '1' ) . ',' . ( ( $isaffiliate == '0' ) ? '0' : '1' ) . ',' . ( ( $docomments == '0' ) ? '0' : '1' ) . ',' . ( ( $zh_CN == '0' ) ? '0' : '1' ) . ');"/>';
				echo "<input type=button class='action-button' value=删除 onClick=\"javascript:WPDeleteKeyword('$urlsave_keyword');\" />\n";
				echo '</td>';
				echo "</tr>\n";
			}
		}
		echo "<tfoot><tr><th>关键词</th><th>链接</th><th>属性</th><th>操作</th></tr></tfoot>\n";
		echo '</table>';
		// echo "</from>";
	} else {
		echo "<table class='wp-list-table widefat keyword-link-list'>\n";
		echo "<thead><tr><th>关键词</th><th>链接</th><th>属性</th><th style='width: 110px;'>操作</th></tr></thead>\n";
		echo "<tr><td colspan='5'>你还没有添加关键词</td></tr>";
		echo "<tfoot><tr><th>关键词</th><th>链接</th><th>属性</th><th>操作</th></tr></tfoot>\n";
		echo '</table>';
	}
	?>

	<form name=wp_page_form id=wp_page_form method="post" action="">
		<?php
		if ( isset( $page_num ) && $page_num > 2 ) {
			for ( $ii = 1;$ii < $page_num;$ii++ ) {
				if ( $ii == $wp_page ) {
					echo '<input class="button" style="margin:10px 0 0 3px;" type=submit name=wp_page value=' . $ii . ' />';
				} else {
					echo '<input class="button" style="margin:10px 0 0 3px;" type=submit name=wp_page value=' . $ii . ' />';
				}
			}
		}
		?>
	</form>

	<form name=delete_form id=delete_form method="post" action="">
		<input type=hidden name=action value=delete />
		<input type=hidden name=keyword value="" />
	</form>

	<script type="text/javascript"> 
		function checkbox() {
			var str = document.getElementsByName("checkbox");
			var objarray = str.length;
			var chestr = "";
			for (i = 0; i < objarray; i++) {
				if (str[i].checked == true) {
					chestr += str[i].value + "#";
				}
			}
			if (chestr == "") {
				alert("请选择一个关键词！");
			} else {
				WPDeleteKeyword(chestr);
			}
		}

		function checkAll(str) {
			var a = document.getElementsByName(str);
			var n = a.length;
			for (var i = 0; i < n; i++) a[i].checked = window.event.srcElement.checked;
		}
		function checkItem(str) {
			var e = window.event.srcElement;
			var all = eval("document.all." + str);
			if (e.checked) {
				var a = document.getElementsByName(e.name);
				all.checked = true;
				for (var i = 0; i < a.length; i++) {
					if (!a[i].checked) {
						all.checked = false;
						break;
					}
				}
			} else all.checked = false;
		}

		function WPDeleteKeyword(keyword) {
			if (!document.forms['delete_form'] || !document.forms['delete_form'].keyword) {
				alert('系统错误：表单未正确加载，请刷新页面');
				return;
			}
			if (confirm('确定要删除此关键词吗？')) {
				document.forms['delete_form'].keyword.value = keyword;
				document.forms['delete_form'].submit();
			}
		}

		function WPEditKeyword(keyword, url, desc, nofollow, firstonly, newwindow, ignorecase, isaffiliate, docomments, zh_CN) {
			document.wp_keywordadd.keyword.value = (keyword);
			document.wp_keywordadd.link.value = (url);
			document.wp_keywordadd.desc.value = (desc);
			document.wp_keywordadd.nofollow.checked = (nofollow == 1);
			document.wp_keywordadd.firstonly.checked = (firstonly == 1);
			document.wp_keywordadd.newwindow.checked = (newwindow == 1);
			document.wp_keywordadd.ignorecase.checked = (ignorecase == 1);
			document.wp_keywordadd.docomments.checked = (docomments == 1);
			document.wp_keywordadd.zh_CN.checked = (zh_CN == 1);
			window.location.hash = "keywordeditor";
		}
	</script>
	<?php
}

function wp_be_keywordlink_addnew() {
	global $last_link_options;
	$defaults = array( '', '', '', '', '', '', '', '', '' );
	$values   = explode( '|', $last_link_options ) + $defaults;
	list($last_link, $last_nofollow, $last_firstonly, $last_newwindow, $last_ignorecase, $last_isaffiliate, $last_docomments, $last_zh_CN, $last_desc) = $values;

	$fields = array(
		'keyword' => array(
			'label'     => '关键词',
			'maxlength' => 400,
			'required'  => true,
		),
		'link'    => array(
			'label'     => '链&nbsp;&nbsp;&nbsp;接',
			'maxlength' => 400,
			'required'  => true,
		),
		'desc'    => array(
			'label'     => '描&nbsp;&nbsp;&nbsp;述',
			'maxlength' => 500,
		),
	);

	$checkboxes = array(
		'zh_CN'      => '中文（必选）',
		'newwindow'  => '新窗口打开',
		'firstonly'  => '仅匹配一个',
		'nofollow'   => 'Nofollow属性',
		'ignorecase' => '忽略大小写',
		'docomments' => '匹配评论',
		// 'isaffiliate' => '内链',
	);
	?>

	<h4>编辑关键词</h4>
	<a name="keywordeditor"></a>
	<form name="wp_keywordadd" id="wp_keywordadd" method="post" action="">
		<input type="hidden" name="action" value="save" />
		<table>
			<?php foreach ( $fields as $name => $attributes ) : ?>
				<tr>
					<td><label for="<?php echo $name; ?>"><?php echo $attributes['label']; ?></label></td>
					<td>
						<input type="text" size="50" maxlength="<?php echo $attributes['maxlength']; ?>" name="<?php echo $name; ?>" />
						<?php if ( ! empty( $attributes['required'] ) ) : ?>
							<span style="font-size: 20px;color: #c40000;">*</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>

			<tr>
			<td style="vertical-align: top;	padding: 8px 0;">属&nbsp;&nbsp;&nbsp;性</td>
				<td class="attributes">
					<?php foreach ( $checkboxes as $name => $label ) : ?>
						<label for="<?php echo $name; ?>" style="display: inline-block;padding: 6px 0;">
							<input class="checkbox of-input" type="checkbox" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="1" 
							<?php if ( ${"last_$name"} ) : ?>
								checked<?php endif; ?> />
							<?php echo $label; ?>
						</label><br />
					<?php endforeach; ?>
				</td>
			</tr>

			<tr>
				<td><input type="submit" class="button-primary" style="margin:20px 0;" value="保存" /></td>
			</tr>
		</table>
	</form>
	<?php
}

function wp_be_keywordlink_global_options() {
	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'global_options' ) {
			$match_num_from = $_POST[ match_num_from ];
			$match_num_to   = $_POST[ match_num_to ];
			if ( ! $match_num_from || ! $match_num_to ) {
				$match_num_from = 2;
				$match_num_to   = 3;
			}
			if ( $match_num_from > $match_num_to ) {
				$match_num_from = $_POST[ match_num_to ];
				$match_num_to   = $_POST[ match_num_from ];
			}
			$link_itself = $_POST[ link_itself ];
			if ( ! $link_itself ) {
				$link_itself = 0;
			}
			$ignore_pre = $_POST[ ignore_pre ];
			if ( ! $ignore_pre ) {
				$ignore_pre = 0;
			}
			$ignore_page = $_POST[ ignore_page ];
			if ( ! $ignore_page ) {
				$ignore_page = 0;
			}
			$support_author = $_POST[ support_author ];
			if ( ! $support_author ) {
				$support_author = 0;
			}
			$global_options = implode( '|', array( $match_num_from, $match_num_to, $link_itself, $ignore_pre, $ignore_page, $support_author ) );
			update_option( WP_GLOBAL_OPTION, $global_options );
			wp_be_keywordlink_topbarmessage( '更新成功' );
		}
	}

	$the_global_options = get_option( WP_GLOBAL_OPTION );
	if ( $the_global_options ) {
		list($match_num_from, $match_num_to, $link_itself, $ignore_pre, $ignore_page, $support_author) = explode( '|', $the_global_options );
	} else {
		$match_num_from = 2;
		$match_num_to   = 3;
		$link_itself    = 0;
		$ignore_pre     = 0;
		$ignore_page    = 1;
		$support_author = 0;
	}
}

function wp_be_keywordlink_savenew() {
	$links = get_option( WP_KEYWORDLINK_OPTION );

	$keyword     = $_POST['keyword'];
	$link        = $_POST['link'];
	$desc        = $_POST['desc'];
	$nofollow    = isset( $_POST['nofollow'] ) ? ( $_POST['nofollow'] == '1' ? '1' : '0' ) : '0';
	$firstonly   = isset( $_POST['firstonly'] ) ? ( $_POST['firstonly'] == '1' ? '1' : '0' ) : '0';
	$newwindow   = isset( $_POST['newwindow'] ) ? ( $_POST['newwindow'] == '1' ? '1' : '0' ) : '0';
	$ignorecase  = isset( $_POST['ignorecase'] ) ? ( $_POST['ignorecase'] == '1' ? '1' : '0' ) : '0';
	$isaffiliate = isset( $_POST['isaffiliate'] ) ? ( $_POST['isaffiliate'] == '1' ? '1' : '0' ) : '0';
	$zh_CN       = isset( $_POST['zh_CN'] ) ? ( $_POST['zh_CN'] == '1' ? '1' : '0' ) : '0';
	$docomments  = isset( $_POST['docomments'] ) ? ( $_POST['docomments'] == '1' ? '1' : '0' ) : '0';

	if ( $keyword == '' || $link == '' ) {
		wp_be_keywordlink_topbarmessage( '必须输入关键词和链接！' );
		return;
	}

	if ( is_numeric( $keyword ) ) {
		wp_be_keywordlink_topbarmessage( '不能将数字用作关键词！' );
		return;
	}

	if ( preg_match( '/#/', $keyword ) ) {
		wp_be_keywordlink_topbarmessage( '不能使用符号“＃”' );
		return;
	}

	if ( isset( $links[ $keyword ] ) ) {
		wp_be_keywordlink_topbarmessage( '关键词已更新！' );
	}

	/* 储存链接 */
	$links[ $keyword ] = implode( '|', array( $link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc ) );
	update_option( WP_KEYWORDLINK_OPTION, $links );
}

function wp_be_keywordlink_deletekeyword() {
	$links   = get_option( WP_KEYWORDLINK_OPTION );
	$keyword = $_POST['keyword'];

	if ( preg_match( '/#/', $keyword ) ) {
		$keywords = explode( '#', $keyword );
	}

	if ( empty( $keywords ) ? '' : $keywords ) {
		foreach ( $keywords as $keyword ) {
			if ( ! isset( $links[ $keyword ] ) ) {
				continue;
			}
			unset( $links[ $keyword ] );
		}
	} else {
		if ( ! isset( $links[ $keyword ] ) ) {
			wp_be_keywordlink_topbarmessage( '没有该关键词！' );
			return;
		}
		unset( $links[ $keyword ] );
	}
		update_option( WP_KEYWORDLINK_OPTION, $links );
}

function wp_be_keywordlink_optionpage() {
	/* 执行动作 */
	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'save' ) {
			wp_be_keywordlink_savenew();
		}
	}
	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'delete' ) {
			wp_be_keywordlink_deletekeyword();
		}
	}
	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'importcvs' ) {
			wp_be_keywordlink_cvsimport();
		}
	}
	if ( isset( $_POST['page_limit'] ) ) {
		if ( $_POST['page_limit'] ) {
			wp_be_pagelimit_updated();
		}
	}
	if ( isset( $_POST['wp_keywordlink_deactivate_yes'] ) ) {
		if ( $_POST['wp_keywordlink_deactivate_yes'] ) {
			wp_be_keywordlink_deactivate();
		}
	}

	/* css */
	wp_be_keywordlink_css();
	echo '<div class="wrap">';
	echo '<h2>关键词</h2>';
	?>
		<div class="keyword-options">
			<?php
			wp_be_keywordlink_showdefinitions();
			wp_be_keywordlink_addnew();
			wp_be_keywordlink_global_options();
			wp_be_keywordlink_cvsmenu();
			?>
			<form name=delete_form id=delete_form method="post" action="" style="margin: 30px 0 0 0;">
				<input type="hidden" name="wp_keywordlink_deactivate_yes" value="1" />
				<input type="submit" name="wp_keywordlink_deactivate" value="删除所有" class="button" onclick="return confirm('确定要删除所有关键词和设置吗？');" />
			</form>
		</div>
	</div>
	<?php
}

function wp_be_keywordlink_replace( $content, $iscomments ) {
	global $wp_keywordlinks;
	$links = $wp_keywordlinks;

	$the_global_options = get_option( WP_GLOBAL_OPTION );
	if ( $the_global_options ) {
		list($match_num_from, $match_num_to, $link_itself, $ignore_pre, $ignore_page, $support_author) = explode( '|', $the_global_options );
	} else {
		$match_num_from = 2;
		$match_num_to   = 3;
		$link_itself    = 0;
		$ignore_pre     = 0;
		$ignore_page    = 1;
		$support_author = 0;
	}

	if ( $links ) {
		foreach ( $links as $keyword => $details ) {
			list($link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc) = explode( '|', $details );
			// 如果关键字未在评论中匹配
			if ( $iscomments && $docomments == 0 ) {
				continue;
			}

			// 如果是链接本身,则跳过.
			$recent_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			if ( $link == $recent_url ) {
				continue;
			}

				$cleankeyword = stripslashes( $keyword );
			if ( ! $desc ) {
				$desc = $cleankeyword; }
				$desc = addcslashes( $desc, '$' );
			if ( $isaffiliate ) {
				$url = "<span class='keyword-be keyword-inner'>";
			} else {
				$url = "<span class='keyword-be keyword-outer'>";
			}
				$url .= "<a href=\"$link\" title=\"$desc\"";

			if ( $nofollow ) {
				$url .= ' rel="nofollow"';
			}
			if ( $newwindow ) {
				$url .= ' target="_blank"';
			}

			$url .= '>' . addcslashes( $cleankeyword, '$' ) . '</a>';
			$url .= '</span>';

			if ( $firstonly ) {
				$limit = 1;
			} else {
				$limit = rand( $match_num_from, $match_num_to );
			}
			if ( $ignorecase ) {
				$case = 'i';
			} else {
				$case = '';
			}

			$ex_word = preg_quote( $cleankeyword, '\'' );
			// ignore pre & ignore_keywordlink
			if ( $num_2 = preg_match_all( '/<wp_nokeywordlink>.*?<\/wp_nokeywordlink>/is', $content, $ignore_keywordlink ) ) {
				for ( $i = 1;$i <= $num_2;$i++ ) {
					$content = preg_replace( '/<wp_nokeywordlink>.*?<\/wp_nokeywordlink>/is', "%ignore_keywordlink_$i%", $content, 1 );
				}
			}

			if ( $num_1 = preg_match_all( '/<pre.*?>.*?<\/pre>/is', $content, $ignore_pre ) ) {
				for ( $i = 1;$i <= $num_1;$i++ ) {
					$content = preg_replace( '/<pre.*?>.*?<\/pre>/is', "%ignore_pre_$i%", $content, 1 );
				}
			}

			// 排除
			if ( $num_3 = preg_match_all( '/<div class="full-img">.*?<\/div>/is', $content, $ignore_keywordlink ) ) {
				for ( $i = 1;$i <= $num_3;$i++ ) {
					$content = preg_replace( '/<div class="full-img">.*?<\/div>/is', "%ignore_keywordlink_$i%", $content, 1 );
				}
			}

			// $content = preg_replace( '|(<a[^>]+>)(.*)('.$ex_word.')(.*)(</a[^>]*>)|U', '$1$2%&&&&&%$4$5', $content);
			$content = preg_replace( '|(<img)([^>]*)(' . $ex_word . ')([^>]*)(>)|U', '$1$2%&&&&&%$4$5', $content );

			// For keywords with quotes (') to work, we need to disable word boundary matching
			$cleankeyword = preg_quote( $cleankeyword, '\'' );
			if ( $zh_CN ) {
				$regEx = '\'(?!((<.*?)|(<a.*?)))(' . $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
			} elseif ( strpos( $cleankeyword, '\'' ) > 0 ) {
				$regEx = '\'(?!((<.*?)|(<a.*?)))(' . $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
			} else {
				$regEx = '\'(?!((<.*?)|(<a.*?)))(\b' . $cleankeyword . '\b)(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
			}
			$content = preg_replace( $regEx, $url, $content, $limit );

			$content = str_replace( '%&&&&&%', stripslashes( $ex_word ), $content );
			// ignore pre & ignore_keywordlink
			for ( $i = 1;$i <= $num_1;$i++ ) {
				$content = str_replace( "%ignore_pre_$i%", $ignore_pre[0][ $i - 1 ], $content );
			}

			for ( $i = 1;$i <= $num_2;$i++ ) {
				$content = str_replace( "%ignore_keywordlink_$i%", $ignore_keywordlink[0][ $i - 1 ], $content );
			}

			// 排除
			for ( $i = 1;$i <= $num_3;$i++ ) {
				$content = str_replace( "%ignore_keywordlink_$i%", $ignore_keywordlink[0][ $i - 1 ], $content );
			}
		}
	}
	return $content;
}

function be_sort_by_len( $a, $b ) {
	if ( $a->name == $b->name ) {
		return 0;
	}
	return ( strlen( $a->name ) > strlen( $b->name ) ) ? -1 : 1;
}

function wp_be_keywordlink_replace_content( $content ) {
	return wp_be_keywordlink_replace( $content, false );
}

function wp_be_keywordlink_replace_comments( $content ) {
	return wp_be_keywordlink_replace( $content, true );
}

function wp_be_pagelimit_updated() {
	if ( isset( $_POST['page_limit'] ) ) {
		$page_limit = $_POST['page_limit'];
		update_option( WP_PAGELIMIT_OPTION, $page_limit );
		// wp_be_keywordlink_topbarmessage( '显示的内容' );
	}
}

function wp_be_keywordlink_init() {
	global $wp_keywordlinks;
	$wp_keywordlinks = get_option( WP_KEYWORDLINK_OPTION );
}

if ( function_exists( 'wp_title' ) ) {
	wp_keywordlink_add();
}

function wp_be_keywordlink_last_options() {
	global $last_link_options;
	$links = get_option( WP_KEYWORDLINK_OPTION );
	if ( $links ) {
		$last_link_options = array_pop( $links );
	}
}

function be_suport_author() {
	$the_global_options = get_option( WP_GLOBAL_OPTION );
	if ( $the_global_options ) {
		list($match_num_from, $match_num_to, $link_itself, $ignore_pre, $ignore_page, $support_author) = explode( '|', $the_global_options );
	} else {
		$match_num_from = 2;
		$match_num_to   = 3;
		$link_itself    = 0;
		$ignore_pre     = 0;
		$ignore_page    = 1;
		$support_author = 0;
	}

	if ( $support_author ) {
		echo '<!-- WP Keyword Link by liucheng.name -->';
	}
}

function be_you() {
	echo '<p>';
	echo '</p>';
}

function wp_be_keywordlink_css() {
	echo '<style type="text/css">
	.search-k {float: right;}
	.keyword-options h4 {font-size: 15px;}
	.action-button {background: transparent;margin: 0 10px 0 0;padding: 0;border: none;cursor: pointer;}
	.action-button:hover {color: #0073aa;}
	.attri-butes label {float: left;width: 50%;display: inline-block;padding: 0 0 20px 0;}
	</style>';
}

function wp_keywordlink_add() {
	add_action( 'admin_menu', 'wp_be_keywordlink_admininit' );
	add_filter( 'the_content', 'wp_be_keywordlink_replace_content', 100 );
	add_filter( 'comment_text', 'wp_be_keywordlink_replace_comments', 100 );

	// WooCommerce
	add_filter( 'woocommerce_content', 'wp_be_keywordlink_replace_content', 100 );

	// bbpress
	add_filter( 'bbp_get_topic_content', 'wp_be_keywordlink_replace_content', 100 );
	add_filter( 'bbp_get_reply_content', 'wp_be_keywordlink_replace_content', 100 );

	add_action( 'init', 'wp_be_keywordlink_checkcvs' );
	add_action( 'init', 'wp_be_keywordlink_init' );
	add_action( 'init', 'wp_be_keywordlink_last_options' );
	// add_action('admin_head','wp_be_keywordlink_css');
	add_action( 'wp_footer', 'be_suport_author' );
}
function wp_be_keywordlink_deactivate() {
	global $wpdb;
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name =  'wp_keywordlinkoption' LIMIT 1" );
	echo '<script>window.location.reload();</script>';
	exit;
}

// 导出导入
function wp_be_keywordlink_cvsexportvalue( $value, $islast = false ) {
	if ( $value ) {
		echo '1';
	} else {
		echo '0';
	}
	if ( ! $islast ) {
		echo ',';
	}
}

function wp_be_keywordlink_cvsexport() {
	$links = get_option( WP_KEYWORDLINK_OPTION );
	/* Tell the browser to expect an CVS file */
	header( 'Content-type: application/text' );
	header( 'Content-Disposition: attachment; filename="Keywordlink.export"' );
	/* Generate the header line */
	echo "Keyword,URL,NoFollow,First Only,New Window,Ignore Case,IsAffiliate,Enable In Comments,Chinese Keyword,Description\n";
	foreach ( $links as $keyword => $details ) {
		list($link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc) = explode( '|', $details );
		$cleankeyword = $keyword;
		if ( ! $desc ) {
			$desc = $cleankeyword; }
		echo "\"$cleankeyword\",";
		echo "\"$link\",";

		wp_be_keywordlink_cvsexportvalue( $nofollow, false );
		wp_be_keywordlink_cvsexportvalue( $firstonly, false );
		wp_be_keywordlink_cvsexportvalue( $newwindow, false );
		wp_be_keywordlink_cvsexportvalue( $ignorecase, false );
		wp_be_keywordlink_cvsexportvalue( $isaffiliate, false );
		wp_be_keywordlink_cvsexportvalue( $docomments, false );
		wp_be_keywordlink_cvsexportvalue( $zh_CN, true );

		echo ",\"$desc\"";
		echo "\n";
	}
	/* End of the show */
	die( 0 );
}

function wp_be_keywordlink_cvsmenu() {
	?>
	<h4>备份关键词</h4>
	<form enctype="multipart/form-data" name="cvs_form" method="post" action="">
	<input type="hidden" name="action" id="export_action" value="">
	<input type="button" class="button-primary" onclick="document.getElementById('export_action').value='exportcvs';this.form.submit();" value="导出" style="margin: 0 10px 0 0;">
	<input type="button" class="button" onclick="document.getElementById('export_action').value='importcvs';this.form.submit();" value="导入" style="margin: 0 10px;">
	<input type="file" name="upload" />
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	</form>
	<?php
}

function wp_be_keywordlink_cvsimport() {
	if ( is_uploaded_file( $_FILES['upload']['tmp_name'] ) ) {
		$cvscontent = file( $_FILES['upload']['tmp_name'], FILE_IGNORE_NEW_LINES );
		$links      = get_option( WP_KEYWORDLINK_OPTION );
		// Ensure $links is an array
		if ( ! is_array( $links ) ) {
			$links = array();
		}
		// Keep some statistics
		$cnt     = 0;
		$skip    = 0;
		$replace = 0;
		$added   = 0;
		foreach ( $cvscontent as $row => $value ) {
			// Skip the first row
			if ( $cnt++ == 0 ) {
				// A little check to see if the file we are importing isn't complete garbage
				if ( strpos( $value, 'Keyword' ) === false ) {
					wp_be_keywordlink_topbarmessage( '不是有效的文件！ 请检查文件格式' );
					return;
				}
				continue;
			}
			if ( preg_match( '/^#/', $value ) ) {
				++$skip;
				continue;
			}
			list( $keyword, $link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc) = explode( ',', $value );
			// Strip "" from the beginning and end of the keyword and url
			$keyword = trim( $keyword, '"' );
			$link    = trim( $link, '"' );
			if ( ! $desc ) {
				$desc = $keyword;
			} else {
				$desc = trim( $desc, '"' ); }
			// Ignore empty keywords, or keywords with no link
			if ( $keyword == '' || $link == '' ) {
				++$skip;
				continue;
			}

			// Count how many entries we are replacing
			if ( array_key_exists( $keyword, $links ) ) {
				++$replace;
			} else {
				++$added;
			}

			// Input validation
			$nofollow    = $nofollow ? 1 : 0;
			$firstonly   = $firstonly ? 1 : 0;
			$newwindow   = $newwindow ? 1 : 0;
			$ignorecase  = $ignorecase ? 1 : 0;
			$isaffiliate = $isaffiliate ? 1 : 0;
			$docomments  = $docomments ? 1 : 0;
			$zh_CN       = $zh_CN ? 1 : 0;

			$newlinks[ $keyword ] = implode( '|', array( $link, $nofollow, $firstonly, $newwindow, $ignorecase, $isaffiliate, $docomments, $zh_CN, $desc ) );
		}
		// If we encountered no errors, merge the new keywords with the existing keywords
		foreach ( $newlinks as $keyword => $parameters ) {
			$links[ $keyword ] = $parameters;
		}
		// Update the WordPress database
		update_option( WP_KEYWORDLINK_OPTION, $links );
		wp_be_keywordlink_topbarmessage( "导入成功，替换 $replace 条关键词, 忽略 $skip 条, 添加 $added 条关键词" );
	} else {
		wp_be_keywordlink_topbarmessage( '上传文件时出错' );
	}
}

function wp_be_keywordlink_checkcvs() {
	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'exportcvs' ) {
			wp_be_keywordlink_cvsexport();
		}
	}
}
