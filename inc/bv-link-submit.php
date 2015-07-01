<?php
/**
 * Front end submission form
 *
 * @package Opening Times
 */
 
/**
 * Register the form and fields for our front-end submission form
 */
function ot_frontend_form_register() {
	
	$prefix = '_ot_bv_link_submit_';
	
	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'front-end-post-form',
		'object_types' => array( 'article' ),
		'hookup'       => false,
		'save_fields'  => false,
	) );

	$cmb->add_field( array(
		'name' => __( 'Name', 'opening_times' ),
		'id'   => $prefix . 'name',
		'type' => 'text',
		'attributes'  => array(
			'class' => 'form-control form-control-small',
			//'placeholder' => 'Your name',
		),
		'row_classes' => 'form-group',
	) );
	
	$cmb->add_field( array(
		'name'    => __( 'Link', 'opening_times' ),
		'id'      => $prefix . 'link',
		'type'    => 'text',
		'attributes'  => array(
			'class' => 'form-control form-control-small',
			//'placeholder' => 'Submit a link',
			'required'    => 'required',
		),
		'row_classes' => 'form-group',
	) );
	
	$cmb->add_field( array(
		'name' => __( 'Reason', 'opening_times' ),
		'id'   => $prefix . 'reason',
		'type' => 'textarea',
		'attributes'  => array(
			'class' => 'form-control',
			//'placeholder' => 'A small amount of text describing why you submitted this link.',
			'required'    => 'required',
			'rows'        => 6,
		),
		'row_classes' => 'form-group',
	) );
}
add_action( 'cmb2_init', 'ot_frontend_form_register' );

/**
 * Gets the front-end-post-form cmb instance
 *
 * @return CMB2 object
 */
function ot_frontend_cmb2_get() {
	// Use ID of metabox in ot_frontend_form_register
	$metabox_id = '_ot_bv_link_submit_front-end-post-form';

	// Post/object ID is not applicable since we're using this form for submission
	$object_id  = 'fake-object-id';

	// Get CMB2 metabox object
	return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Handle the user-submit-frontend-form shortcode
 *
 * @param  array  $atts Array of shortcode attributes
 * @return string       Form html
 */
function ot_do_frontend_form_submission_shortcode( $atts = array() ) {

	// Get CMB2 metabox object
	$cmb = ot_frontend_cmb2_get();

	// Get $cmb object_types
	$post_types = $cmb->prop( 'object_types' );

	// Current user
	$user_id = get_current_user_id();

	// Parse attributes
	$atts = shortcode_atts( array(
		//'post_author' => $user_id ? $user_id : 7, // Current user, or admin
		'post_author' => 7, // Current user, or admin
		'post_status' => 'pending',
		'post_type'   => reset( $post_types ), // Only use first object_type in array
	), $atts, 'user-submit-frontend-form' );

	/*
	 * Let's add these attributes as hidden fields to our cmb form
	 * so that they will be passed through to our form submission
	 */
	foreach ( $atts as $key => $value ) {
		$cmb->add_hidden_field( array(
			'field_args'  => array(
				'id'    => "atts[$key]",
				'type'  => 'hidden',
				'default' => $value,
			),
		) );
	}

	// Initiate our output variable
	$output = '';

	// Get any submission errors
	if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
		// If there was an error with the submission, add it to our ouput.
		$output .= '<h3>' . sprintf( __( 'There was an error in the submission: %s', 'ot-post-submit' ), '<strong>'. $error->get_error_message() .'</strong>' ) . '</h3>';
	}

	// If the post was submitted successfully, notify the user.
	if ( isset( $_GET['post_submitted'] ) && ( $post = get_post( absint( $_GET['post_submitted'] ) ) ) ) {

		// Get submitter's name
		$name = get_post_meta( $post->ID, 'submitted_author_name', 1 );
		$name = $name ? ' '. $name : '';

		// Add notice of submission to our output
		$output .= '<h3>' . sprintf( __( 'Thank you%s, your new post has been submitted and is pending review by a site administrator.', 'ot-post-submit' ), esc_html( $name ) ) . '</h3>';
	}

	// Get our form
	$output .= cmb2_get_metabox_form( $cmb, 'fake-object-id', array( 'save_button' => __( 'Submit', 'ot-post-submit' ) ) );

	return $output;
}
add_shortcode( 'user-submit-frontend-form', 'ot_do_frontend_form_submission_shortcode' );

/**
 * Filter the form to add a class to the save button and match the OT styles
 *
 */
function opening_times_cmb2_save_form_modify( $form_format ) {	
	$form_format = str_replace( 'class="button-primary"', 'class="ot-button ot-button-no-float button-primary"', $form_format );
	return $form_format;
}
add_filter( 'cmb2_get_metabox_form_format', 'opening_times_cmb2_save_form_modify' );

/**
 * Handles form submission on save. Redirects if save is successful, otherwise sets an error message as a cmb property
 *
 * @return void
 */
function ot_handle_frontend_new_post_form_submission() {

	// If no form submission, bail
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}

	// Get CMB2 metabox object
	$cmb = ot_frontend_cmb2_get();

	$post_data = array();

	// Get our shortcode attributes and set them as our initial post_data args
	if ( isset( $_POST['atts'] ) ) {
		foreach ( (array) $_POST['atts'] as $key => $value ) {
			$post_data[ $key ] = sanitize_text_field( $value );
		}
		unset( $_POST['atts'] );
	}

	// Check security nonce
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', __( 'Security check failed.' ) ) );
	}

	// Check title submitted
	if ( empty( $_POST['_ot_bv_link_submit_link'] ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', __( 'New post requires a title.' ) ) );
	}

	// And that the title is not the default title
	if ( $cmb->get_field( '_ot_bv_link_submit_link' )->default() == $_POST['_ot_bv_link_submit_link'] ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', __( 'Please enter a new title.' ) ) );
	}

	/**
	 * Fetch sanitized values
	 */
	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	// Set our post data arguments
	$post_data['post_title']   = $sanitized_values['_ot_bv_link_submit_link'];
	unset( $sanitized_values['_ot_bv_link_submit_link'] );
	$post_data['post_content'] = $sanitized_values['_ot_bv_link_submit_reason'];
	unset( $sanitized_values['_ot_bv_link_submit_reason'] );

	// Create the new post
	$new_submission_id = wp_insert_post( $post_data, true );

	// If we hit a snag, update the user
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	/**
	 * Other than post_type and post_status, we want
	 * our uploaded attachment post to have the same post-data
	 */
	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );
	unset( $post_data['post_category'] );

	// Loop through remaining (sanitized) data, and save to post-meta
	foreach ( $sanitized_values as $key => $value ) {
		update_post_meta( $new_submission_id, $key, $value );
	}

	/*
	 * Redirect back to the form page with a query variable with the new post ID.
	 * This will help double-submissions with browser refreshes
	 */
	wp_redirect( esc_url_raw( add_query_arg( 'post_submitted', $new_submission_id ) ) );
	exit;
}
add_action( 'cmb2_after_init', 'ot_handle_frontend_new_post_form_submission' );
