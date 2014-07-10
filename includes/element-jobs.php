<?php
$sc_values = '';

if ( ! empty( $atts ) ) {
	foreach ( $atts as $slug => $value ) {
		if ( $value ) {

			if ( $slug == 'per_page' && $value == '0' )
				continue;

			$sc_values .= ' ' . $slug . '="' . $value . '"';
		}
	}
}
$short_code = "[jobs " . $sc_values . "]";

echo $short_code;

?>