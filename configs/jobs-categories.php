<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Categories
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Categories','wp-job-manager-shortwidget'),
	'id' => '1441010111',
	'master' => 'show_categories',
	'fields' => array(
		'show_categories'	=>	array(
			'label'		=> 	__('Show Categories<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to true when categories are enabled. If enabled, the filters will also show a dropdown letting the user choose a job category to filter by.','wp-job-manager-shortwidget'),
			'type'		=>	'onoff',
			'default'	=> 	'true||True,false||False',
			'inline'	=> 	true,
		),
		'categories'	=>	array(
			'label'		=> 	__('Categories<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Comma separate slugs to limit the jobs to certain categories. This option overrides \'show_categories\' if both are set.','wp-job-manager-shortwidget'),
			'type'		=>	'textfield',
			'default'	=> 	'',
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

