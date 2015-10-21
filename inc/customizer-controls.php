<?php
/**
 * Customizer Controls
 *
 * Version: 1.0
 * Author: Paul Flannery
 * Author URI: http://www.paulflannery.co.uk
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ot_customizer_controls
 *
 * @category Opening Times
 * @package  OT functionality
 */

function ot_bv_front_end_submit_customize_register( $wp_customize ) {
	$wp_customize->add_section(	
		'ot_bv_user_selected_links',
		array(
			'title'       => 'User Submitted Links',
			'description' => sprintf( __( 'Configure the category and user to loop through for the submitted links section.', 'opening_times' ) ),
			'priority'    => 125
		)
	);

	// Category Select
	require_once dirname(__FILE__) . '/customizer-controls/category-dropdown-custom-control.php';
	$wp_customize->add_setting(
		'ot_bv_user_selected_links_cat',
		array(
			'sanitize_callback' => 'ot_front_end_submit_sanitize_integer',
		)
	);

	$wp_customize->add_control(
		new Category_Dropdown_Custom_Control(
			$wp_customize,
			'ot_bv_user_selected_links_cat',
			array(
				'label'    => 'Category',
				'section'  => 'ot_bv_user_selected_links',
			)
		)
	);

	// User Select
	require_once dirname(__FILE__) . '/customizer-controls/user-dropdown-custom-control.php';
	$wp_customize->add_setting(
		'ot_bv_user_selected_links_author',
		array(
			'sanitize_callback' => 'ot_front_end_submit_sanitize_integer',
		)
	);

	$wp_customize->add_control(
		new User_Dropdown_Custom_Control(
			$wp_customize,
			'ot_bv_user_selected_links_author',
			array(
				'label'    => 'User',
				'section'  => 'ot_bv_user_selected_links',
			)
		)
	);
}
add_action( 'customize_register', 'ot_bv_front_end_submit_customize_register' );

/**
 * Sanitize the Integer Inputs.
 */
function ot_front_end_submit_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}
