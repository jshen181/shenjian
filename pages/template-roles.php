<?php
/*
Template Name: 创作团队
*/
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); ?>

<main id="main" class="be-main author-content" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" class="post-item post ms" <?php aos_a(); ?>>
			<?php if ( get_post_meta( get_the_ID(), 'header_img', true ) || get_post_meta( get_the_ID(), 'header_bg', true ) ) { ?>
			<?php } else { ?>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>
			<?php } ?>
			<div class="entry-content">
				<div class="single-content">
					<?php the_content(); ?>
					<?php edit_post_link( '<i class="be be-editor"></i>', '<div class="page-edit-link edit-link">', '</div>' ); ?>
					<div class="clear"></div>
				</div>
			</div>
		</article>
	<?php endwhile; ?>

	<div class="author-page<?php if ( cx_get_option( 'roles_mode' ) == 'normal' ) { ?> author-wrap<?php } ?>">
		<?php
			global $wp_roles;
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			$roles = $wp_roles->get_names();
			$roles = $wp_roles->get_names();
			foreach ( $roles as $role => $role_name ) {
				$users = get_users( array( 'role' => $role ) );
				$has_capable_users = false;

				$administrator = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'administrator', cx_get_option( 'no_roles' ) ) ? 'administrator' : '';
				$editor        = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'editor', cx_get_option( 'no_roles' ) ) ? 'editor' : '';
				$authors       = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'author', cx_get_option( 'no_roles') ) ? 'author' : '';
				$contributor   = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'contributor', cx_get_option( 'no_roles' ) ) ? 'contributor' : '';

				// 筛选并排序用户
				$users = array_filter( $users, function( $user ) use ( $administrator, $editor, $authors, $contributor ) {
					return $user->roles[0] !== $administrator && $user->roles[0] !== $editor && $user->roles[0] !== $authors && $user->roles[0] !== $contributor && user_can( $user, 'edit_posts' );
				});

				usort( $users, function( $a, $b ) {
					return count_user_posts( $b->ID ) - count_user_posts( $a->ID );
				});

				if ( ! cx_get_option( 'rolescss' ) || ( cx_get_option( 'rolescss' ) == 'img' ) ) {
					$rolecss = ' roleimg';
				}

				if ( cx_get_option( 'rolescss' ) == 'grid' ) {
					$rolecss = ' rolegrid';
				}

				if ( $users ) { ?>
					<?php if ( ! cx_get_option( 'roles_mode' ) || ( cx_get_option( 'roles_mode' ) == 'roles' ) ) { ?>
					<article class="role-user-box">
						<div class="role-name-title group-title"><h3><?php echo translate_user_role( $role_name ); ?></h3></div>
						<div class="role-user-main">
					<?php } ?>
							<?php 
								foreach ( $users as $user ) {
									$administrator = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'administrator', cx_get_option( 'no_roles' ) ) ? 'administrator' : '';
									$editor        = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'editor', cx_get_option( 'no_roles' ) ) ? 'editor' : '';
									$authors       = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'author', cx_get_option( 'no_roles') ) ? 'author' : '';
									$contributor   = is_array( cx_get_option( 'no_roles' ) ) && in_array( 'contributor', cx_get_option( 'no_roles' ) ) ? 'contributor' : '';
	
									$username = ( get_bloginfo( 'language' ) === 'en-US' ) ? $user->last_name : $user->display_name;
									$remark = ( get_bloginfo( 'language' ) === 'en-US' ) ? 'remarken' : 'remark';
									if ( cx_get_option( 'roles_post' ) ) {
										$published_posts = count_user_posts( $user->ID, 'post' ) > 0;
									} else { 
										$published_posts = true;
									}
									if ( $published_posts && $role !== $administrator && $role !== $editor && $role !== $authors && $role !== 'contributor' &&  user_can( $user, 'edit_posts' ) ) { ?>
										<div class="role-user-item rolefl-<?php echo cx_get_option( 'roles_f' ); ?><?php echo $rolecss; ?>">
											<div class="boxs6">
												<div class="role-user-area">
													<a class="role-user-link" href="<?php echo get_author_posts_url( $user->ID ); ?>" rel="nofollow" target="_blank"></a>
													<div class="role-user-avatar load">
														<?php if ( zm_get_option( 'cache_avatar' ) ) { ?>
															<?php echo begin_avatar( $user->user_email, $size = 128, '', $username ); ?>
														<?php } else { ?>
															<?php be_avatar_roles(); ?>
														<?php } ?>
													</div>
													<?php if ( get_user_post_update( $user->ID ) ) {?>
														<div class="have-update"></div>
													<?php } ?>
													<?php if ( get_the_author_meta( 'last_name', $user->ID ) ) { ?>
														<div class="role-user-name"><?php echo $username; ?></div>
													<?php } else { ?>
														<div class="role-user-name"><?php echo $user->display_name; ?></div>
													<?php } ?>
													<?php if ( get_the_author_meta( $remark, $user->ID ) ) { ?>
														<div class="user-remark"><div class="role-user-remark"><?php echo the_author_meta( $remark, $user->ID ); ?></div></div>
													<?php } ?>

													<?php if ( zm_get_option( 'follow_btn' ) ) { ?>
														<div class="follow-btn"><?php be_follow_btn( $btn ='team' ); ?></div>
													<?php } ?>

													<?php if ( current_user_can( 'administrator' ) ) { ?>
														<a class="role-user-edit" href="<?php echo admin_url(); ?>/user-edit.php?user_id=<?php echo $user->ID; ?>&wp_http_referer=/wp-admin/users.php" target="_blank"><?php _e( '编辑', 'begin' ); ?></a>
													<?php } ?>

													<?php if ( cx_get_option( 'roles_des' ) ) { ?>
														<?php if ( get_the_author_meta( 'description', $user->ID ) ) { ?>
															<div class="role-user-des<?php if ( cx_get_option( 'roles_des_clamp' ) ) { ?> roles-des-clamp<?php } ?>">
																<?php echo mb_substr( get_the_author_meta( 'description', $user->ID ), 0, cx_get_option( 'roles_des_count' ), 'UTF-8' ) . '...'; ?>
															</div>
														<?php } ?>
														<?php if ( ! cx_get_option( 'rolescss' ) == 'grid' ) { ?><div class="clear"></div><?php } ?>
													<?php } ?>

													<div class="role-user-count">
														<div class="role-user-inf user-post-count"><span><?php _e( '文章', 'begin' ); ?></span><?php echo count_user_posts( $user->ID ); ?></div>
														<div class="role-user-inf user-comments-count"><span><?php _e( '评论', 'begin' ); ?></span><?php echo $comments = get_comments( array( 'user_id' => $user->ID, 'count' => true ) ); ?></div>
													</div>
														
													<?php if ( cx_get_option( 'rolescss' ) == 'grid' ) { ?><div class="clear"></div><?php } else { ?><div class="role-user-floor"></div><?php } ?>
												</div>
											</div>
										</div>
									<?php }
								}
							?>
						<?php if ( ! cx_get_option( 'roles_mode' ) || ( cx_get_option( 'roles_mode' ) == 'roles' ) ) { ?>
						</div>
						<?php } ?>
					</article>
				<?php
				}
			}
		?>
	<?php cx_help( $text = '排除角色，辅助设置 → 综合设置 → 创作团队页面', $base = '综合设置', $go = '创作团队页面' ); ?>
	<div class="clear"></div>
</div>
</main>

<?php get_footer(); ?>