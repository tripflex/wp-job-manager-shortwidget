<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Job Field
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Job Field','wp-job-manager-shortwidget'),
	'id' => '61011367',
	'master' => 'field',
	'fields' => array(
		'field'	=>	array(
			'label'		=> 	__('Field','wp-job-manager-shortwidget'),
			'caption'	=>	__('Insert the meta key here for the field you would like to output.','wp-job-manager-shortwidget'),
			'type'		=>	'textfield',
			'default'	=> 	'',
		),
		'job_id'	=>	array(
			'label'		=> 	__('Job ID','wp-job-manager-shortwidget'),
			'caption'	=>	__('If you want to display for a specific job select from this list, otherwise do not select anything from this list to use the current job listing.','wp-job-manager-shortwidget'),
			'type'		=>	'posttypeselector',
			'default'	=> 	'job_listing',
		),
	),
	'multiple'	=> false,
);

