<?php

class Widget_job extends WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		//add_action( 'init', array( $this, 'widget_textdomain' ) );

		parent::__construct(
			'job-id',
			__( 'Single Job', 'wp-job-manager-shortwidget' ),
			array(
				'description'	=>	__( 'Output a single Job.', 'wp-job-manager-shortwidget' )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles-widgets.php', array( $this, 'register_admin_styles_scripts' ) );

		// register front
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		if( empty($instance) ){
			return;
		}

		if(isset($instance['__cur_tab__'])){
			unset($instance['__cur_tab__']);
		}
		extract( $args, EXTR_SKIP );

		
		
		//dump($instance);
		// TODO: Here is where you manipulate your widget's values based on their input fields
		$element = Wp_Job_Manager_Shortwidget::get_instance();
		echo $element->render_element($instance, '', 'job');		

		

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		return $new_instance;


	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

    	// TODO:	Define default values for your variables
		$instance = wp_parse_args(
			(array) $instance
		);
		
		// Display the admin form
		//$configfiles = glob( self::get_path( dirname( __FILE__ ) ) .'configs/job-*.php' );
		if(file_exists(self::get_path( dirname( __FILE__ ) ) .'configs/fieldgroups-job.php')){
			include self::get_path( dirname( __FILE__ ) ) .'configs/fieldgroups-job.php';		
		}else{
			return;
		}

		echo "<input type=\"hidden\" name=\"wp_job_manager_shortwidget-widget\">\r\n";
		$groups = array();
		$setsize = 'full';

		
		echo "<div style=\"position: relative;\">\r\n";

		foreach ($configfiles as $key=>$fieldfile) {
			include $fieldfile;
			$group['id'] = uniqid('wp-job-manager-shortwidget');
			$groups[] = $group;
		}
		
			
			echo "<div class=\"wp-job-manager-shortwidget-widget-config-content " . $setsize . "\">\r\n";
			foreach ($groups as $key=>$group) {
				echo "<div id=\"row".self::get_field_id('__row'.$group['id'])."\" class=\"wp-job-manager-shortwidget-groupbox group\" " . ( !empty($instance['__cur_tab__']) ? ($instance['__cur_tab__'] == $key ? "" : "style=\"display:none;\"") : ($key === 0 ? "" : "style=\"display:none;\"" )) . ">\r\n";
				if(count($groups) > 1 || empty($setsize)){
					echo "<h3>".$group['label']."</h3>";
				}
				$this->group($group, $instance);
				echo "</div>\r\n";
			}
			
			echo '</div>';
		echo "</div>\r\n";
		echo "<hr class=\"widget-footer\">\r\n";
	} // end form

	/**
	 * Generates a group of fields for the widget.
	 *
	 */
	// build instance
	public function group($group, $instance){
		$depth = 1;


		foreach($group['fields'] as $field=>$settings){			
			if(!empty($instance[$field]) && !empty($group['multiple'])){
				if(count($instance[$field]) > $depth){
					$depth = count($instance[$field]);
				}
			}
		}

		for( $i=0; $i<$depth;$i++ ){
				if($i > 0){
					echo '  <div class="button button-primary right wp-job-manager-shortwidget-removeRow" style="margin:5px 5px 0;">'.__('Remove', 'wp-job-manager-shortwidget').'</div>';
				}			
			echo "<div class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				foreach($group['fields'] as $field=>$settings){
					//dump($settings);
					$id = self::get_field_id('field_'.$field).'_'.$i;
					$groupid = $group['id'];
					$name = self::get_field_name($field);
					$single = true;
					$value = $settings['default'];
					if(!empty($group['multiple'])){
						$name = self::get_field_name($field).'['.$i.']';
						if(isset($instance[$field][$i])){
							$value = $instance[$field][$i];
						}
					}else{
						if(isset($instance[$field])){
							$value = $instance[$field];
						}
					}
					$label = (!empty($settings['caption']) ? $settings['caption'] : $settings['label']);
					//$caption = $settings['caption'];
									
					echo '<div class="wp-job-manager-shortwidget-field-row"><label class="wp-job-manager-shortwidget_widget_label" for="'.$id.'">'.$label.'</label>';
					include self::get_path( dirname( __FILE__ ) ) . 'includes/field-'.$settings['type'].'.php';
					echo '</div>';
				}
			echo "</div>\r\n";
		}
		if(!empty($group['multiple'])){
			echo "<div><button class=\"button wp-job-manager-shortwidget-add-group-row\" type=\"button\" data-field=\"".self::get_field_id('ref')."\" data-rowtemplate=\"group-".$group['id']."-tmpl\">".__('Add Another','wp-job-manager-shortwidget')."</button></div>\r\n";
		}
		
		// Place html template for repeated fields.
		if(!empty($group['multiple'])){
			echo "<script type=\"text/html\" id=\"group-".$group['id']."-tmpl\">\r\n";
			echo '  <div class="button button-primary right wp-job-manager-shortwidget-removeRow" style="margin:5px 5px 0;">'.__('Remove','wp-job-manager-shortwidget').'</div>';
			echo "	<div class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				foreach($group['fields'] as $field=>$settings){
					//dump($settings);
					$id = self::get_field_id('field_{{id}}_'.$field);
					$groupid = $group['id'];
					$name = self::get_field_name($field);
					$single = true;
					if(!empty($group['multiple'])){
						$name = self::get_field_name($field).'[__count__]';
					}
					$label = $settings['label'];
					$caption = $settings['caption'];
					$value = $settings['default'];
					echo '<div class="wp-job-manager-shortwidget-field-row"><label class="wp-job-manager-shortwidget_widget_label" for="'.$id.'">'.$label.'</label>';
					include self::get_path( dirname( __FILE__ ) ) . 'includes/field-'.$settings['type'].'.php';
					echo '</div>';
				}
			echo "	</div>\r\n";
			echo "</script>";
		}
	}

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( 'wp-job-manager-shortwidget', false, self::get_path( dirname( __FILE__ ) ) . '/lang/' );

	} // end widget_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles_scripts() {

		// Always good to have.
		wp_enqueue_media();
		wp_enqueue_script('media-upload');

		//$configfiles = glob( self::get_path( dirname( __FILE__ ) ) .'configs/job-*.php' );
		if(file_exists(self::get_path( dirname( __FILE__ ) ) .'configs/fieldgroups-job.php')){
			include self::get_path( dirname( __FILE__ ) ) .'configs/fieldgroups-job.php';		
		}else{
			return;
		}
		foreach ($configfiles as $key=>$fieldfile) {
			include $fieldfile;
			if(!empty($group['scripts'])){
				foreach($group['scripts'] as $script){
					wp_enqueue_script( 'wp-job-manager-shortwidget-'.strtok($script, '.'), self::get_url( 'assets/js/'.$script , dirname(__FILE__) ) , array('jquery') );					
				}
			}
			if(!empty($group['styles'])){
				foreach($group['styles'] as $style){
					wp_enqueue_style( 'wp-job-manager-shortwidget-'.strtok($style, '.'), self::get_url( 'assets/css/'.$style , dirname(__FILE__) ) );
				}
			}
		}

		wp_enqueue_style( 'wp-job-manager-shortwidget-panel-styles', self::get_url( 'assets/css/panel.css', dirname(__FILE__) ) );
		wp_enqueue_script( 'wp-job-manager-shortwidget-panel-script', self::get_url( 'assets/js/panel.js', dirname(__FILE__) ), array( 'jquery' ) );


	} // end register_admin_styles
	
	/**
	* Registers and enqueues widget-specific styles.
	*/
	public function register_widget_styles() {
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			$widget_settings = get_option('widget_'.$this->id_base);
			$sidebars = get_option('sidebars_widgets');
			foreach ($sidebars as $sidebarid => $sidebar) {
				if(is_active_sidebar($sidebarid) && $sidebarid != 'wp_inactive_widgets' && false === strpos($sidebarid, 'orphaned_widgets')){
					foreach($widget_settings as $key=>$settings){
						if(in_array($this->id_base.'-'.$key,$sidebar)){
							if(empty($element)){
								$element = Wp_Job_Manager_Shortwidget::get_instance();
							}
							echo $element->render_element($settings, '', 'job', true);

						}
					}
				}
			}
			// render headers
			//
			//dump($widget_settings,0);
			
			//dump($sidebars,0);
			//wp_enqueue_style( 'widget-name-widget-styles', self::get_url( 'widget-name/css/widget.css' ) );
		}

	} // end register_widget_styles

	

	/***
	 * Get the current URL
	 *
	 */
	static function get_url($src = null, $path = null) {
		if(!empty($path)){
			return plugins_url( $src, $path);
		}
		return trailingslashit( plugins_url( $path , __FILE__ ) );
	}

	/***
	 * Get the current URL
	 *
	 */
	static function get_path($src = null) {
		return plugin_dir_path( $src );

	}
	
} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Widget_job");' ) );
