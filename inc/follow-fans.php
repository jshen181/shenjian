<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// 关注
function follow_list() {
	$user_id     = get_current_user_id();
	$meta        = get_user_meta( $user_id, 'follow', true );
	$followlist = explode( ',', $meta );
	$users       = get_users( array( 'include' => $followlist ) );
	$follow_text   = __( '关注', 'begin' );
	$followed_text = __( '已关注', 'begin' );
	
?>

<h4><?php _e( '我的关注', 'begin' ); ?><span class="m-number"><?php echo get_follow_count( $user_id ); ?></span></h4>

<div class="follow-area">
	<?php foreach ( $users as $user ) : ?>
		<div class="follow-item">
			<div class="follow-list">
				<a href="<?php echo get_author_posts_url( $user->ID ); ?>" target="_blank" class="follow-user-link"></a>
				<div class="user-pic load">
					<?php if ( zm_get_option( 'cache_avatar' ) ) { ?>
						<?php echo begin_avatar( $user->user_email, $size = 128, '', $user->display_name ); ?>
					<?php } else { ?>
						<?php 
							if ( ! zm_get_option( 'avatar_load' ) ) {
								echo get_avatar( $user->user_email, $size = 128, '', $user->display_name );
							} else {
								echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. $user->display_name .'" width="128" height="128" data-original="' . preg_replace( array( '/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i' ), array( '', '' ), get_avatar( $user->user_email, $size = 128, '', $user->display_name ) ) . '">';
							}
						?>
					<?php } ?>
				</div>
				<div class="user-dec">
					<div class="dec-name">
						<?php echo $user->display_name; ?>
					</div>
					<div class="dec-sub ease">
						<span class="dec-fans"><?php _e( '粉丝', 'begin' ); ?><i><?php echo get_fans_count( $user->ID ); ?></i></span>
						<span class="dec-follow"><?php _e( '关注', 'begin' ); ?><i><?php echo get_follow_count( $user->ID ); ?></i></span>
					</div>
					<div class="addfollow-btn ease">
						<a href="javascript:;" user="<?php echo $user->ID; ?>" class="addfollow be-followed" data-follow-text="<?php echo esc_attr( $follow_text ); ?>" data-followed-text="<?php echo esc_attr( $followed_text ); ?>">
							<?php echo esc_html( $followed_text ); ?>
						</a>
					</div>

					<div class="follow-posts-count ease">
						<?php _e( '文章', 'begin' ); ?><i><?php echo count_user_posts( $user->ID ); ?></i>
					</div>
					<?php if ( get_user_post_update( $user->ID ) ) {?>
						<div class="have-update"></div>
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php }

function fans_list() {
	$user_id  = get_current_user_id();
	$fans = get_user_meta( $user_id, 'fans', true );
	$fanslist = explode( ',', $fans );
	$focused     = get_users(array( 'include' => $fanslist ) );
	$follow_text   = __( '互关', 'begin' );
	$followed_text = __( '已互关', 'begin' );
?>

<h4><?php _e( '我的粉丝', 'begin' ); ?><span class="m-number"><?php echo get_fans_count( $user_id ); ?></span></h4>

<div class="follow-area">
	<?php foreach ( $focused as $fensi ) : ?>
		<div class="follow-item">
			<div class="follow-list">
				<a href="<?php echo get_author_posts_url( $fensi->ID ); ?>" target="_blank" class="follow-user-link"></a>
				<div class="user-pic load">
					<?php if ( zm_get_option( 'cache_avatar' ) ) { ?>
						<?php echo begin_avatar( $fensi->user_email, $size = 128, '', $fensi->display_name ); ?>
					<?php } else { ?>
						<?php 
							if ( ! zm_get_option( 'avatar_load' ) ) {
								echo get_avatar( $fensi->user_email, $size = 128, '', $fensi->display_name );
							} else {
								echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. $fensi->display_name .'" width="128" height="128" data-original="' . preg_replace( array( '/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i' ), array( '', '' ), get_avatar( $fensi->user_email, $size = 128, '', $fensi->display_name ) ) . '">';
							}
						?>
					<?php } ?>
				</div>
				<div class="user-dec">
					<div class="dec-name">
						<?php echo $fensi->display_name; ?>
					</div>
					<div class="dec-sub ease">
						<span class="dec-fans"><?php _e( '粉丝', 'begin' ); ?><i><?php echo get_fans_count( $fensi->ID ); ?></i></span>
						<span class="dec-follow"><?php _e( '关注', 'begin' ); ?><i><?php echo get_follow_count( $fensi->ID ); ?></i></span>
					</div>
					<div class="addfollow-btn ease">
						<?php 
							$meta = get_user_meta( get_current_user_id(), 'follow', true );
							$guanzhu = $meta ? explode( ',', $meta ) : array();
							$user = $fensi->ID;
						?>
						<?php if ( in_array( $user, $guanzhu ) ) { ?>
							<a href="javascript:;" user="<?php echo $fensi->ID; ?>" class="addfollow be-followed" data-follow-text="<?php echo esc_attr( $follow_text ); ?>" data-followed-text="<?php echo esc_attr( $followed_text ); ?>">
								<?php echo esc_html( $followed_text ); ?>
							</a>
						<?php } else { ?>
							<a href="javascript:;" user="<?php echo $fensi->ID; ?>" class="addfollow be-follow" data-follow-text="<?php echo esc_attr( $follow_text ); ?>" data-followed-text="<?php echo esc_attr( $followed_text ); ?>">
								<?php echo esc_html( $follow_text ); ?>
							</a>
						<?php } ?>
					</div>

					<div class="follow-posts-count ease">
						<?php _e( '文章', 'begin' ); ?><i><?php echo count_user_posts( $fensi->ID ); ?></i>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php }