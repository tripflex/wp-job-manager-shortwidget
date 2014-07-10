<?php
/**
*
* fieldconfig for wp-job-manager-shortwidget/Ordering
*
* @package Wp_Job_Manager_Shortwidget
* @author Myles McNamara myles@smyl.es
* @license GPL-2.0+
* @link http://smyl.es
* @copyright 2014 Myles McNamara
*/


$group = array(
	'label' => __('Ordering','wp-job-manager-shortwidget'),
	'id' => '15744109',
	'master' => 'order',
	'fields' => array(
		'order'	=>	array(
			'label'		=> 	__('Order<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to \'desc\'. Can be set to \'asc\' or \'desc\' to choose the sorting direction.','wp-job-manager-shortwidget'),
			'type'		=>	'onoff',
			'default'	=> 	'desc||Descending,asc||Ascending',
			'inline'	=> 	true,
		),
		'orderby'	=>	array(
			'label'		=> 	__('Order By<br><small>Optional</small>','wp-job-manager-shortwidget'),
			'caption'	=>	__('Defaults to \'date\'. Supports title, ID, name, date, modified, parent, rand.','wp-job-manager-shortwidget'),
			'type'		=>	'dropdown',
			'default'	=> 	'date||Date,title||Title,ID||ID,name||Name,modified||Modified,parent||Parent,rand||Random,job_expires||Expiration',
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

