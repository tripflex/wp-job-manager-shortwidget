<?php
/**
 * get_the_custom_job_field function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
if(!function_exists('get_the_custom_job_field')){
	function get_the_custom_job_field( $field, $post = null ) {
		$post = get_post( $post );
		if ( $post->post_type !== 'job_listing' || !$field )
			return;
		$custom_field = $post->$field;
		
		return apply_filters( 'the_' . $field, $custom_field, $post );
	}
}
?>