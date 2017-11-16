<?php
/*
 * Opening Times Front End Submission Form
 *
 * Requires the CMB2 plugin 
 *
 * @subpackage        Opening Times
 * @subpackage        CMB2 https://github.com/WebDevStudios/CMB2
 * @author            Paul Flannery <psflannery@gmail.com>
 * @license           GPL-2.0+
 * @link              http://paulflannery.co.uk
 *
 * @wordpress-plugin
 *
 * Plugin Name:       OT front end submission form
 * Plugin URI:        http://otdac.org
 * Description:       Adds a front end submission form to the site via a short-code, so that visitors can add their own content.
 * Version:           1.2.6
 * Author:            Paul Flannery
 * Author URI:        http://paulflannery.co.uk
 * Text Domain:       opening_times
 * License:           GPL-2.0+
 * GitHub Plugin URI: https://github.com/psflannery/ot_front_end_submit
 * GitHub Branch:     master
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
 
// Plugin Directory 
define( 'OT_FRONT_END_DIR', dirname( __FILE__ ) );
include_once( OT_FRONT_END_DIR . '/inc/bv-link-submit.php' );
include_once( OT_FRONT_END_DIR . '/inc/customizer-controls.php' );
//include_once( OT_FRONT_END_DIR . '/inc/cmb2-js-validation-required.php' );
//include_once( OT_FRONT_END_DIR . '/inc/page_templater.php' );
include_once( OT_FRONT_END_DIR . '/inc/extras.php' );
