<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 缩略图缓存
if ( ! function_exists( 'be_resize_image' ) ) {
	function be_resize_image( $url, $width = null, $height = null, $crop = null, $single = true ) {
		try {
			$upload_info = wp_upload_dir();
			$upload_dir  = $upload_info['basedir'];
			$upload_url  = $upload_info['baseurl'];

			$http_prefix  = 'http://';
			$https_prefix = 'https://';

			if ( 0 === strpos( $url, $https_prefix ) ) {
				$upload_url = str_replace( $http_prefix, $https_prefix, $upload_url );
			} elseif ( 0 === strpos( $url, $http_prefix ) ) {
				$upload_url = str_replace( $https_prefix, $http_prefix, $upload_url );
			}

			if ( false === strpos( $url, $upload_url ) ) {
				return $url;
			}

			$rel_path = str_replace( $upload_url, '', $url );
			$img_path = $upload_dir . $rel_path;

			if ( false !== strpos( $rel_path, '..' ) || false !== strpos( $rel_path, './' ) ) {
				return $url;
			}

			$real_path = realpath( $img_path );
			if ( false === $real_path || 0 !== strpos( $real_path, realpath( $upload_dir ) ) ) {
				return $url;
			}

			if ( ! file_exists( $img_path ) ) {
				return $url;
			}

			$info     = pathinfo( $img_path );
			$ext      = $info['extension'];
			$filename = $info['filename'];

			$thumbnail_dir = $upload_dir . '/thumbnail';
			if ( ! file_exists( $thumbnail_dir ) ) {
				mkdir( $thumbnail_dir, 0755, true );
			}

				$suffix       = $width . 'x' . $height;
				$destfilename = $thumbnail_dir . '/' . $filename . '-' . $suffix . '.' . $ext;
				$thumb_url    = $upload_url . '/thumbnail/' . $filename . '-' . $suffix . '.' . $ext;

			if ( file_exists( $destfilename ) ) {
				$img_url = $thumb_url;
			} else {
				$image   = new \claviska\SimpleImage( $img_path );
				$quality = zm_get_option( 'img_quality' ) ? zm_get_option( 'img_quality' ) : 80;

				if ( $crop ) {
					$image->thumbnail( $width, $height );
				} else {
					$image->resize( $width, $height );
				}

				$image->toFile( $destfilename, null, $quality );
				$img_url = $thumb_url;
			}

			if ( $single ) {
				return $img_url;
			} else {
				$img_info = getimagesize( $destfilename );
				return array(
					0 => $img_url,
					1 => $img_info[0],
					2 => $img_info[1],
				);
			}
		} catch ( Exception $e ) {
			error_log( 'SimpleImage resize error: ' . $e->getMessage() );
			return $url;
		}
	}
}

function zm_thumbnail_simpleimage() {
	global $post;
	$html   = '';
	$width  = zm_get_option( 'img_w' );
	$height = zm_get_option( 'img_h' );
	$rel    = zm_get_option( 'img_rel_nofollow' ) ? 'external nofollow' : 'bookmark';

	if ( get_post_meta( get_the_ID(), 'fancy_box', true ) ) {
		$html .= '<a class="fancy-box" rel="' . $rel . '" href="' . get_post_meta( get_the_ID(), 'fancy_box', true ) . '"></a>';
	}

	$beimg = str_replace( array( "\n", "\r", ' ' ), '', explode( ',', zm_get_option( 'random_image_url' ) ) );
	$src   = $beimg[ array_rand( $beimg ) ];

	if ( get_post_meta( get_the_ID(), 'thumbnail', true ) ) {
		$image = get_post_meta( get_the_ID(), 'thumbnail', true );
		$thumb = be_resize_image( $image, $width, $height, true );
		$html .= '<a class="sc" rel="' . $rel . '" ' . goal() . ' href="' . get_permalink() . '"><img src="' . $thumb . '" alt="' . $post->post_title . '" width="' . $width . '" height="' . $height . '"></a>';
	} elseif ( has_post_thumbnail() ) {
		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
		if ( $full_image_url ) {
			$thumb = be_resize_image( $full_image_url[0], $width, $height, true );
			$html .= '<a class="sc" rel="' . $rel . '" ' . goal() . ' href="' . get_permalink() . '"><img src="' . $thumb . '" alt="' . $post->post_title . '" width="' . $width . '" height="' . $height . '"></a>';
		}
	} else {
		$content = $post->post_content;
		preg_match_all( '/<img.*?(?: |\t|\r|\n)?src=["\']?(.+?)["\']?(?:(?: |\t|\r|\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER );
		$n = count( $strResult[1] );
		if ( $n > 0 && ! get_post_meta( get_the_ID(), 'rand_img', true ) ) {
			$first_img = $strResult[1][0];
			$thumb     = be_resize_image( $first_img, $width, $height, true );
			$html     .= '<a class="sc" rel="' . $rel . '" ' . goal() . ' href="' . get_permalink() . '"><img src="' . $thumb . '" alt="' . $post->post_title . '" width="' . $width . '" height="' . $height . '"></a>';
		} else {
			$thumb = be_resize_image( $src, $width, $height, true );
			$html .= '<a class="sc" rel="' . $rel . '" ' . goal() . ' href="' . get_permalink() . '"><img src="' . $thumb . '" alt="' . $post->post_title . '" width="' . $width . '" height="' . $height . '"></a>';
		}
	}

	return $html;
}

// 清理缩略图缓存
class Be_Thumb_Cache {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
	}

	function get_cache_dir() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/thumbnail';
	}

	function delete_cache() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		global $wp_filesystem;

		$dir = $this->get_cache_dir();
		if ( ! is_dir( $dir ) ) {
			return true;
		}

		$files = $wp_filesystem->dirlist( $dir );
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$wp_filesystem->delete( $dir . '/' . $file['name'], true );
			}
		}
		return true;
	}

	function add_admin_page() {
		add_management_page( '缩略图缓存', '<span class="bem"></span>缩略图', 'manage_options', 'crop-thumb', array( $this, 'admin_page' ) );
	}

	function admin_page() {
		if ( isset( $_POST['be_nonce'] ) && wp_verify_nonce( $_POST['be_nonce'], 'clear_cache' ) ) {
			$this->delete_cache();
			echo '<div class="updated"><p>所有缩略图缓存已删除！</p></div>';
		} ?>

		<div class="wrap">
			<h2>缩略图缓存</h2>

			<div class="card be-area-box">
				<p>缓存目录</p>
				<p><code><?php echo esc_html( $this->get_cache_dir() ); ?></code></p>
				<?php
				$dir        = $this->get_cache_dir();
				$file_count = 0;
				$dir_size   = 0;
				if ( is_dir( $dir ) && wp_is_writable( $dir ) ) :
					$files = scandir( $dir );
					foreach ( $files as $file ) {
						if ( $file !== '.' && $file !== '..' && is_file( $dir . '/' . $file ) ) {
							++$file_count;
							$dir_size += filesize( $dir . '/' . $file );
						}
					}
					?>
					<p>数量： <?php echo intval( $file_count ); ?></p>
					<p>大小： <?php echo size_format( $dir_size ); ?></p>
					<p style="color:#7AD03A">可写入</p>
					<p>清理缩略图缓存文件，之后会再次生成。</p>

				<?php else : ?>
					<p style="color:#A00">不可写入，请确保文件夹存在并有写入权限（755）!</p>
				<?php endif ?>
			</div>

			<form method="post" action="">
				<div class="submit">
					<?php wp_nonce_field( 'clear_cache', 'be_nonce' ); ?>
					<input class="button button-primary" value="清理缓存" type="submit">
				</div>
			</form>
		</div>
		<?php
	}
}
new Be_Thumb_Cache();
