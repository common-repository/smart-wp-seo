<?php

/**
 * Calls the class on the post edit screen.
 */
function call_swsPostMeta() {
    new swsPostMeta();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_swsPostMeta' );
    add_action( 'load-post-new.php', 'call_swsPostMeta' );
}

/** 
 * The Class.
 */
class swsPostMeta {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('post', 'page');     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'some_meta_box_name'
			,__( 'Smart Wordpress SEO Setting', 'sws_textdomain' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['sws_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['sws_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'sws_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$sws_post_title = sanitize_text_field( $_POST['sws_post_title'] );
                $sws_post_description = sanitize_text_field( $_POST['sws_post_description'] );
                $sws_post_keywords = sanitize_text_field( $_POST['sws_post_keywords'] );

		// Update the meta field.
		update_post_meta( $post_id, 'sws_post_title', $sws_post_title );
                update_post_meta( $post_id, 'sws_post_description', $sws_post_description );
                update_post_meta( $post_id, 'sws_post_keywords', $sws_post_keywords );
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	 echo '<table class="sws-table" style="width:100%">';
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'sws_inner_custom_box', 'sws_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$sws_post_title = get_post_meta( $post->ID, 'sws_post_title', true );
                $sws_post_description = get_post_meta( $post->ID, 'sws_post_description', true );
                $sws_post_keywords = get_post_meta( $post->ID, 'sws_post_keywords', true );
               
		// Display the form, using the current value.
		echo sws_input_text_field('sws_post_title', 'SEO Title',NULL,$sws_post_title);
                
                echo sws_input_textarea_field('sws_post_description', 'SEO Description',NULL,$sws_post_description);
                
                echo sws_input_text_field('sws_post_keywords', 'SEO Keywords',NULL,$sws_post_keywords);
                echo '</table>';
	}
}