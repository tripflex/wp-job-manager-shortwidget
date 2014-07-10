<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Pagination
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Pagination','wp-job-manager-shortwidget'),
	'id' => '219299',
	'master' => 'show_pagination',
	'fields' => array(
		'show_pagination'	=>	array(
			'label'		=> 	__('Show Pagination<br/><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to true.  Shows standard WordPress pagination instead of load more link.','wp-job-manager-shortwidget'),
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

