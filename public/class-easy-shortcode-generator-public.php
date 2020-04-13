<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       sherinshelvan.com
 * @since      1.0.0
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/public
 * @author     Sherin Shelvan <sherinshelvan@gmail.coom>
 */
class Easy_Shortcode_Generator_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	private $table_name;
	private $wpdb;
	public function __construct( $plugin_name, $version ) {
		global $table_prefix, $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->table_name  = $table_prefix . "easy_shortcode"; // table name of the plugin
		$this->wpdb        = $wpdb;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/easy-shortcode-generator-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/easy-shortcode-generator-public.js', array( 'jquery' ), $this->version, false );

	}
	public function register_plugin_shortcodes(){
		// Shortcode registration 
		add_shortcode( 'esg', array( $this, 'decode_esg_shortcode' ) ); 
	}

	// Shotcode callback while rendering in front page.
	public function decode_esg_shortcode( $atts, $content = "" ){

		// fetch shortcode data from table based on shortcode id.
		$shortcode = $this->wpdb->get_row( "SELECT * FROM $this->table_name WHERE id = ".$atts['id'] );

		//Default shortcode response.
		$response_data = sprintf( '<div class="esg-message esg-error"><p>[ esg id="%d" title="%s" ] This Shortcode not exist.</p></div>', $atts['id'], $atts['title'] );
		//Chek shortcode exist or not
		if( $shortcode !== null ){
			$shortcode_template = sprintf( '<div class="item" >%s</div>', stripslashes( $shortcode->template ) ); //Default template defining
			$paged = ( get_query_var( 'paged' ) && isset( $_GET['esg'] ) && $_GET['esg'] == $shortcode->id ) ? get_query_var( 'paged' ) : 1; // Page number
			$args = array(
				'post_type'      => $shortcode->post,
				'post_status'    => 'publish',
				'orderby'        => $shortcode->order_by,
				'order'          => $shortcode->sort,
				'lang'					 => '',
				'tax_query'      => array(),
			);
			// Check pagination exist or not
			if( $shortcode->pagination == 1 ){
				$args['posts_per_page'] =  $shortcode->pagination_count;
				$args['paged']          =  $paged;
			}
			// Check maximum post option selected or not while shortcode generation through the plug-in.
			if( $shortcode->maximum_post > 0) {
				$args['posts_per_page'] =  $shortcode->maximum_post;
				$args['paged']          = 1;
			}
			// Generate query based on taxonomy.
			if( $shortcode->taxonomy != '' ){
				$tax_query = array(
					'taxonomy'         => $shortcode->taxonomy,
					'include_children' => true
				);
				if( $shortcode->terms != '' ){
					$tax_query['terms'] = explode( ',', $shortcode->terms );
				}
				else{
					$tax_query['operator'] = 'EXISTS';
				}
				array_push( $args['tax_query'], $tax_query );
			}

			
			if( $shortcode->maximum_post > 0 && $shortcode->pagination == 1 ){
				$args['fields'] = 'ids';
				$args['post__in'] = get_posts( $args );
		    if( $shortcode->pagination == 1 ){
					$args['posts_per_page'] =  $shortcode->pagination_count;
					$args['paged']          =  $paged;
				}
		    $query = new WP_Query( $args );
			}
			else{
				$query = new WP_Query( $args );
			}
 
			// Post exist
	    if ( $query->have_posts() ) :
				$tokens        = array("{title}", "{content}", "{image}", "{permalink}", "{author}", "{date}");
				$response_data = '';
				//fetch post data one by one.
      	while ( $query->have_posts() ) : 
      		$query->the_post();
      		$content = strip_shortcodes( strip_tags(get_the_content() ?: '') );
      		//if except data exist content will be replaced with excerpt data. 
      		if( has_excerpt() && $shortcode->trim_content == 1){
      			$content = get_the_excerpt();
      		}
      		//check trim option enabled or not at the time of shortcode generation.
      		if( $shortcode->trim_content == 1 && strlen( $content ) > $shortcode->trim_count && strpos( $content, ' ', $shortcode->trim_count ) ){
      			//find next space after desired length.
      			$break_pos = strpos( $content, ' ', $shortcode->trim_count );
      			//trim content 0 point to break point.
      			$content   = substr( $content, 0, $break_pos ).'...';
      		}

      		// Replace default value with actual value 
      		$replace = array(
        			( trim( get_the_title() ) ?: '' ),
        			$content,
        			( has_post_thumbnail() ? get_the_post_thumbnail_url( '', 'large' ) : '' ),
        			( get_the_permalink() ?: '' ),
        			( get_the_author() ?: '' ),
        			( get_the_time() ? get_the_time( '<b>j</b> <b>F</b> <b>Y</b>' ) : '' ),
        		);
      		//replace the string with corresponding keywords
      		$response_data .= str_ireplace( $tokens, $replace, $shortcode_template );
      	endwhile;
      	$pagination = '';
      	//if pagination enabled from plug-in and maximum pagination exist.
      	if( $query->max_num_pages > 1 && $shortcode->pagination == 1 ){
      		$pagination = paginate_links( array(
	            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
	            'total'        => $query->max_num_pages,
	            'current'      => max( 1, $paged ),
	            'format'       => '?paged=%#%',
	            'show_all'     => false,
	            'type'         => 'list',
	            'end_size'     => 2,
	            'mid_size'     => 1,
	            'prev_next'    => true,
	            'prev_text'    => sprintf( '%1$s', __( '«', 'text-domain' ) ),
	            'next_text'    => sprintf( '%1$s', __( '»', 'text-domain' ) ),
	            'add_args'     => array( 'esg' => $shortcode->id ),
	            'add_fragment' => '',
	        ) );
      	}
      	//it will replace with esg shortcode tag in front-end.
      	$response_data = sprintf( '<div id="esg-%d" class="esg-wrapper %s">%s</div><div class="esg-pagination">%s</div>', $shortcode->id, $shortcode->wrapper_class, $response_data, $pagination );
          wp_reset_postdata();
	    else :
	    	// If post not exist it will return as response
	      $response_data = sprintf( '<div class="esg-message no-result">%s</div>', (_e( 'Sorry, no posts matched your criteria.'))  );
	    endif; 
		}
		return $response_data;
	}


}
