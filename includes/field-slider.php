<?php
/// sets the values
$range = '0,100';
$vars = explode('|', $settings['default']);
if(!empty($vars[1])){
	if($value == $settings['default']){
		$value = $vars[1];
	}
}else{
	if($value == $settings['default']){
		$value = 0;
	}
}
if(!empty($vars[0])){
	if(false === strpos($vars[0], ',')){
		$range = '0,'.$vars[0];
	}else{
		$range = $vars[0];
	}
}


?>
						<input autocomplete="off" data-init="wp_job_manager_shortwidget_init_slider" name="<?php echo $name; ?>" class="wp-job-manager-shortwidget-init-callback wp_job_manager_shortwidget_simple_slider" type="text" ref="<?php echo $groupid; ?>" id="<?php echo $id; ?>" value="<?php echo sanitize_text_field( $value ); ?>" data-slider-highlight="true" data-slider-range="<?php echo $range; ?>"/>
						 <span class="output"><?php echo sanitize_text_field( $value ); ?></span> <?php echo ( !empty($vars[2]) ? $vars[2] : null ); ?>