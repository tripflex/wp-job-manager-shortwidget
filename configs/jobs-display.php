<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Display
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Display','wp-job-manager-shortwidget'),
	'id' => '1232477',
	'master' => 'per_page',
	'fields' => array(
		'per_page'	=>	array(
			'label'		=> 	__('Per Page<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to the \'per page\' option in settings. This controls how many jobs get listed per page.','wp-job-manager-shortwidget'),
			'type'		=>	'slider',
			'default'	=> 	'0,100|0',
			'inline'	=> 	true,
		),
	),
	'styles'	=> array(
		'simple-slider.css',
	),
	'scripts'	=> array(
		'simple-slider.min.js',
	),
	'multiple'	=> false,
);

