<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.sherinshelvan.com
 * @since      1.0.0
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/admin
 * @author     Sherin Shelvan <sherinshelvan@gmail.coom>
 */


class Easy_Shortcode_Generator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public $messages;
	private $table_name;
	private $wpdb;
	public function __construct( $args ) {
		global $table_prefix, $wpdb;
		$this->plugin_name = $args['name'];
		$this->version     = $args['version'];
		$this->messages    = array();
		$this->table_name  = $table_prefix . "easy_shortcode";
		$this->wpdb        = $wpdb;
		//Pagination count update
		add_filter('set-screen-option', array( $this, 'esg_set_option' ), 10, 3);
		//Plugin action links
		add_filter( 'plugin_action_links', array( $this, 'esg_add_settings_link' ), 10, 5 );
	}
	/**
	* Add a settings link to your WordPress plugin on the plugin listing page.
	*/
	public function esg_add_settings_link( $links, $plugin_file ){
		if($plugin_file == ESG_ROOT_FILE){
		 	$links['settings'] = sprintf( '<a href="%s">Settings</a>', 'admin.php?page=easy-shortcode' );
		 	$links['configuration'] = sprintf( '<a href="%s">Configuration</a>', 'admin.php?page=easy-shortcode-configuration' );
		}
		return $links;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Shortcode_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Shortcode_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/easy-shortcode-generator-admin.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Shortcode_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Shortcode_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/easy-shortcode-generator-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_objects',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_plugin_admin_menu() { 
		global $_wp_last_object_menu;
		$_wp_last_object_menu++;
		// Add a new menu item in the WP backend
		$menu_title = __( 'Easy Shortcode', 'easy-shortcode' );

		// Add 'All Lists' sub page
		$submenu_1_title = __( 'All Lists', 'easy-shortcode' );
		$page_title = sprintf( '%s', $menu_title);

		$hook = add_menu_page( 
			$page_title, // page title
			$menu_title, // menu title
			'easy_shortcode', // capatibility
			'easy-shortcode', // menu slug
			array( $this, 'page_list' ), // callable function
			'dashicons-editor-code', // icon url
			$_wp_last_object_menu // position
		);
		
		// Give first sub level menu link a different label than the top level menu link 
		// by calling the add_submenu_page function the first time with the parent_slug 
		// and menu_slug as same values
		$hook = add_submenu_page( 
			'easy-shortcode', // parent slug
			sprintf( '%s', $menu_title ), // page title
			$submenu_1_title, // menu title
			'easy_shortcode', // capatibility
			'easy-shortcode'
		);
		//creating screen option
		add_action( "load-$hook", array( $this, 'esg_add_option') );
		//create help tab
		add_action("load-$hook", array( $this, 'esg_help_tab') );

		// Add 'Add New' sub page
		$text = 'Add New';
		$submenu_2_title = _x( $text, 'post' );

		add_submenu_page(
			'easy-shortcode', // parent slug
			sprintf( '%s: %s', $menu_title, $submenu_2_title ), // page title
			$submenu_2_title, // menu title
			'easy_shortcode', // capatibility
			'easy-shortcode-edit',
			array( $this, 'page_edit' )

		);

		add_submenu_page(
			'', // parent slug
			sprintf( '%s: %s', $menu_title, "Configuration" ), // page title
			$submenu_2_title, // menu title
			'manage_options', // capatibility
			'easy-shortcode-configuration',
			array( $this, 'plugin_configuration' )

		);

		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'esg_add_settings_link') );

		// Menu access Permission
		global $wp_roles;
		$access_roles = array('administrator');		
		foreach ($access_roles as $key => $role) {
			$wp_roles->add_cap( $role, 'easy_shortcode' ); 
		}
		
	}

	// Add esg_help_tab if current screen is My Admin Page
	function esg_help_tab(){
		$screen = get_current_screen();
		$help = array(
      'id'	=> 'overview',
      'title'	=> __('Overview'),
      'content'	=> '<p>' . __( 'This screen provides access to all shortcode generated using Easy shortcode Plugin. On this screen, you can manage an unlimited number of shortcodes. You can create new shortcodes, edit the existing shortcodes, and delete unwanted ones. Each shortcode has a unique ID and has a structure like [esg ..]. To insert a shortcode into a post, page or a text widget, just insert the shortcode into the target area.' ) . '</p>',
    	) ;
		$screen->add_help_tab( $help );
		$help = array(
      'id'	=> 'screen_options',
      'title'	=> __('Screen Options'),
      'content'	=> '<div>' . __( '<p>You can customize the display of this screen’s contents in a number of ways:</p>
	<ul>
		<li>You can hide/display columns based on your needs and decide how many posts to list per screen using the Screen Options tab.</li>
		<li>You can filter the list of posts by post status using the text links above the posts list to only show posts with that status. The default view is to show all posts.</li>
		<li>You can view posts in a simple title list or with an excerpt using the Screen Options tab.</li>
		<li>You can refine the list to show only posts in a specific category or from a specific month by using the dropdown menus above the posts list. Click the Filter button after making your selection. You also can refine the list by clicking on the post author, category or tag in the posts list.</li>
	</ul>' ) . '</div>',
    	) ;
		$screen->add_help_tab( $help );
		$help = array(
      'id'	=> 'available_actions',
      'title'	=> __('Available Actions'),
      'content'	=> '<div>' . __( '<p>Hovering over a row in the Shortcode list will display available action links that allow you to manage your shortcode. The following actions can be performed:</p>
				<ul>
					<li><strong>Edit</strong> Edit link will take you to the edit screen for that shortcode. Here you can make any changes needed to the shortcode and save it.</li>
					<li><strong>Delete</strong>  Delete removes your shortcode permanently from the list.</li>
				</ul>' ) . '</div>',
    	) ;
		$screen->add_help_tab( $help );  
		$help = array(
      'id'	=> 'bulk_actions',
      'title'	=> __('Bulk Action'),
      'content'	=> '<p>' . __( 'You can delete multiple shortcodes at once using the bulk action option. You can select the shortcodes to be deleted using the checkboxes, select the delete action from Bulk Action dropdown and click apply. This will permanently delete all the selected shortcodes from the list. ' ) . '</p>',
    	) ;
		$screen->add_help_tab( $help );  
	}
	//Set pagination count into esg_shortcode_per_page
	function esg_set_option($status, $option, $value) {
		if ( 'esg_shortcode_per_page' == $option ) return $value;
		return $value;	
	}
	//Add dynamic pagination count
	function esg_add_option(){
		global $table;
		$option = 'per_page';
		$args = array(
		    'label' => 'Number of items per page:',
		    'default' => 20,
		    'option' => 'esg_shortcode_per_page'
		);
		add_screen_option( $option, $args );
		$table  = new Esg_Admin_Table_list();
	}
	protected function get_user_roles(){
		global $wp_roles;		
		$role      = [];
		$all_roles = $wp_roles->roles;
		foreach ($all_roles as $key => $value) {
			$capabilities = $value['capabilities'];
			$access = false;
			if(array_key_exists('easy_shortcode', $capabilities) && $capabilities['easy_shortcode'] == '1'){
				$access = true;
			}
			$role[] = [
				'name'   => $value['name'],
				'key'    => $key,
				'access' => $access
			];					
		}
		return $role;
	}
	//Plugin configuration 
	public function plugin_configuration(){
		global $wp_roles;
		$messages  = array();
		$form_data = $_POST;
		$role = $this->get_user_roles();
		// Check the page submitted or not
		if( isset( $form_data['doSubmit'] ) && $form_data['doSubmit'] == 'Update Permissions' ){	
			foreach ($role as $key => $value) {
				$capabilities = $value['capabilities'];
				if(isset($form_data['access_permission']) && is_array($form_data['access_permission']) && in_array($value['key'], $form_data['access_permission'])){
					$wp_roles->add_cap( $value['key'], 'easy_shortcode' );
				}
				else if($value['key'] != "administrator" && $value['key'] != "Administrator" ){
					$wp_roles->remove_cap( $value['key'], 'easy_shortcode' );
				}				
			}
			$messages['success'] = array("User permission successfully updated."); 
			$role = $this->get_user_roles();
		}		
		//define page heading.
		$page_heading = "Easy Short Code Configuration";
		include_once( 'partials/plugin-configuration-view.php' );
	}
	// Page edit codes
	public function page_edit(){
		$messages                  = array();
		$form_data                 = $_POST;
		$default_template 		   = '<div class="image"><img src="{image}" alt="" /></div><h2><a href="{permalink}">{title}</a></h2><div class="content">{content}</div>';
		$form_data['template']     = ( isset( $form_data['template'] ) ? $form_data['template'] : $default_template );
		$form_data['pagination']   = ( isset( $form_data['pagination'] ) ? 1 : 0 );
		$form_data['trim_content'] = ( isset( $form_data['trim_content'] ) ? 1 : 0 );
		// checking the page is edit or add page
		if( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) && !empty( $_GET['id']) && !isset( $form_data['doSubmit'] ) ){
			
			$existing_data = $this->wpdb->get_row( "SELECT * FROM $this->table_name WHERE id = '".((int)$_GET['id'])."'", 'ARRAY_A' );	
			
			
			$form_data = array_replace( $form_data, ( is_array($existing_data) ? $existing_data : array() ) );
			$form_data['shortcode'] = sprintf('[esg id="%d" title="%s"]', $form_data['id'], $form_data['name']);
		}
		$form_data['template']    = stripslashes( $form_data['template'] );
		$args = array(
		   'public'   => true,
		);		 
		$output          = 'objects'; // names or objects, note names is the default
		$operator        = 'and'; // 'and' or 'or'		 
		$post_types      = get_post_types( $args, $output, $operator );
		$taxonomy_result = array();
		$terms_result    = array();
		if( isset( $form_data['post'] ) && $form_data['post'] != '' ){
			$taxonomy_result = get_object_taxonomies( $form_data['post'], 'object' ); 
		}
		if(
			isset( $form_data['post'] ) && $form_data['post'] != '' && 
			isset( $form_data['taxonomy'] ) && $form_data['taxonomy'] != ''
		){
			$args = array(
			               'type' 	  => $form_data['post'],
			               'taxonomy' => $form_data['taxonomy'],
			               'orderby'  => 'name',
			               'order'    => 'ASC'
			           );
			$terms_result = get_categories( $args ); 
		}
		if( isset( $form_data['terms'] ) && !is_array( $form_data['terms'] ) ){
			$form_data['terms'] = explode( ',', $form_data['terms'] );
		}
		// Check the page submitted or not
		if( isset( $form_data['doSubmit'] ) && $form_data['doSubmit'] == 'save' ){			
			$messages                  = $this->form_validate( $form_data ); //form validation callback
			if( count( $messages['error'] ) == 0 ){ // if errors not found
				if( isset( $form_data['id'] ) && !empty( $form_data['id'] ) ){
					$reponse                = $this->update_shortcode( $form_data ); //update shortcode
				}
				else{
					$reponse         = $this->insert_shortcode( $form_data ); //insert shortcode
					$form_data['id'] = $reponse['id'];					
				}
				$form_data['shortcode'] = $reponse['shortcode'];
				$messages        = $reponse['messages'];				
			}
		}		
		//define page heading.
		$page_heading = ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && is_numeric( $_GET['id'] ) ? 'Edit' : 'Add' ).' Easy Shortcodes';
		include_once( 'partials/edit-view.php' );

	}

	// Shortcode updation callback.
	public function update_shortcode( $form_data ){
		$this->wpdb->update( 
			$this->table_name, 
			array( 
				'name'             => $form_data['name'],
				'post'             => $form_data['post'],
				'taxonomy'         => ( isset( $form_data['taxonomy'] ) && $form_data['taxonomy'] != '' ? $form_data['taxonomy'] : '' ),
				'terms'             => ( ( isset($form_data['terms'] ) && is_array( $form_data['terms'] ) && count( $form_data['terms']) > 0) ? implode( ',', $form_data['terms']) : '' ),
				'template'         => addslashes( $form_data['template'] ),
				'wrapper_class'    => $form_data['wrapper_class'],
				'pagination'       => $form_data['pagination'],
				'pagination_count' => ( ( $form_data['pagination'] == 1 ) ? round( $form_data['pagination_count'] ) : '' ),
				'trim_content'     => $form_data['trim_content'],
				'trim_count'       => ( ($form_data['trim_content'] == 1 ) ? round( $form_data['trim_count'] ) : '' ),
				'maximum_post'	   => ( !empty( $form_data['maximum_post'] ) ? round( $form_data['maximum_post'] ) : 0 ),
				'sort'             => $form_data['sort'],
				'order_by'         => $form_data['order_by'],
			), 
			array( 'id' => (int) $form_data['id'] ), 
			array( 
				'%s', '%s', '%s', '%s','%s', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%s'
			),
			array( '%d' ) 
		);
		$shortcode = sprintf( '[esg id="%d" title="%s"]', $form_data['id'], $form_data['name'] );
		return array(
						'shortcode' => $shortcode,
						'messages' => array(
							'success' => array('Shortcode successfully updated.')
						)
					);
	}

	// Shortcode insertion callback.
	public function insert_shortcode( $form_data ){
		$this->wpdb->insert( 
			$this->table_name, 
			array( 
				'name'             => $form_data['name'],
				'post'             => $form_data['post'],
				'taxonomy'         => ( isset($form_data['taxonomy'] ) && $form_data['taxonomy'] != '' ? $form_data['taxonomy'] : '' ),
				'terms'             => ( ( isset($form_data['terms'] ) && is_array( $form_data['terms'] ) && count( $form_data['terms'] ) > 0 ) ? implode( ',', $form_data['terms'] ) : '' ),
				'template'         => $form_data['template'],
				'wrapper_class'    => $form_data['wrapper_class'],
				'pagination'       => $form_data['pagination'],
				'pagination_count' => ( ( $form_data['pagination'] == 1 ) ? round( $form_data['pagination_count'] ) : 0 ),
				'trim_content'     => $form_data['trim_content'],
				'trim_count'       => ( ( $form_data['trim_content'] == 1 ) ? round( $form_data['trim_count'] ) : 0 ),
				'maximum_post'	   => ( !empty( $form_data['maximum_post'] ) ? round( $form_data['maximum_post'] ) : 0 ),
				'sort'             => $form_data['sort'],
				'order_by'         => $form_data['order_by'],
				'created_by'       => get_current_user_id(),
				'active'           => '1'
			), 
			array( 
				'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%d', '%s'
			) 
		);
		$shortcode = sprintf( '[esg id="%d" title="%s"]', $this->wpdb->insert_id, $form_data['name'] );
		return array(
			'id'        => $this->wpdb->insert_id,
			'shortcode' => $shortcode,
			'messages'  => array(
				'success' => array('Shortcode successfully generated.')
				)
		);
	}

	// Edit/Add shortcode validation callback
	public function form_validate( $form_data ){
		$errors    = array();
		if( !isset( $form_data['name'] ) || $form_data['name'] == '' ){
			$errors[] = "Title field required.";
		}
		if( !isset( $form_data['post'] ) || $form_data['post'] == '' ){
			$errors[] = "Post type field required.";
		}
		if( ( isset( $form_data['pagination'] ) && $form_data['pagination'] == 1 ) && ( !isset( $form_data['pagination_count'] ) || $form_data['pagination_count'] == '') ){
			$errors[] = "Pagination Count field required.";
		}
		else if( ( isset( $form_data['pagination'] ) && $form_data['pagination'] == 1 ) && (  !is_numeric( $form_data['pagination_count'] ) || $form_data['pagination_count'] <= 0 ) ){
			$errors[] = "Pagination Count should be greater than Zero.";
		}
		if( ( isset( $form_data['trim_content'] ) && $form_data['trim_content'] == 1 ) && ( !isset($form_data['trim_count'] ) || $form_data['trim_count'] == '' ) ){
			$errors[] = "Trim Count field required.";
		}
		else if( ( isset($form_data['trim_content'] ) && $form_data['trim_content'] == 1) && ( !is_numeric($form_data['trim_count'] ) || $form_data['trim_count'] <= 0 ) ){
			$errors[] = "Trim Count should be greater than Zero.";
		}
		if( isset( $form_data['maximum_post'] ) && $form_data['maximum_post'] != '' && $form_data['maximum_post'] <= 0 ){
			$errors[] = "Maximum no.of Post should be greater than Zero.";
		}
		return array('error' => $errors);
	}

	// Default list page callback
	public function page_list(){
		global $table;
		$page_heading = 'Easy Shortcodes';
		include_once( 'partials/list-view.php' );
	}

	// Common ajax call back
	public function esg_ajax_callback(){ 
		$form_data      = $_POST;
		if( isset( $form_data['perform'] ) ){
			// Identifying the execution based on parameter
			switch ( $form_data['perform'] ) {
				case 'get_taxonomy':
					$this->get_taxonomy( $form_data );
					break;
				case 'get_terms':
					$this->get_terms( $form_data );
					break;
				default:
					# code...
					break;
			}
		}
	}

	// The function retrieves list of terms based on selected post type and taxonomy.
	private function get_terms( $form_data ){
		$terms_html  = '<label for="terms">Terms</label>%s';
		$options = '';
		$args = array(
			               'type' 	  => $form_data['post'],
			               'taxonomy' => $form_data['taxonomy'],
			               'orderby'  => 'name',
			               'order'    => 'ASC'
			           );
		$terms = get_categories($args);
		foreach($terms as $key => $term) {
			$options .= sprintf( '<div class="item"><label><input type="checkbox" name="terms[]" value="%s" /> %s</label></div>', $term->term_id, $term->name ); 
		}
		$terms_html = sprintf( $terms_html, $options );
		$response       = array(
			'message'       => "success",
			'terms_html' => ( count( $terms )> 0 ? $terms_html : '' ),
		);
		echo json_encode( $response );
		wp_die();
	}

	// The function retrieves taxonomy list based on selected post type.
	private function get_taxonomy( $form_data ){
		$taxonomy_html  = '<label for="taxonomy">Taxonomy</label>
							<select name="taxonomy" class="taxonomy" id="taxonomy">%s</select>';
		$options = '<option value= "" > --Select -- </option>';
		if( isset( $form_data['post'] ) && $form_data['post'] != '' ){
			$taxonomy = get_object_taxonomies( $form_data['post'], 'object' );
			foreach ( $taxonomy as $key => $row ) {
				if( $row->label != '' ){
					$options .= sprintf( '<option value="%s">%s</option>', $row->name, $row->label );
				}				
			}
		}
		$taxonomy_html = sprintf( $taxonomy_html, $options );
		$response       = array(
			'message'       => "success",
			'taxonomy_html' => $taxonomy_html,
		);
		echo json_encode( $response );
		wp_die();
	}
}
