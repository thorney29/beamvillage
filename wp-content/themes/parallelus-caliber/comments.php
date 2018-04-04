<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to rf_list_comment() which is
 * located in the includes/template-tags.php file.
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if ( post_password_required() ) {
	return;
}
?>

<?php do_action( 'before_comments' ); ?>
<div id="comments" class="comments-area">

<?php // You can start editing here -- including this comment! ?>

<?php if ( have_comments() ) : ?>
	<header class="comments-header">
		<h2 class="comments-title">
			<?php
				// printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'parallelus-caliber' ),
					// number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
				_e('Comments', 'parallelus-caliber');
			?>
		</h2>
	</header>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<nav id="comment-nav-above" class="comment-navigation">
		<h5 class="screen-reader-text"><?php _e( 'Comment navigation', 'parallelus-caliber' ); ?></h5>
		<ul class="pager">
			<li class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'parallelus-caliber' ) ); ?></li>
			<li class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'parallelus-caliber' ) ); ?></li>
		</ul>
	</nav><!-- #comment-nav-above -->
	<?php endif; // check for comment navigation ?>

	<?php do_action( 'before_comment_list' ); ?>
	<ol class="comment-list media-list">
		<?php
			/* Loop through and list the comments. Tell wp_list_comments()
			 * to use rf_list_comment() to format the comments.
			 * If you want to overload this in a child theme then you can
			 * define rf_list_comment() and that will be used instead.
			 * See rf_list_comment() in extensions/template-functions for more.
			 */
			wp_list_comments( array( 'callback' => 'rf_list_comment', 'avatar_size' => 50 ) );
		?>
	</ol><!-- .comment-list -->
	<?php do_action( 'after_comment_list' ); ?>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<nav id="comment-nav-below" class="comment-navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'parallelus-caliber' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'parallelus-caliber' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'parallelus-caliber' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // check for comment navigation ?>

<?php endif; // have_comments() ?>

<?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
?>
	<p class="no-comments"><?php _e( 'Comments are closed.', 'parallelus-caliber' ); ?></p>
<?php endif; ?>

<?php do_action( 'before_comment_form' ); ?>
<?php comment_form( $args = array(
		  'id_form'           => 'commentform',  // that's the wordpress default value! delete it or edit it ;)
		  'id_submit'         => 'commentsubmit',
		  'title_reply'       => __( 'Leave a Reply', 'parallelus-caliber' ),  // that's the wordpress default value! delete it or edit it ;)
		  'title_reply_to'    => __( 'Leave a Reply to %s', 'parallelus-caliber' ),  // that's the wordpress default value! delete it or edit it ;)
		  'cancel_reply_link' => __( 'Cancel Reply', 'parallelus-caliber' ),  // that's the wordpress default value! delete it or edit it ;)
		  'label_submit'      => __( 'Post Comment', 'parallelus-caliber' ),  // that's the wordpress default value! delete it or edit it ;)

		  'comment_field' =>  '<p><textarea placeholder="Start typing..." id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',

		  'comment_notes_after' => '<div class="form-allowed-tags-wrapper"><p class="form-allowed-tags">' .
			__( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'parallelus-caliber' ) .
			'</p><div class="well well-sm"><small>' . allowed_tags() . '</small></div></div>'

));
?>
<?php do_action( 'after_comment_form' ); ?>

</div><!-- #comments -->
<?php do_action( 'after_comments' ); ?>