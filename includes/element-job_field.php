<?php
if( $atts['field'] ) {
	
	if( ! $atts['job_id'] ) {
		
	global $post;
		
		if ( $post->ID ) {
			
			echo get_the_custom_job_field($atts['field'], $post->ID );
			
		}
		
	} else {
		
		echo get_the_custom_job_field($atts['field'], $atts['job_id'] );
		
	}
	
}

			
?>