<?php
/**
 * Extra functionality to aid the layout and display
 * 
 * @package: OT front end submission form
 * @subpackage: Opening Times
 */
 
/**
 * Return the the name of the person who submitted a link
 */
function opening_times_get_link_submitter() {
	$submitted_by = get_post_meta( get_the_ID(), '_ot_bv_link_submit_name', true );
	$label = esc_html__( 'Submitted by: ', 'opening_times' );

	$name = '' != $submitted_by ? $submitted_by : esc_html__( 'Anonymous', 'opening_times' );

	return $label . $name ;
}

/**
 * Echo the the name of the person who submitted a link
 */
function opening_times_do_link_submitter() {
	echo opening_times_get_link_submitter();
}
