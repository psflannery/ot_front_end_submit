<?php
/**
 * Front end submission form
 *
 * @package Opening Times
 */
 
/**
 * Register the form and fields for our front-end submission form
 *
 * @link https://justmarkup.com/log/2012/12/28/input-url/
 * @link http://stackoverflow.com/questions/17946960/with-html5-url-input-validation-assume-url-starts-with-http
 */
function ot_frontend_form_register() {
	
	$prefix = '_ot_bv_link_submit_';
	
	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'front-end-post-form',
		'object_types' => array( 'article' ),
		'hookup'       => false,
		'save_fields'  => false,
		'cmb_styles'   => false,
	) );

	$cmb->add_field( array(
		'name' => __( 'Name', 'opening_times' ),
		'id'   => $prefix . 'name',
		'type' => 'text',
		'attributes'  => array(
			'class' => 'form-control form-control-small',
		),
		'row_classes' => 'form-group',
	) );
	
	$cmb->add_field( array(
		'name'    => __( 'Link', 'opening_times' ),
		'id'      => $prefix . 'link',
		'type'    => 'text_url',
		'attributes'  => array(
			'class' => 'form-control form-control-small auto-protocol',
			'required' => 'required',
			'type' => 'url',
			//'pattern' => '@(https?|ftp|torrent|image|irc)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?$@iS',
			//'pattern' => '^(https?://)?([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$',
			//'pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
			//'pattern' => '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?',
			//'pattern' => '/(?:https?:\/\/)?(?:[\w]+\.)([a-zA-Z\.]{2,6})([\/\w\.-]*)*\/?/g',
		),
		'row_classes' => 'form-group',
	) );
	
	$cmb->add_field( array(
		'name' => __( 'Reason', 'opening_times' ),
		'id'   => $prefix . 'reason',
		'type' => 'textarea',
		'attributes'  => array(
			'class' => 'form-control textarea-vert',
			'required' => 'required',
			'rows' => 6,
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
	//$user_id = get_current_user_id();
	$user_id = 9;

	// Post Category
	//$post_categories = array(22); // Website, Uncategorised

	// Parse attributes
	$atts = shortcode_atts( array(
		'post_author' => $user_id ? $user_id : 1, // Current user, or admin
		'post_status' => 'pending',
		'post_type'   => reset( $post_types ), // Only use first object_type in array
		// new
		//'post_category'  => reset( $post_categories ),
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
		$output .= '<h3 class="label label-error">' . sprintf( __( 'There was an error in the submission: %s', 'ot-post-submit' ), '<strong>'. $error->get_error_message() .'</strong>' ) . '</h3>';
	}

	// If the post was submitted successfully, notify the user.
	if ( isset( $_GET['post_submitted'] ) && ( $post = get_post( absint( $_GET['post_submitted'] ) ) ) ) {

		// Get submitter's name
		$name = get_post_meta( $post->ID, '_ot_bv_link_submit_name', 1 );
		$name = $name ? ' '. $name : '';

		// Add notice of submission to our output
		$output .= '<h3 class="label label-success">' . sprintf( __( 'Thank you%s, your link has been submitted and is pending review.', 'ot-post-submit' ), esc_html( $name ) ) . '</h3>';
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

	// Check the link is valid
	$url = $sanitized_values['_ot_bv_link_submit_link'];
	$response = wp_safe_remote_head( $url, array( 'timeout' => 5 ) );
	$accepted_status_codes = array( 200, 301, 302 );

	if ( $_POST['_ot_bv_link_submit_link'] && ! in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes )  ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'invalid_url', __( 'That URL doesn\'t seem to exist or is currently down, please try again.' ) ) );
	}

	// Get title of the website via link and use as post title
	function get_title_from_url($url){
		if ( ! is_admin() ) {
			$response = wp_safe_remote_head( $url, array( 'timeout' => 5 ) );
			//$accepted_status_codes = array( 200, 301, 302 );

			//if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes ) ) {
			if ( ! is_wp_error( $response ) ) {

				$str = wp_remote_retrieve_body( wp_remote_get( $url ) );

				if( strlen($str)>0 ){
					$str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
					preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case
					return $title[1];
				}
			}
			return '(THIS IS A BADLY FORMED OR INCORRECT URL, WE CAN\'T USE IT) ' . $url;
		}
	}

	// Set the Title
	$get_title_from_url = get_title_from_url($url);

	// Set our post data arguments
	////$post_data['post_title']   = $sanitized_values['_ot_bv_link_submit_link'];
	////unset( $sanitized_values['_ot_bv_link_submit_link'] );
	$post_data['post_title']   = $get_title_from_url;
	unset( $get_title_from_url );
	$post_data['post_content'] = $sanitized_values['_ot_bv_link_submit_reason'];
	unset( $sanitized_values['_ot_bv_link_submit_reason'] );

	// Create the new post
	$new_submission_id = wp_insert_post( $post_data, true );

	// If we hit a snag, update the user
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	/*
	 * Redirect back to the form page with a query variable with the new post ID.
	 * This will help double-submissions with browser refreshes
	 */
	wp_redirect( esc_url_raw( add_query_arg( 'post_submitted', $new_submission_id ) ) );
	exit;
}
add_action( 'cmb2_after_init', 'ot_handle_frontend_new_post_form_submission' );
