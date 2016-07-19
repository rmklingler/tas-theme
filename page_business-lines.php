<?php 
/**
 * Template Name: Business Lines
 *
 * @package TAS Theme
 * @subpackage Spacious
 * @since Spacious 1.0
 */
?>

<?php get_header( 'business-lines' ); ?>

	<?php do_action( 'spacious_before_body_content' ); ?>

	<div id="primary">
		<div id="content" class="clearfix">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
					do_action( 'spacious_before_comments_template' );
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();					
	      		do_action ( 'spacious_after_comments_template' );
				?>

			<?php endwhile; ?>

		</div><!-- #content -->

		<?php
			$tas_post_tag = get_post_meta( get_the_ID() , "tas_post_tag_id", true);
			if (!empty($tas_post_tag)) {
				echo '<div class="bl-related-posts"><h3 class="widget-title">Related Posts</h3>';
				echo do_shortcode('[rpwe limit="5" tag="' . $tas_post_tag .'" thumb_default="" thumb="false" excerpt=true length=75 styles_default=false]');
				echo '</div>';
			} ?>

	</div><!-- #primary -->
	
	<?php get_sidebar( 'business-lines' ); ?>

	<?php do_action( 'spacious_after_body_content' ); ?>

<?php get_footer(); ?>