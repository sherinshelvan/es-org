<?php 
/* This class is used to generate the List Tables that populate WordPress' various admin screens. */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Esg_Admin_Table_list extends WP_List_Table {
	public $result;
	private $table_name,$user_table;
	private $wpdb;
	public function __construct( ) {
		global $table_prefix, $wpdb;
		parent::__construct( array(
			'singular' => __('shortcode', 'esg'),
			'plural'   => __('shortcodes', 'esg'),
        ) );        
		$this->wpdb       = $wpdb;
		$this->table_name = $table_prefix . "easy_shortcode";
		$this->user_table = $table_prefix . "users";
	}

	/* The method get_columns() is needed to label the columns on the top and bottom of the table. The keys in the array have to be the same as in the data array otherwise the respective columns aren't displayed. */
	function get_columns(){
	  	$columns = array(
			'cb'           => '<input type="checkbox" />',
			'name'         => __('Title', 'esg'),
			'post_type'    => __('Post Type', 'esg'),
			'shortcode'    => __('Shortcode', 'esg'),
			'author'       => __('Author', 'esg'),
			'created_date' => __("Created", 'esg')
	  	);
	  	return $columns;
	}

	/* Before actually displaying each column WordPress looks for methods called column_{key_name}, There has to be such a method for every defined column. To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined: */
	function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
	    case 'name':
	    	return $item->name;
	    case 'post_type':
	    	return ucfirst($item->post);
	    case 'shortcode':
	    	$shortcode = sprintf( '[esg id="%d" title="%s"]', $item->id, $item->name );
	    	return $shortcode;
	    case 'author':
	   	 	$user_info = get_userdata($item->created_by);
	    	// return $user_info->first_name.' '.$user_info->last_name;
	    	return $user_info->display_name;
	    case 'created_date':
	    	return date(get_option('date_format'), strtotime($item->created_date));	    
	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}
	/* Prepare_items defines two arrays controlling the behaviour of the table: */
	function prepare_items() {
		$action 			   = $this->current_action();
		switch ($action) {
			case 'delete':
				$this->do_delete();
				break;			
			default:
				# code...
				break;
		}		
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		// $this->_column_headers = array($columns, $hidden, $sortable);
		$this->_column_headers = $this->get_column_info();
		$this->items           = $this->get_shortcode_result();
		
	}

	// Shortcode delete codes 
	function do_delete(){
		$ids = array();
		if( isset( $_GET['id'] ) && ( $_GET['id'] > 0 ) ){
			$ids = array( $_GET['id'] );
		}
		if( isset( $_POST['shortcode'] ) && is_array( $_POST['shortcode'] ) && count( $_POST['shortcode']) > 0 ){
			$ids = $_POST['shortcode'];
		}
		if( count( $ids ) > 0 ){
			$this->wpdb->query("DELETE FROM $this->table_name
					WHERE id IN (".(implode(', ', $ids)).")"
			);
		}
	}

	/* What it does contain is some code to mark certain columns as sortable */
	function get_sortable_columns(){
		$sortable_columns = array(
			'name'         => array( 'name', false ),
			'post_type'    => array( 'post', false ),
			'created_date' => array( 'created_date', false ),
			// 'author'    => array('author',false)
		);
		return $sortable_columns;
	}

	/* These actions will appear if the user hovers the mouse cursor over the table: */
	function column_name($item) {
	  	$actions = array(
            'edit'      => sprintf( '<a href="?page=%s&action=%s&id=%s" title="Edit">Edit</a>', 'easy-shortcode-edit', 'edit', $item->id ),
            'delete'  => sprintf( '<a href="javascript:void(0);" class="esg-delete" title="Delete" data-page="easy-shortcode" data-action="delete" data-id="%d" ><label for="detete-checkbox">Delete</label></a>', $item->id )
	    );

	  return sprintf( '%1$s %2$s', $item->name, $this->row_actions($actions) );
	}

	/* Bulk action are implemented by overwriting the method get_bulk_actions() and returning an associated array: */
	function get_bulk_actions() {
	  $actions = array(
	    'delete'    => 'Delete'
	  );
	  return $actions;
	}

	/* The checkboxes for the rows have to be defined separately. As mentioned above there is a method column_{column} for rendering a column. The cb-column is a special case: */
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="shortcode[]" value="%s" />', $item->id
        );    
  }

  /* If there are no items in the list the standard message is "No Shortcodes found." is displayed. If you want to change this message you can overwrite the method no_items(): */
  function no_items() {
	  _e( 'No Shortcodes found.' );
	}

	/* Fetching shortcodes from database*/
	function get_shortcode_result(){
		$per_page     = $this->get_items_per_page('esg_shortcode_per_page', 20);
		$current_page = $this->get_pagenum();
		$offset       = ( ($current_page - 1 ) * $per_page );
		$condition    = '';
		if( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ){
			$condition = " WHERE name like '%".$_REQUEST['s']."%'";
		}
		$total_items  = $this->wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name".$condition );
		$orderby      = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'name';
		// If no order, default to asc
		$order        = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

		$result = $this->wpdb->get_results( "
				SELECT *
				FROM $this->table_name shortcode 
				$condition
				ORDER BY $orderby $order
				LIMIT $per_page OFFSET $offset
			");
		$this->set_pagination_args( array(
		    'total_items' => $total_items,                  //WE have to calculate the total number of items
		    'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );
		return $result;
	}
}