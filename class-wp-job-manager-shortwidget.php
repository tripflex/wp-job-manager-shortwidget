<?php
/**
 * WP Job Manager ShortWidget.
 *
 * @package   Wp_Job_Manager_Shortwidget
 * @author    Myles McNamara <myles@smyl.es>
 * @license   GPL-2.0+
 * @link      http://smyl.es
 * @copyright 2014 Myles McNamara
 */

/**
 * Plugin class.
 * @package Wp_Job_Manager_Shortwidget
 * @author  Myles McNamara <myles@smyl.es>
 */
class Wp_Job_Manager_Shortwidget {

	/**
	 * @var     string
	 */
	const VERSION = '1.0.0';
	/**
	 * @var      string
	 */
	protected $plugin_slug = 'wp-job-manager-shortwidget';
	/**
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * @var      array
	 */
	protected $element_instances = array();
	/**
	 * @var      array
	 */
	protected $element_css_once = array();
	/**
	 * @var      array
	 */
	protected $elements = array();
	/**
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );
		
		add_action('wp_footer', array( $this, 'footer_scripts' ) );

		// Detect element before rendering the page so that we can enque scripts and styles needed
		if(!is_admin()){
			add_action( 'wp', array( $this, 'detect_elements' ) );
		}

		if(is_admin()){
			add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
			add_action( 'admin_footer', array( $this, 'shortcode_modal_template' ) );
		}
// Add shortcodes
		// add_shortcode('job', array($this, 'render_element'));
		// add_shortcode('jobs', array($this, 'render_element'));
		// add_shortcode('job_summary', array($this, 'render_element'));
		// add_shortcode('job_field', array($this, 'render_element'));
		// add_shortcode('submit_job_form', array($this, 'render_element'));
		$this->elements = array_merge($this->elements, array(
			'shortcodes'			=>	array(
				'job' 			=> '1',
				'jobs' 			=> '1',
				'job_summary' 			=> '1',
				'job_field' 			=> '1',
				'submit_job_form' 			=> '1',
			)
		));

	}

	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 *
	 * @param	int	$blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )
			return;

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 *
	 * @return	array|false	The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here if needed
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here needed
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		// TODO: Add translations as need in /languages
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		
		if($screen->base == 'post'){
			wp_enqueue_script( $this->plugin_slug . '-shortcode-modal-script', self::get_url( 'assets/js/shortcode-modal.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-panel-script', self::get_url( 'assets/js/panel.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-panel-styles', self::get_url( 'assets/css/panel.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-slider-styles-simple-slider-css', self::get_url( 'assets/css/simple-slider.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-slider-script-simple-slider-min-js', self::get_url( 'assets/js/simple-slider.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-onoff-styles-toggles-css', self::get_url( 'assets/css/toggles.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-onoff-script-toggles-min-js', self::get_url( 'assets/js/toggles.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			$slug = array_search( $screen->id, $this->plugin_screen_hook_suffix );
			//$configfiles = glob( self::get_path( __FILE__ ) .'configs/'.$slug.'-*.php' );
			if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php')){
				include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php';
			}else{
				return;
			}

			if( !empty( $configfiles ) ) {
				// Always good to have.
				wp_enqueue_media();
				wp_enqueue_script('media-upload');

				foreach ($configfiles as $key=>$fieldfile) {
					include $fieldfile;
					if(!empty($group['scripts'])){
						foreach($group['scripts'] as $script){
							if( is_array( $script ) ){
								foreach($script as $remote=>$location){
									$infoot = false;
									if($location == 'footer'){
										$infoot = true;
									}
									if( false !== strpos($remote, '.')){
										wp_enqueue_script( $this->plugin_slug . '-' . strtok(basename($remote), '.'), $remote , array('jquery'), false, $infoot );
									}else{
										wp_enqueue_script( $remote, false , array(), false, $infoot );
									}
								}
							}else{
								if( false !== strpos($script, '.')){
									wp_enqueue_script( $this->plugin_slug . '-' . strtok($script, '.'), self::get_url( 'assets/js/'.$script , __FILE__ ) , array('jquery'), false, true );					
								}else{
									wp_enqueue_script( $script );
								}
							}
						}
					}
					if(!empty($group['styles'])){
						foreach($group['styles'] as $style){
							if( is_array( $style ) ){
								foreach($style as $remote){
									wp_enqueue_style( $this->plugin_slug . '-' . strtok(basename($remote), '.'), $remote );
								}
							}else{
								wp_enqueue_style( $this->plugin_slug . '-' . strtok($style, '.'), self::get_url( 'assets/css/'.$style , __FILE__ ) );
							}
						}
					}
				}
			}	
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', self::get_url( 'assets/css/panel.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', self::get_url( 'assets/js/panel.js', __FILE__ ), array(), self::VERSION );
		}

	}

	
	
	
	/**
	 * Process a field value
	 *
	 */
	public function process_value($type, $value){

		switch ($type){
			default:
				return $value;
				break;
			
		}

		return $value;	

	}

	
	/**
	 * Insert shortcode media button
	 *
	 *
	 */
	function shortcode_insert_button(){
		global $post;
		if(!empty($post)){
			echo "<a id=\"wp-job-manager-shortwidget-shortcodeinsert\" title=\"".__('WP Job Manager ShortWidget Shortcode Builder','wp-job-manager-shortwidget')."\" style=\"padding-left: 0.4em;\" class=\"button wp-job-manager-shortwidget-editor-button\" href=\"#inst\">\n";
			echo "	<img src=\"". self::get_url( __FILE__ ) . "assets/images/icon.png\" alt=\"".__("Insert Shortcode","wp-job-manager-shortwidget")."\" style=\"padding:0 2px 1px;\" /> ".__('WP Job Manager ShortWidget', 'wp-job-manager-shortwidget')."\n";
			echo "</a>\n";
		}
	}

	/**
	 * render shortcode config panel.
	 *
	 *
	 * @return    null
	 */
	function render_shortcode_panel($shortcode, $type = 1, $template = false){


		if(!empty($template)){
			echo "<script type=\"text/html\" id=\"wp-job-manager-shortwidget-".$shortcode."-config-tmpl\">\r\n";
		}
		echo "<input id=\"wp-job-manager-shortwidget-shortcodekey\" class=\"configexclude\" type=\"hidden\" value=\"".$shortcode."\">\r\n";
		echo "<input id=\"wp-job-manager-shortwidget-shortcodetype\" class=\"configexclude\" type=\"hidden\" value=\"".$type."\">\r\n";
		echo "<input id=\"wp-job-manager-shortwidget-default-content\" class=\"configexclude\" type=\"hidden\" value=\" ".__('Your content goes here','wp-job-manager-shortwidget')." \">\r\n";

		if(!empty($this->elements['posttypes'][$shortcode])){
			$posts = get_posts(array('post_type' => $shortcode));

			if(empty($posts)){
				echo 'No items available';
			}else{
				foreach($posts as $post){
					echo '<div class="posttype-item"><label><input type="radio" value="'.$post->ID.'" name="id"> '.$post->post_title.'</label></div>';
				}
			}
			if(!empty($template)){
				echo "</script>\r\n";
			}
			return;
		}
	
		if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$shortcode.'.php')){
			include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$shortcode.'.php';		
		}else{
			if(!empty($template)){
				echo "</script>\r\n";
			}
			return;
		}

		$groups = array();
		echo "<div class=\"wp-job-manager-shortwidget-shortcode-config-nav\">\r\n";
		echo "	<ul>\r\n";
		foreach ($configfiles as $key=>$fieldfile) {
			include $fieldfile;
			$groups[] = $group;
				echo "		<li class=\"" . ( $key === 0 ? "current" : "" ) . "\">\r\n";
				echo "			<a title=\"".$group['label']."\" href=\"#row".$group['master']."\"><strong>".$group['label']."</strong></a>\r\n";
				echo "		</li>\r\n";
		}
		echo "	</ul>\r\n";
		echo "</div>\r\n";

		echo "<div class=\"wp-job-manager-shortwidget-shortcode-config-content " . ( count($configfiles) > 1 ? "" : "full" ) . "\">\r\n";
			foreach($groups as $key=>$group){
				echo "<div class=\"group\" " . ( $key === 0 ? "" : "style=\"display:none;\"" ) . " id=\"row".$group['master']."\">\r\n";
				echo "<h3 class=\"wp-job-manager-shortwidget-config-header\">".$group['label']."</h3>\r\n";
				echo "<table class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				echo "	<tbody>\r\n";
					foreach($group['fields'] as $field=>$settings){
						//dump($settings);
						$id = 'field_'.$field;
						$groupid = $group['id'];
						$name = $field;
						$single = true;
						if(!empty($group['multiple'])){
							$name = $field.'[]';
						}
						$label = $settings['label'];
						$caption = $settings['caption'];
						$value = $settings['default'];
						echo "<tr valign=\"top\">\r\n";
							echo "<th scope=\"row\">\r\n";
								echo "<label for=\"".$id."\">".$label."</label>\r\n";
							echo "</th>\r\n";
							echo "<td>\r\n";
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo "<p class=\"description\">".$caption."</p>\r\n";
							}
							echo "</td>\r\n";
						echo "</tr>\r\n";
					}
				echo "	</tbody>\r\n";
				echo "</table>\r\n";

				if(!empty($group['multiple'])){
					echo "<div class=\"toolrow\"><button class=\"button wp-job-manager-shortwidget-add-group-row\" type=\"button\" data-rowtemplate=\"group-".$group['id']."-tmpl\">".__('Add Another','wp-job-manager-shortwidget')."</button></div>\r\n";
				}
				echo "</div>\r\n";
			}
		echo "</div>\r\n";

		if(!empty($template)){
			echo "</script>\r\n";
		}
		// go get the loop templates
		foreach($groups as $group){
			// Place html template for repeated fields.
			if(!empty($group['multiple'])){
				echo "<script type=\"text/html\" id=\"group-".$group['id']."-tmpl\">\r\n";
				echo '  <div class="button button-primary right wp-job-manager-shortwidget-removeRow" style="margin:5px 5px 0;">'.__('Remove','wp-job-manager-shortwidget').'</div>';
				echo "	<table class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				echo "		<tbody>\r\n";
					foreach($group['fields'] as $field=>$settings){
						//dump($settings);
						$id = 'field_{{id}}';
						$groupid = $group['id'];
						$name = $field.'[__count__]';
						$single = true;
						$label = $settings['label'];
						$caption = $settings['caption'];
						$value = $settings['default'];
						echo "<tr valign=\"top\">\r\n";
							echo "<th scope=\"row\">\r\n";
								echo "<label for=\"".$id."\">".$label."</label>\r\n";
							echo "</th>\r\n";
							echo "<td>\r\n";
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo "<p class=\"description\">".$caption."</p>\r\n";
							}
							echo "</td>\r\n";
						echo "</tr>\r\n";

					}
				echo "		</tbody>\r\n";
				echo "	</table>\r\n";
				echo "</script>";
			}
		}
	}

	/**
	 * Insert shortcode modal template
	 *
	 *
	 */
	function shortcode_modal_template(){
		$screen = get_current_screen();

		if($screen->base != 'post'){return;}

		echo "<script type=\"text/html\" id=\"wp-job-manager-shortwidget-shortcode-panel-tmpl\">\r\n";
		echo "	<div tabindex=\"0\" id=\"wp-job-manager-shortwidget-shortcode-panel\" class=\"hidden\">\r\n";
		echo "		<div class=\"media-modal-backdrop\"></div>\r\n";
		echo "		<div class=\"wp-job-manager-shortwidget-modal-modal\">\r\n";
		echo "			<div class=\"wp-job-manager-shortwidget-modal-content\">\r\n";
		echo "				<div class=\"wp-job-manager-shortwidget-modal-header\">\r\n";
		echo "					<a title=\"Close\" href=\"#\" class=\"media-modal-close\">\r\n";
		echo "						<span class=\"media-modal-icon\"></span>\r\n";
		echo "					</a>\r\n";
		echo "					<h2 style=\"background: url(".self::get_url( '/assets/images/icon.png', __FILE__ ) . ") no-repeat scroll 0px 2px transparent; padding-left: 20px;\">".__('WP Job Manager ShortWidget','wp-job-manager-shortwidget')." <small>".__("Shortcode Builder","wp-job-manager-shortwidget")."</small></h2>\r\n";
		echo "				</div>\r\n";
		echo "				<div class=\"wp-job-manager-shortwidget-modal-body\">\r\n";
		echo "					<span id=\"wp-job-manager-shortwidget-categories\">\r\n";
		echo "						Shortcode: <span id=\"wp-job-manager-shortwidget-elements\"><select class=\"wp-job-manager-shortwidget-elements-selector\" id=\"wp-job-manager-shortwidget-elements-selector\"><option value=\"\">Select Shortcode</option><option value=\"job\">".__('Single Job','wp-job-manager-shortwidget')."</option>
<option value=\"jobs\">".__('Job List','wp-job-manager-shortwidget')."</option>
<option value=\"job_summary\">".__('Job Summary','wp-job-manager-shortwidget')."</option>
<option value=\"job_field\">".__('Job Field','wp-job-manager-shortwidget')."</option>
<option value=\"submit_job_form\">".__('Submit Job Form','wp-job-manager-shortwidget')."</option>
</select>
</span>
\r\n";
		echo "					</span>\r\n";
		echo "					<div id=\"wp-job-manager-shortwidget-shortcode-config\" class=\"wp-job-manager-shortwidget-shortcode-config\">\r\n";
		echo "					</div>\r\n";
		echo "				</div>\r\n";
		echo "				<div class=\"wp-job-manager-shortwidget-modal-footer\">\r\n";
		echo "					<button class=\"button button-primary button-large\" id=\"wp-job-manager-shortwidget-insert-shortcode\">".__("Insert Shortcode","wp-job-manager-shortwidget")."</button>\r\n";
		echo "				</div>\r\n";
		echo "			</div>\r\n";
		echo "		</div>\r\n";
		echo "	</div>\r\n";
		echo "</script>\r\n";

		foreach($this->elements['shortcodes'] as $shortcode=>$type){
			$this->render_shortcode_panel($shortcode, $type, true);
		}
		
	}

	/**
	 * Gets a list of shorcodes used within the content provided
	 *
	 * @return 	array
	 */
	function get_regex(){

	// this makes it easier to cycle through and get the used codes for inclusion
	$validcodes = join( '|', array_map('preg_quote', array_keys($this->elements['shortcodes'])) );
	return
			  '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($validcodes)"                    // 2: selected codes only
			. '\\b'                              // Word boundary
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			.     '(?:'
			.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
			.     ')*?'
			. ')'
			. '(?:'
			.     '(\\/)'                        // 4: Self closing tag ...
			.     '\\]'                          // ... and closing bracket
			. '|'
			.     '\\]'                          // Closing bracket
			.     '(?:'
			.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			.             '[^\\[]*+'             // Not an opening bracket
			.             '(?:'
			.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			.                 '[^\\[]*+'         // Not an opening bracket
			.             ')*+'
			.         ')'
			.         '\\[\\/\\2\\]'             // Closing shortcode tag
			.     ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

	}

	function get_used_shortcodes($content, $return = array(), $internal = true, $preview = false){

		$regex = self::get_regex();

		preg_match_all('/' . $regex . '/s', $content, $found);

		foreach($found[5] as $innerContent){
			if(!empty($innerContent)){
			   $new = self::get_used_shortcodes($innerContent, $found, $internal);
				if(!empty($new)){
					foreach($new as $key=>$val){
						$found[$key] = array_merge($found[$key], $val);
					}
				}
			}
		}

		return $found;
	}


	/**
	 * setup meta boxes.
	 *
	 *
	 * @return    null
	 */
	public function get_post_meta($id, $key = null, $single = false){
		
		if(!empty($key)){

			//$configfiles = glob(self::get_path( __FILE__ ) .'configs/*.php');
			if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-wp_job_manager_shortwidget.php')){
				include self::get_path( __FILE__ ) .'configs/fieldgroups-wp_job_manager_shortwidget.php';		
			}else{
				return;
			}

			$field_type = 'text';
			foreach( $configfiles as $config=>$file ){
				include $file;
				if(isset($group['fields'][$key]['type'])){
					$field_type = $group['fields'][$key]['type'];
					break;
				}
			}
			$key = 'wp_job_manager_shortwidget_' . $key;
		}
		if( false === $single){
			$metas = get_post_meta( $id, $key );
			foreach ($metas as $key => &$value) {
				$value = $this->process_value( $field_type, $value );
			}
			return $metas;
		}
		return $this->process_value( $field_type, get_post_meta( $id, $key, $single ) );

	}


	/**
	 * save metabox data
	 *
	 *
	 */
	function save_post_metaboxes($pid, $post){

		if(!isset($_POST['wp_job_manager_shortwidget_metabox']) || !isset($_POST['wp_job_manager_shortwidget_metabox_prefix'])){return;}


		if(!wp_verify_nonce($_POST['wp_job_manager_shortwidget_metabox'], plugin_basename(__FILE__))){
			return $post->ID;
		}
		if(!current_user_can( 'edit_post', $post->ID)){
			return $post->ID;
		}
		if($post->post_type == 'revision' ){return;}
		
		foreach( $_POST['wp_job_manager_shortwidget_metabox_prefix'] as $prefix ){
			if(!isset($_POST[$prefix])){continue;}

			if($_POST['wp_job_manager_shortwidget_storage'] == 'single'){
				foreach($_POST[$prefix] as $field=>$data){
					update_post_meta($post->ID, $field, $data);
				}
			}else{
				delete_post_meta($post->ID, $prefix);
				add_post_meta($post->ID, $prefix, $_POST[$prefix]);
			}
			//foreach($_POST['wp_job_manager_shortwidget'] as $field=>$data){
			/*
			*/
			//}
		}
	}	
	/**
	 * create and register an instance ID
	 *
	 */
	public function element_instance_id($id, $process){

		$this->element_instances[$id][$process][] = true;
		$count = count($this->element_instances[$id][$process]);
		if($count > 1){
			return $id.($count-1);
		}
		return $id;
	}

	/**
	 * Render the element
	 *
	 */
	public function render_element($atts, $content, $slug, $head = false) {
		
		$raw_atts = $atts;
		

		if(!empty($head)){
			$instanceID = $this->element_instance_id('wp_job_manager_shortwidget'.$slug, 'header');
		}else{
			$instanceID = $this->element_instance_id('wp_job_manager_shortwidget'.$slug, 'footer');
		}

		//$configfiles = glob(self::get_path( __FILE__ ) .'configs/'.$slug.'-*.php');
		if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php')){
			include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php';		
		
			$defaults = array();
			foreach($configfiles as $file){

				include $file;
				foreach($group['fields'] as $variable=>$conf){
					if(!empty($group['multiple'])){
						$value = array($this->process_value($conf['type'],$conf['default']));
					}else{
						$value = $this->process_value($conf['type'],$conf['default']);
					}
					if(!empty($group['multiple'])){
						if(isset($atts[$variable.'_1'])){
							$index = 1;
							$value=array();
							while(isset($atts[$variable.'_'.$index])){
								$value[] = $this->process_value($conf['type'],$atts[$variable.'_'.$index]);
								$index++;
							}
						}elseif (isset($atts[$variable])) {
							if(is_array($atts[$variable])){
								foreach($atts[$variable] as &$varval){
									$varval = $this->process_value($conf['type'],$varval);
								}
								$value = $atts[$variable];
							}else{
								$value[] = $this->process_value($conf['type'],$atts[$variable]);
							}
						}
					}else{
						if(isset($atts[$variable])){
							$value = $this->process_value($conf['type'],$atts[$variable]);
						}
					}
					
					if(!empty($group['multiple']) && !empty($value)){
						foreach($value as $key=>$val){
							//if(is_array($val)){
								$groups[$group['master']][$key][$variable] = $val;
							//}elseif(strlen($val) > 0){
							//	$groups[$group['master']][$key][$variable] = $val;
							//}
						}
					}
					$defaults[$variable] = $value;
					/*if(is_array($value)){
						foreach($value as $varkey=>&$varval){
							if(is_array($val)){
								if(!empty($val)){
									unset($value[$varkey]);
								}
							}elseif(strlen($varval) === 0){
								unset($value[$varkey]);
							}
						}
						if(!empty($value)){
							$defaults[$variable] = implode(', ', $value);
						}
					}else{
						if(strlen($value) > 0){
							$defaults[$variable] = $value;
						}
					}*/
				}
			}
			//dump($atts,0);
			//dump($defaults,0);
			$atts = $defaults;
		}
		// pull in the assets
		$assets = array();
		if(file_exists(self::get_path( __FILE__ ) . 'assets/assets-'.$slug.'.php')){
			include self::get_path( __FILE__ ) . 'assets/assets-'.$slug.'.php';
		}

		ob_start();
		if(file_exists(self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.php')){
			include self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.php';
		}else if( file_exists(self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.html')){
			include self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.html';
		}
		$out = ob_get_clean();


		if(!empty($head)){

			// process headers - CSS
			if(file_exists(self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.php')){
				ob_start();
				include self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.php';
				$this->element_header_styles[] = ob_get_clean();
				add_action('wp_head', array( $this, 'header_styles' ) );
			}else if( file_exists(self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.css')){
				wp_enqueue_style( $this->plugin_slug . '-'.$slug.'-styles', self::get_url( 'assets/css/styles-'.$slug.'.css', __FILE__ ), array(), self::VERSION );
			}
			// process headers - JS
			if(file_exists(self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.php')){
				ob_start();
				include self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.php';				
				$this->element_footer_scripts[] = ob_get_clean();
			}else if( file_exists(self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.js')){
				wp_enqueue_script( $this->plugin_slug . '-'.$slug.'-script', self::get_url( 'assets/js/scripts-'.$slug.'.js', __FILE__ ), array( 'jquery' ), self::VERSION , true );
			}
			// get clean do shortcode for header checking
			ob_start();
			do_shortcode( $out );
			ob_get_clean();			
			return;
		}
		
		// CHECK FOR EMBEDED ELEMENTS
		/*foreach($elements as $subelement){
			if(empty($subelement['state']) || $subelement['shortcode'] == $element['_shortcode']){continue;}
			if(false !== strpos($out, '{{:'.$subelement['shortcode'].':}}')){
				$out = str_replace('{{:'.$subelement['shortcode'].':}}', caldera_doShortcode(array(), $out, $subelement['shortcode']), $out);
			}
		}*/


		/*if(!empty($element['_removelinebreaks'])){
			add_filter( 'the_content', 'wpautop' );
		}*/

		return do_shortcode($out);
	}

	/**
	 * Detect elements used on the page to allow us to enqueue needed styles and scripts
	 *
	 */
	public function detect_elements() {
		global $wp_query;

		
	
		// find used shortcodes within posts
		foreach ($wp_query->posts as $key => &$post) {
			$shortcodes = self::get_used_shortcodes($post->post_content);
			if(!empty($shortcodes[2])){
				foreach($shortcodes[2] as $foundkey=>$shortcode){
					$atts = array();
					if(!empty($shortcodes[3][$foundkey])){
						$atts = shortcode_parse_atts($shortcodes[3][$foundkey]);
					}
					
					// process header portion
					$this->render_element($atts, $post->post_content, $shortcode, true);
				}
			}
		}


	}

	/**
	 * Render any header styles
	 *
	 */
	public function header_styles() {
		if(!empty($this->element_header_styles)){
			echo "<style type=\"text/css\">\r\n";
			foreach($this->element_header_styles as $styles){
				echo $styles."\r\n";
			}			
			echo "</style>\r\n";
		}
	}
	
	/**
	 * Render any footer scripts
	 *
	 */
	public function footer_scripts() {

		if(!empty($this->element_footer_scripts)){
			echo "<script type=\"text/javascript\">\r\n";
				foreach($this->element_footer_scripts as $script){
					echo $script."\r\n";
				}
			echo "</script>\r\n";
		}
	}

	

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
	
}
