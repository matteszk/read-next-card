<?php
/**
 * Plugin Name:       Read Next Card
 * Plugin URI:        https://github.com/matteszk/read-next-card
 * Description:       A "Further reading" card block that links to a chosen post, or automatically picks the most related one.
 * Version:           0.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Matthew Szklarz
 * Author URI:        https://matthewszklarz.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       read-next-card
 * Update URI:        false
 */

// Block direct access to this file outside WordPress.
defined( 'ABSPATH' ) || exit;

/**
 * Register the block. It's dynamic: the post stores only the chosen ID (or 0
 * for automatic), and the card is rendered fresh on each view so titles and
 * images stay current.
 */
function rnc_register_block() {
	register_block_type(
		__DIR__ . '/block',
		array( 'render_callback' => 'rnc_render_block' )
	);
}
add_action( 'init', 'rnc_register_block' );

/**
 * Find the post that shares the most categories and tags with the given post.
 * Ties go to the most recent post.
 *
 * @param int $post_id The post the card appears in.
 * @return int Related post ID, or 0 if none share a term.
 */
function rnc_find_related( $post_id ) {
	$categories = wp_get_post_categories( $post_id );
	$tags       = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );

	if ( ! $categories && ! $tags ) {
		return 0;
	}

	// Only query taxonomies the post actually uses; an empty "terms" clause misbehaves.
	$tax_query = array( 'relation' => 'OR' );
	if ( $categories ) {
		$tax_query[] = array(
			'taxonomy' => 'category',
			'terms'    => $categories,
		);
	}
	if ( $tags ) {
		$tax_query[] = array(
			'taxonomy' => 'post_tag',
			'terms'    => $tags,
		);
	}

	// Pull recent candidates that share at least one term.
	$candidates = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'post__not_in'   => array( $post_id ),
			'no_found_rows'  => true,
			'tax_query'      => $tax_query,
		)
	);

	// Score each candidate by how many terms it shares. get_posts() returns
	// newest first, so a strict ">" keeps the newest post on a tie.
	$best_id    = 0;
	$best_score = 0;
	foreach ( $candidates as $candidate ) {
		$shared = count( array_intersect( $categories, wp_get_post_categories( $candidate->ID ) ) )
			+ count( array_intersect( $tags, wp_get_post_tags( $candidate->ID, array( 'fields' => 'ids' ) ) ) );

		if ( $shared > $best_score ) {
			$best_score = $shared;
			$best_id    = $candidate->ID;
		}
	}

	return $best_id;
}

/**
 * Render the card.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Unused (dynamic block).
 * @param WP_Block $block      Block instance, used for the current post's ID.
 * @return string
 */
function rnc_render_block( $attributes, $content = '', $block = null ) {
	$current_id = $block->context['postId'] ?? get_the_ID();
	$target_id  = (int) ( $attributes['postId'] ?? 0 );

	// 0 means "automatic".
	if ( ! $target_id && $current_id ) {
		$target_id = rnc_find_related( $current_id );
	}

	$target = $target_id ? get_post( $target_id ) : null;
	if ( ! $target || 'publish' !== $target->post_status ) {
		return '';
	}

	$title = wp_trim_words( get_the_title( $target ), 12 );

	ob_start();
	?>
	<aside class="rnc">
		<a class="rnc__card" href="<?php echo esc_url( get_permalink( $target ) ); ?>">
			<?php if ( has_post_thumbnail( $target ) ) : ?>
				<span class="rnc__image"><?php echo get_the_post_thumbnail( $target, 'medium', array( 'alt' => '' ) ); ?></span>
			<?php endif; ?>
			<span class="rnc__text">
				<span class="rnc__kicker"><?php esc_html_e( 'Further reading', 'read-next-card' ); ?></span>
				<span class="rnc__title"><?php echo esc_html( $title ); ?></span>
			</span>
		</a>
	</aside>
	<?php
	return ob_get_clean();
}
