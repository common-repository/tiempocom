<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
* TC_Shortcodes_Table class
*/
class TC_Shortcodes_Table extends WP_List_Table {

	function __construct(){

        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'shortcode',
            'plural'    => 'shortcodes',
            'ajax'      => false
        ) );
    }

    /**
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column
     */
    function column_default($item, $column_name){


        $formats = tc_static_get_formats();

        switch($column_name){
            case 'id':
            case 'title':
            case 'location_label':
            case 'time':
                return $item[$column_name];
            case 'format':
                return $formats[$item[$column_name]];
           	case 'lang':
           		return strtoupper($item[$column_name]);
            case 'style':
            	return '<img src="http://css13.tiempo.com/widget/css/galeria' . $item[$column_name] . '/pq8.png" />';
            case 'shortcode':
            	return '<input type="text" disabled value="[tiempocom id=' . $item['id'] . ']" /><a title="'.__('Copy to clipboard', 'tiempocom').'" href="javascript:;" class="copytoclipboard" rel="[tiempocom id=' . $item['id'] . ']"></a>';
            default:
                return print_r($item,true);
        }
    }

    /**
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column
     */
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&shortcode=%s">%s</a>',$_REQUEST['page'],'edit',$item['id'], __('Edit', 'tiempocom')),
            'delete'    => sprintf('<a href="?page=%s&action=%s&shortcode=%s">%s</a>',$_REQUEST['page'],'delete',$item['id'], __('Delete', 'tiempocom')),
        );
        
        //Return the title contents
        return sprintf('%1$s %2$s',
            $item['title'],
            $this->row_actions($actions)
        );
    }

    /**
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }
    
    /**
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    function get_columns(){

        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'id' 		=> __('ID', 'tiempocom'),
            'title'     => __('Title', 'tiempocom'),
            'location_label'    =>  __('Location', 'tiempocom'),
            'format' => __('Format', 'tiempocom'),
            'time' => __('Days', 'tiempocom'),
            'lang' => __('Language', 'tiempocom'),
            'style'  =>  __('Style', 'tiempocom'),
            'shortcode' =>  __('Shortcode', 'tiempocom')
        );
        return $columns;
    }
    
    /**
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    function get_sortable_columns() {

        $sortable_columns = array(
            'id'     => array('id', false), 
            'title'    => array('title', false),
            'location_label'  => array('location_label', false),
            'style'  => array('style', false),
            'shortcode'  => array('shortcode', false),
            'lang'  => array('lang', false),
            'time'  => array('time', false),
            'format'  => array('format', false),
        );
        return $sortable_columns;
    }
    
    /**
    * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
    */
    function get_bulk_actions() {

        $actions = array(
            'delete'    => __('Delete', 'tiempocom')
        );
        return $actions;
    }

    /**
    * @see $this->prepare_items()
    */
    function process_bulk_action() {

        // Reset values
        $error = '';
        $updated = '';

        // Hiden notice, just for JS validation purposes
        ?>
            <div class="error" id="required_location" style="display:none;"><p>
                <?php _e('A location is required.', 'tiempocom'); ?></p>
            </div>
        <?php

        // If request save
        if(isset($_REQUEST['save'])) {

            if(false !== tc_db_create_shortcode($_REQUEST)) {

                $updated = __('Shortcode created successfully.', 'tiempocom');
            } else {

                $error = __('Error creating shortcode.', 'tiempocom');
            }
        }

        // If request updating
        if(isset($_REQUEST['update'])) {

            if( false !== tc_db_update_shortcode($_GET['shortcode'], $_REQUEST)) {

                $updated = __('Shortcode updated successfully.', 'tiempocom');
            } else {

                $error = __('Error updating shortcode.', 'tiempocom');
            }
        }

        // If action is defined and is not delete
        if($this->current_action() and $this->current_action() != 'delete') {

            // Load shortcode form
            tc_print_shortcode_form( $this->current_action() );

        } else if( $this->current_action() === 'delete' ) {

            // Delete shortcode
            if( tc_db_delete_shortcode($_REQUEST['shortcode']) ) {

                $updated = __('Shortcode deleted successfully.', 'tiempocom');
            } else {

                $error = __('Error deleting shortcode.', 'tiempocom');
            }
        }

        // Show error message if exists
        if(!empty($error)) { 
            ?>
                <div class="error"><p><?php echo $error; ?></p></div>
            <?php 
        }

        // Show updated message if exists
        if(!empty($updated)) { 
            ?>
                <div class="updated"><p><?php echo $updated; ?></p></div>
            <?php
        }
    }

    /**
    * @global WPDB $wpdb
    * @uses $this->_column_headers
    * @uses $this->items
    * @uses $this->get_columns()
    * @uses $this->get_sortable_columns()
    * @uses $this->get_pagenum()
    * @uses $this->set_pagination_args()
    */
    function prepare_items() {

        $per_page = 20;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();

    	global $wpdb;

    	$query = 'SELECT * FROM ' . $wpdb->prefix . 'tc_shortcodes';

    	$data = $wpdb->get_results($query, ARRAY_A);
                
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}

?>