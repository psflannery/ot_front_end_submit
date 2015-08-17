<?php
/*
 * Opening Times Front End Submission Form
 * 
 *
 * @package		Opening Times
 * @author		Paul Flannery <psflannery@gmail.com>
 * @license		GPL-2.0+
 * @link		http://paulflannery.co.uk
 *
 * @wordpress-plugin
 * Requires the CMB2 plugin
 * @link		https://github.com/WebDevStudios/CMB2
 *
 * Plugin Name:	OT front end submission form
 * Plugin URI:	http://otdac.org
 * Description:	Adds a front end submission form to the site via a short-code, so that visitors can add their own content.
 * Version: 	1.0.1
 * Author: 		Paul Flannery
 * Author URI:	http://paulflannery.co.uk
 * Text Domain:	opening_times
 * License: 	GPL-2.0+
 * GitHub Plugin URI: https://github.com/psflannery/ot_front_end_submit
 * GitHub Branch: master
 *
 * Copyright 2015 Paul Flannery <psflannery@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

// Plugin Directory 
define( 'OT_FRONT_END_DIR', dirname( __FILE__ ) );

include_once( OT_FRONT_END_DIR . '/inc/bv-link-submit.php' );
