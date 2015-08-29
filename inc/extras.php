<?php
/**
 * Extra functionality to aid the layout and display
 * 
 * @package: OT front end submission form
 * @subpackage: Opening Times
 */

 
/**
 * Adds custom classes to the array of post classes.
 *
 * @param array $classes Classes for the posts.
 * @return array
 *
 */
function opening_times_bv_link_submit_post_classes( $classes, $class, $post_id ) {
    if ( is_page_template( 'bv-submitted-links.php' )  ) {
		$classes[] = 'strap-container';
		$classes[] = 'veiled';
    }
 
    return $classes;
}
add_filter( 'post_class', 'opening_times_bv_link_submit_post_classes', 10, 3 );

 
/**
 * Output the the name of the person who submitted a link
 */
function opening_times_bv_link_submitter() {
	global $post;
	$submitted_by = get_post_meta( $post->ID, '_ot_bv_link_submit_name', true );
	if ( '' != $submitted_by ) :
		echo $submitted_by;
	else :
		esc_html_e( 'Anonymous', 'opening_times' );
	endif;
}
