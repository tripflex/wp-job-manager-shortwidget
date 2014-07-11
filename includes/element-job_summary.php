<?php
if ( ! $atts[ 'id' ] ) {
	global $post;
	if ( $post->ID )
		$job_id = $post->ID;
} else {
	$job_id = $atts[ 'id' ];
}

if ( $job_id ) echo '[job_summary id="' . $job_id . '"]';
?>