<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! function_exists( 'cx_get_option' ) ) {
	function cx_get_option( $option = '', $default = null ) {

		$options = get_option( 'begin_cx' );
		return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
	}
}

$prefix = 'begin_cx';

ZMOP::createOptions(
	$prefix,
	array(
		'framework_title'         => '辅助设置',
		'framework_class'         => 'be-box',

		'menu_title'              => '辅助设置',
		'menu_slug'               => 'cx-options',
		'menu_type'               => 'submenu',
		'menu_capability'         => 'manage_options',
		'menu_icon'               => null,
		'menu_position'           => null,
		'menu_hidden'             => false,
		'menu_parent'             => 'themes.php',

		'show_bar_menu'           => false,
		'show_sub_menu'           => false,
		'show_in_network'         => true,
		'show_in_customizer'      => false,

		'show_search'             => false,
		'show_reset_all'          => true,
		'show_reset_section'      => true,
		'show_footer'             => true,
		'show_all_options'        => true,
		'show_form_warning'       => true,
		'sticky_header'           => true,
		'save_defaults'           => true,
		'ajax_save'               => $save,

		'admin_bar_menu_icon'     => 'cx cx-begin',
		'admin_bar_menu_priority' => 80,

		'footer_text'             => '',
		'footer_after'            => '',
		'footer_credit'           => '',

		'database'                => '',
		'transient_time'          => 0,

		'contextual_help'         => array(),
		'contextual_help_sidebar' => '',

		'enqueue_webfont'         => true,
		'async_webfont'           => false,

		'output_css'              => true,

		'nav'                     => 'normal',
		'theme'                   => 'be',
		'class'                   => '',

		'defaults'                => array(),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'id'    => 'sundry_setting',
		'title' => '综合设置',
		'icon'  => 'dashicons dashicons-image-filter',
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '隐藏菜单',
		'icon'        => '',
		'description' => '隐藏后台不常用的菜单项',
		'fields'      => array(

			array(
				'id'      => 'hide_wp_menu',
				'type'    => 'checkbox',
				'title'   => '隐藏系统菜单',
				'inline'  => true,
				'all'     => true,
				'options' => array(
					'link'       => '链接',
					'page'       => '页面',
					'comments'   => '评论',
					'themes'     => '外观',
					'customize'  => '自定义',
					'background' => '背景',
					'plugins'    => '插件',
					'users'      => '用户',
					'tools'      => '工具',
					'options'    => '设置',
				),
				'default' => array( '' ),
			),

			array(
				'id'      => 'hide_be_menu',
				'type'    => 'checkbox',
				'title'   => '隐藏主题菜单',
				'inline'  => true,
				'all'     => true,
				'options' => array(
					'bulletin' => '公告',
					'picture'  => '图片',
					'video'    => '视频',
					'tao'      => '商品',
					'sites'    => '网址',
					'show'     => '项目',
					'playlist' => '音频',
					'surl'     => '短链接',
					'invite'   => '邀请码',
				),
				'default' => array(),
			),

		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '文章面板',
		'icon'        => '',
		'description' => '文章编辑页面面板设置',
		'fields'      => array(
			array(
				'id'      => 'build_metabox',
				'type'    => 'switcher',
				'title'   => '构建面板',
				'default' => true,
				'after'   => '<span class="after-perch">仅在页面选择“模块构建”模板时显示。</span>',
			),

			array(
				'id'      => 'header_show_meta',
				'type'    => 'switcher',
				'title'   => '标题幻灯',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'header_show_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'header_bg_meta',
				'type'    => 'switcher',
				'title'   => '标题图片',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'header_bg_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'other_meta',
				'type'    => 'switcher',
				'title'   => '其它设置',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'other_box',
				'class'   => 'be-child-item be-child-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'gallery_meta',
				'type'    => 'switcher',
				'title'   => '图文模块',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'gallery_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'ext_inf_meta',
				'type'    => 'switcher',
				'title'   => '附加信息',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'ext_inf_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'down_meta',
				'type'    => 'switcher',
				'title'   => '下载信息',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'down_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'tao_meta',
				'type'    => 'switcher',
				'title'   => '商品设置',
				'default' => true,
				'after'   => '<span class="after-perch">启用，仅在选择“商品模板”时显示。</span>',
			),

			array(
				'id'      => 'tao_meta_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'ed_down_meta',
				'type'    => 'switcher',
				'title'   => 'Erphpdown属性',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'ed_down_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'erphpdown_meta',
				'type'    => 'switcher',
				'title'   => '资源信息',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'erphpdown_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'cp_page_meta',
				'type'    => 'switcher',
				'title'   => '产品展示模板设置',
				'default' => true,
				'after'   => '<span class="after-perch">启用，仅在选择“产品展示”模板时显示。</span>',
			),

			array(
				'id'      => 'cp_page_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

			array(
				'id'      => 'btnlink_meta',
				'type'    => 'switcher',
				'title'   => '按钮链接',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'btnlink_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),


			array(
				'id'      => 'subject_meta',
				'type'    => 'switcher',
				'title'   => '专题模板设置',
				'default' => true,
				'after'   => '<span class="after-perch">启用</span>',
			),

			array(
				'id'      => 'subject_box',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">仅管理员可见</span>',
			),

		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '下载页面',
		'icon'        => '',
		'description' => '设置单独的下载页面',
		'fields'      => array(

			array(
				'id'    => 'root_down_url',
				'type'  => 'switcher',
				'title' => '下载链接到根目录，并执行下面的操作',
			),

			array(
				'id'    => 'root_file_move',
				'class' => 'be-child-item be-sub-last-item',
				'type'  => 'switcher',
				'title' => '复制下载模板到网站根目录',
				'after' => '<span class="after-perch">自动将“inc/download.php”文件复制到网站根目录，勾选后需保存两次设置，用后取消勾选</span>',
			),

			array(
				'id'      => 'down_header_img',
				'type'    => 'upload',
				'title'   => '页面背景图片',
				'default' => $imgdefault . '/options/1200.jpg',
				'preview' => true,
			),

			array(
				'id'       => 'down_explain',
				'class'    => 'textarea-30',
				'type'     => 'textarea',
				'title'    => '版权说明',
				'default'  => '本站大部分下载资源收集于网络，只做学习和交流使用，版权归原作者所有。若您需要使用非免费的软件或服务，请购买正版授权并合法使用。本站发布的内容若侵犯到您的权益，请联系站长删除，我们将及时处理。',
				'sanitize' => false,
				'after'    => '可使用HTML代码',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '外链字体',
		'icon'        => '',
		'description' => '外链特殊字体，例如谷歌字体库',
		'fields'      => array(

			array(
				'id'    => 'custom_fonts_cn',
				'type'  => 'switcher',
				'title' => '中文语言',
			),

			array(
				'id'      => 'custom_fonts_cn_url',
				'class'   => 'textarea-30 be-child-item be-child-last-item',
				'type'    => 'textarea',
				'title'   => '字体链接',
				'default' => '',
				'after'   => '例如：https://fonts.loli.net/css2?family=Outfit:wght@100..900&display=swap',
			),

			array(
				'id'    => 'custom_fonts_en',
				'type'  => 'switcher',
				'title' => '英文语言',
			),

			array(
				'id'      => 'custom_fonts_en_url',
				'class'   => 'textarea-30 be-child-item be-child-last-item',
				'type'    => 'textarea',
				'title'   => '字体链接',
				'default' => '',
				'after'   => '例如：https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap',
			),

			array(
				'class'   => 'be-help-item',
				'title'   => '说明',
				'type'    => 'content',
				'content' => '例子中的<code>https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swa</code>，外链接字体为：Outfit<br />
			<p>到主题选项 → 定制风格 → <a href="' . home_url() . '/wp-admin/admin.php?page=begin-options#tab=' . strtolower( urlencode( '定制风格' ) ) . '/' . strtolower( urlencode( '自定义样式' ) ) . '" target="_blank">自定义样式</a>中，输入：</p>
			<p></p>
			<p><code>body, button, input, select, textarea {font-family: Outfit;}</code></p>
			<p>可以考虑使用国内的公共资源库，例如：https://fonts.loli.net</p>',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '创作团队',
		'icon'        => '',
		'description' => '设置“创作团队”页面模板，用于显示全部作者',
		'fields'      => array(

			array(
				'id'      => 'no_roles',
				'type'    => 'checkbox',
				'title'   => '排除角色',
				'inline'  => true,
				'all'     => true,
				'options' => array(
					'administrator' => '管理员',
					'editor'        => '编辑',
					'author'        => '作者',
					'contributor'   => '贡献者',
				),
			),

			array(
				'id'      => 'roles_des',
				'type'    => 'switcher',
				'title'   => '显示简介',
				'default' => true,
			),

			array(
				'id'      => 'roles_des_clamp',
				'class'   => 'be-child-item be-sub-item',
				'type'    => 'switcher',
				'title'   => '仅显示两行',
				'default' => true,
			),

			array(
				'id'      => 'roles_des_count',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'number',
				'title'   => '字数',
				'default' => '180',
			),

			array(
				'id'      => 'roles_post',
				'type'    => 'switcher',
				'title'   => '仅发表文章的',
				'default' => true,
			),

			array(
				'id'      => 'roles_mode',
				'type'    => 'radio',
				'title'   => '模式',
				'inline'  => true,
				'options' => array(
					'roles'  => '角色分类',
					'normal' => '不分角色',
				),
				'default' => 'roles',
			),

			array(
				'id'      => 'rolescss',
				'type'    => 'radio',
				'title'   => '样式',
				'inline'  => true,
				'options' => array(
					'img'  => '默认',
					'grid' => '卡片',
				),
				'default' => 'img',
			),

			array(
				'id'      => 'roles_f',
				'type'    => 'radio',
				'title'   => '分栏',
				'inline'  => true,
				'options' => array(
					'3' => '3栏（卡片）',
					'4' => '4栏',
					'5' => '5栏',
					'6' => '6栏',
				),
				'default' => '5',
			),

			array(
				'id'      => 'update_tip',
				'type'    => 'radio',
				'title'   => '更新标志',
				'inline'  => true,
				'options' => array(
					'week'  => '一周内发表过文章',
					'month' => '一月内发表过文章',
				),
				'default' => 'week',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '实时天气',
		'icon'        => '',
		'description' => '在菜单上显示天气挂件',
		'fields'      => array(

			array(
				'id'    => 'weather_widget',
				'type'  => 'switcher',
				'title' => '启用',
			),

			array(
				'id'      => 'weather_mode',
				'type'    => 'radio',
				'title'   => '显示在',
				'inline'  => true,
				'options' => array(
					'top'  => '顶部',
					'main' => '主菜单',
					'logo' => '标志',
				),
				'default' => 'top',
			),

			array(
				'id'      => 'weatherbg',
				'class'   => 'textarea-30',
				'type'    => 'textarea',
				'title'   => '背景图片',
				'default' => $imgdefault . '/captcha/y1.jpg, ' . $imgdefault . '/captcha/y2.jpg,' . $imgdefault . '/captcha/y4.jpg',
				'after'   => '随机显示，多张图片链接，中间用英文半角逗号","隔开，建议图片尺寸：270×382',
			),

			array(
				'id'    => 'weather_code',
				'type'  => 'switcher',
				'title' => '自定义代码',
				'label' => '默认调用代码可能超出使用量，请使用自己的代码',
			),

			array(
				'id'         => 'weather_script',
				'class'      => 'textarea-30 be-child-item be-sub-last-item',
				'type'       => 'textarea',
				'title'      => '输入代码',
				'sanitize'   => false,
				'before'     => '阅读 <a href="https://docs.seniverse.com/widget/start/get.html" rel="external nofollow" target="_blank">心知天气</a> 官方教程，在 <a href="https://www.seniverse.com/widgetv3" rel="external nofollow" target="_blank">配置插件</a> 页面，仅需将<code>&lt;script&gt;&lt;/script&gt;</code>部分代码复制粘贴到下面。',
				'after'      => '因主题修改了默认的样式，增减显示项目可能会造成样式错误。',
				'dependency' => array( 'weather_code', '==', 'true' ),
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '登录提示文字',
		'icon'        => '',
		'description' => '用于自定义登录注册时错误提示文字',
		'fields'      => array(

			array(
				'id'      => 'reg_name',
				'type'    => 'text',
				'title'   => '请输入用户名',
				'default' => '请输入用户名',
			),

			array(
				'id'      => 'reg_password',
				'type'    => 'text',
				'title'   => '请输入密码',
				'default' => '请输入密码',
			),

			array(
				'id'      => 'reg_name_error',
				'type'    => 'text',
				'title'   => '用户名错误',
				'default' => '用户名错误',
			),

			array(
				'id'      => 'reg_password_error',
				'type'    => 'text',
				'title'   => '密码错误',
				'default' => '密码错误',
			),

			array(
				'id'      => 'reg_change_name',
				'type'    => 'text',
				'title'   => '换个用户名',
				'default' => '换个用户名',
			),

			array(
				'id'      => 'reg_change_email',
				'type'    => 'text',
				'title'   => '此邮箱已被注册',
				'default' => '此邮箱已被注册',
			),

			array(
				'id'    => 'no_login_errors',
				'type'  => 'switcher',
				'title' => '禁止后台登录提示',
				'label' => '出于安全考虑，禁止WordPress默认登录注册页面错误提示',
			),

		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '自定义仪表盘',
		'icon'        => '',
		'description' => '隐藏仪表盘默认元素，并添加自定义内容',
		'fields'      => array(

			array(
				'id'      => 'hide_dashboard',
				'type'    => 'switcher',
				'title'   => '隐藏仪表盘默认元素',
				'default' => true,
			),

			array(
				'id'      => 'add_dashboard',
				'type'    => 'switcher',
				'title'   => '显示自定义内容',
				'default' => true,
			),

			array(
				'id'      => 'dashboard_title',
				'type'    => 'text',
				'title'   => '自定义标题',
				'default' => '欢迎光临本站',
			),

			array(
				'id'       => 'dashboard_content',
				'class'    => 'textarea-30',
				'type'     => 'textarea',
				'title'    => '自定义内容',
				'sanitize' => false,
				'default'  => '<a href="#" target="_blank"><img src="' . $imgdefault . '/random/560.jpg" alt="广告也精彩" /></a>辅助设置 → 综合设置 → 自定义仪表盘，修改此内容 ',
				'after'    => '支持HTML代码',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '自定义媒体路径',
		'icon'        => '',
		'description' => '修改默认媒体路径',
		'fields'      => array(

			array(
				'id'    => 'be_upload_path',
				'type'  => 'switcher',
				'title' => '启用',
			),

			array(
				'id'      => 'be_upload_path_url',
				'type'    => 'text',
				'title'   => '自定义路径',
				'default' => 'wp-content/media',
				'after'   => 'WP缺省为wp-content/uploads',
			),

			array(
				'class'   => 'be-button-url be-button-help-url',
				'type'    => 'subheading',
				'title'   => '媒体设置',
				'content' => '<span class="be-url-btn"><a href="' . home_url() . '/wp-admin/options-media.php" target="_blank">媒体设置</a></span>',
			),

		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => 'sundry_setting',
		'title'       => '自定义后台页脚信息',
		'icon'        => '',
		'description' => '用于隐藏后台页脚信息',
		'fields'      => array(
			array(
				'id'      => 'remove_footer_wp',
				'type'    => 'switcher',
				'title'   => '启用',
				'default' => true,
			),

			array(
				'id'      => 'wp_footer_inf',
				'class'   => 'textarea-30 be-child-item be-child-last-item',
				'type'    => 'textarea',
				'title'   => '自定义内容',
				'default' => '',
				'after'   => '留空则隐藏',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'parent'      => '',
		'title'       => '网址收藏',
		'icon'        => 'dashicons dashicons-admin-site-alt2',
		'description' => '网址相关设置',
		'fields'      => array(

			array(
				'id'      => 'site_f',
				'type'    => 'radio',
				'title'   => '分栏',
				'inline'  => true,
				'options' => $fsl45,
				'default' => '4',
			),

			array(
				'id'      => 'site_p_n',
				'type'    => 'number',
				'title'   => '汇总页文章数',
				'default' => 20,
				'after'   => '<span class="after-perch">默认 20</span>',
			),

			array(
				'id'      => 'site_c_n',
				'type'    => 'number',
				'title'   => '归档页文章数',
				'default' => 40,
				'after'   => '<span class="after-perch">默认 40</span>',
			),

			array(
				'id'      => 'all_site_cat',
				'type'    => 'switcher',
				'title'   => '分类目录',
				'default' => true,
				'after'   => '<span class="after-perch">显示</span>',
			),

			array(
				'id'      => 'site_cat_fixed',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'switcher',
				'title'   => '&nbsp;',
				'default' => true,
				'after'   => '<span class="after-perch">固定在左侧</span>',
			),

			array(
				'id'      => 'sites_des',
				'type'    => 'switcher',
				'title'   => '显示描述',
				'default' => true,
			),

			array(
				'id'      => 'sites_count',
				'type'    => 'switcher',
				'title'   => '显示文章数',
				'default' => true,
			),

			array(
				'id'      => 'sites_ico',
				'type'    => 'switcher',
				'title'   => '显示图标',
				'default' => true,
			),

			array(
				'id'      => 'sites_boxs',
				'type'    => 'switcher',
				'title'   => '悬停动画',
				'default' => true,
			),

			array(
				'id'    => 'sites_adorn',
				'type'  => 'switcher',
				'title' => '装饰动画',
			),

			array(
				'id'      => 'more_site',
				'type'    => 'radio',
				'title'   => '翻页模式',
				'inline'  => true,
				'options' => array(
					'true'  => '文字',
					'false' => '图标',
				),
				'default' => 'false',
			),

			array(
				'id'      => 'site_link_go',
				'type'    => 'switcher',
				'title'   => '网址跳转',
				'default' => true,
			),

			array(
				'id'          => 'site_link_go_id',
				'class'       => 'be-child-item be-sub-last-item',
				'type'        => 'select',
				'title'       => '选择跳转页面',
				'placeholder' => '选择页面',
				'default'     => url_to_postid( $bloghome . '/go' ),
				'options'     => 'pages',
				'query_args'  => array(
					'posts_per_page' => -1,
				),
				'after'       => '新建页面，模板选择“链接跳转”，并发表',
			),

			array(
				'id'      => 'site_sc',
				'type'    => 'switcher',
				'title'   => '网址正文显示网站截图',
				'default' => true,
			),

			array(
				'id'      => 'screenshot_api',
				'class'   => 'be-child-item be-sub-last-item',
				'type'    => 'radio',
				'title'   => '截图API接口',
				'inline'  => true,
				'options' => array(
					'api_wp'                => 's0.wp',
					'api_wordpress'         => 's0.wordpress.com',
					'api_urlscan'           => 'urlscan.io',
					'api_screenshotmachine' => 'screenshotmachine.com',
				),
				'default' => 'api_wp',
			),

		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'title'       => '信息提交',
		'icon'        => 'dashicons dashicons-edit-page',
		'description' => '用于前端提交信息到公告文章，新建页面并添加短代码 <code>[bet_submission_info]</code>',
		'fields'      => array(

			array(
				'id'      => 'publish_info',
				'type'    => 'switcher',
				'title'   => '启用',
				'default' => true,
			),

			array(
				'id'      => 'info_pending',
				'class'   => 'be-child-item be-sub-item',
				'type'    => 'switcher',
				'title'   => '提交为待审状态',
				'default' => true,
				'after'   => '<span class="after-perch">没必要发表</span>',
			),

			array(
				'id'    => 'no_logged_in',
				'class' => 'be-child-item be-sub-item',
				'type'  => 'switcher',
				'title' => '免登录提交',
			),

			array(
				'id'          => 'info_cat',
				'class'       => 'be-child-item be-sub-last-item',
				'type'        => 'select',
				'title'       => '选择分类',
				'placeholder' => '选择分类',
				'options'     => 'categories',
				'query_args'  => array(
					'taxonomy' => 'notice',
				),
			),

			array(
				'title'   => '添加表单',
				'type'    => 'content',
				'content' => '最多可添加20个表单项，编号 1-20 不可重复使用',
			),

			array(
				'id'      => 'message_name',
				'class'   => 'be-child-item',
				'type'    => 'text',
				'title'   => '标题 <span style="font-weight: 400;">（必须）</span>',
				'default' => '姓名',
			),

			array(
				'id'                     => 'message_form_add',
				'class'                  => 'be-child-item',
				'type'                   => 'group',
				'title'                  => '添加表单',
				'accordion_title_by'     => array( 'message_number', 'message_title', 'message_select' ),
				'accordion_title_number' => false,
				'accordion_title_prefix' => '编号',
				'fields'                 => array(
					array(
						'id'    => 'message_number',
						'type'  => 'number',
						'title' => '编号',
					),

					array(
						'id'    => 'message_title',
						'class' => 'be-child-item',
						'type'  => 'text',
						'title' => '文本框标题',
					),

					array(
						'id'     => 'message_select',
						'class'  => 'be-child-item',
						'type'   => 'text',
						'title'  => '下拉单选标题',
						'before' => '留空则显示文本框',
					),

					array(
						'id'         => 'message_text',
						'class'      => 'be-child-item',
						'type'       => 'text',
						'title'      => '默认文字',
						'dependency' => array( 'message_select', '!=', '' ),
					),

					array(
						'id'         => 'message_option',
						'class'      => 'be-child-item',
						'type'       => 'group',
						'title'      => '添加下拉列表',
						'fields'     => array(
							array(
								'id'    => 'select_value',
								'class' => 'be-child-item',
								'type'  => 'text',
								'title' => '文字',
							),
						),
						'dependency' => array( 'message_select', '!=', '' ),
					),
				),

				'default'                => array(
					array(
						'message_number' => '1',
						'message_title'  => '职业',
						'message_select' => '',
						'message_text'   => '选择一个',
						'message_option' => array(
							array(
								'select_value' => '高中',
							),
							array(
								'select_value' => '大学',
							),
							array(
								'select_value' => '本科以上',
							),
						),
					),

					array(
						'message_number' => '2',
						'message_title'  => '',
						'message_select' => '学历',
						'message_text'   => '选择一个',
						'message_option' => array(
							array(
								'select_value' => '高中',
							),
							array(
								'select_value' => '大学',
							),
							array(
								'select_value' => '本科以上',
							),
						),
					),

					array(
						'message_number' => '3',
						'message_title'  => '电话',
						'message_select' => '',
						'message_text'   => '选择一个',
						'message_option' => array(
							array(
								'select_value' => '高中',
							),
							array(
								'select_value' => '大学',
							),
							array(
								'select_value' => '本科以上',
							),
						),
					),

					array(
						'message_number' => '4',
						'message_title'  => '微信/QQ',
						'message_select' => '',
						'message_text'   => '选择一个',
						'message_option' => array(
							array(
								'select_value' => '高中',
							),
							array(
								'select_value' => '大学',
							),
							array(
								'select_value' => '本科以上',
							),
						),
					),

					array(
						'message_number' => '5',
						'message_title'  => '邮箱',
						'message_select' => '',
						'message_text'   => '选择一个',
						'message_option' => array(
							array(
								'select_value' => '高中',
							),
							array(
								'select_value' => '大学',
							),
							array(
								'select_value' => '本科以上',
							),
						),
					),
				),
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'title'       => 'WOO商店',
		'icon'        => 'dashicons dashicons-cart',
		'description' => '需要安装商店插件 WooCommerce ',
		'fields'      => array(

			array(
				'id'      => 'woo_cols_n',
				'type'    => 'number',
				'title'   => '每页显示数量',
				'default' => 20,
			),

			array(
				'id'      => 'woo_related_n',
				'type'    => 'number',
				'title'   => '相关文章数量',
				'default' => 4,
			),

			array(
				'id'      => 'woo_f',
				'type'    => 'radio',
				'title'   => '分栏',
				'inline'  => true,
				'options' => $fl456,
				'default' => '5',
			),

			array(
				'id'      => 'woo_thumbnail',
				'type'    => 'upload',
				'title'   => '默认缩略图',
				'default' => $imgdefault . '/random/320.jpg',
				'preview' => true,
			),

			array(
				'id'      => 'shop_header_img',
				'type'    => 'upload',
				'title'   => '商店页面默认图片',
				'default' => $imgdefault . '/options/1200.jpg',
				'preview' => true,
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'title'       => '其它设置',
		'icon'        => 'dashicons dashicons-dashboard',
		'description' => '',
		'fields'      => array(

			array(
				'id'      => 'meta_delete',
				'type'    => 'switcher',
				'title'   => '防止文章选项丢失',
				'after'   => '<span class="after-perch">在使用文章快速编辑和定时发布时使用</span>',
				'default' => true,
			),

			array(
				'id'    => 'front_english',
				'type'  => 'switcher',
				'title' => '前端英文',
			),

			array(
				'id'    => 'login_down_key',
				'type'  => 'switcher',
				'title' => '下载模块登录查看密码',
			),

			array(
				'id'    => 'remove_version',
				'type'  => 'switcher',
				'title' => '版本号',
				'after' => '<span class="after-perch">CSS、JS带版本号，使用CDN缓存时可以不勾选</span>',
			),

			array(
				'id'    => 'remove_jqmigrate',
				'type'  => 'switcher',
				'title' => '移除jQuery迁移辅助',
				'after' => '<span class="after-perch">如发现有错误，取消勾选</span>',
			),

			array(
				'id'    => 'web_queries',
				'type'  => 'switcher',
				'title' => '在页脚显示查询次数及加载时间',
			),

			array(
				'id'    => 'remove_core_updater',
				'type'  => 'switcher',
				'title' => '移除另一更新正在进行',
				'after' => '<span class="after-perch">用后关闭</span>',
			),

			array(
				'id'    => 'delete_favorite',
				'type'  => 'switcher',
				'title' => '删除文章收藏数据表',
			),
		),
	)
);

ZMOP::createSection(
	$prefix,
	array(
		'title'       => '备份设置',
		'icon'        => 'dashicons dashicons-update',
		'description' => '将辅助设置数据导出为“<span style="color: #000;">辅助设置备份 + 日期.json</span>”文件，并下载到本地',
		'fields'      => array(

			array(
				'class' => 'be-child-item be-sub-item',
				'type'  => 'backup_sub',
				'after' => '请不要随意输入内容，并执行导入操作，否则所有设置将消失！',
			),
		),
	)
);
