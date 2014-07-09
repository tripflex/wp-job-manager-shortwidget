<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Filtering
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Filtering','wp-job-manager-shortwidget'),
	'id' => '98126514',
	'master' => 'show_filters',
	'fields' => array(
		'show_filters'	=>	array(
			'label'		=> 	__('Show Filters<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to true. Shows filters above the job list letting the user narrow the list by keyword, location, and job type. Once a filter is chosen, active filters are listed above the jobs, as is an \'RSS\' link for the current search.','wp-job-manager-shortwidget'),
			'type'		=>	'onoff',
			'default'	=> 	'true||True,false||False',
			'inline'	=> 	true,
		),
	),
	'styles'	=> array(
		'toggles.css',
	),
	'scripts'	=> array(
		'toggles.min.js',
	),
	'multiple'	=> false,
);

