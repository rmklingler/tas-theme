<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package ThemeGrill
 * @subpackage Spacious
 * @since Spacious 1.0
 */
?>

<div id="secondary">
	<?php do_action( 'spacious_before_sidebar' ); ?>
		<?php
			$sidebar = 'tas_business_lines_sidebar';
		?>

	<?php if ( is_active_sidebar( 'tas_business_lines_sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'tas_business_lines_sidebar' ); ?>
	<?php else : ?>

			<aside id="search" class="widget widget_search">
				<?php get_search_form(); ?>
			</aside>

			<aside id="archives" class="widget">
				<h3 class="widget-title"><?php _e( 'Archives', 'spacious' ); ?></h3>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</aside>

			<aside id="meta" class="widget">
				<h3 class="widget-title"><?php _e( 'Meta', 'spacious' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</aside>

		<?php endif; ?>
	<?php do_action( 'spacious_after_sidebar' ); ?>
</div>