<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Configuration
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Configuration','wp-job-manager-shortwidget'),
	'id' => '13866113',
	'master' => 'id',
	'fields' => array(
		'id'	=>	array(
			'label'		=> 	__('Job<br/><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Select the job from the list you would like to display.','wp-job-manager-shortwidget'),
			'type'		=>	'posttypeselector',
			'default'	=> 	'job_listing',
		),
	),
	'multiple'	=> false,
);

