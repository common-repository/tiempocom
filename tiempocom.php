<?php
    /*
    Plugin Name: Tiempo.com
    Plugin URI: http://api.tiempo.com
    Description: Tiempo.com for WordPress. Allows to add weather widgets and shortcodes from the tiempo.com API.
    Version: 0.1.2
    Author: tiempocom
    Author URI: http://www.tiempo.com
    License: GPLv2 or later
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    */

    // Cache init
    global $lw_cache, $tiempocom_locale;

    // Core Libs
    if(!class_exists('LW_Cache')) 
        require_once(dirname( __FILE__ ) . '/core/cache.php');

    $lw_cache = new LW_Cache(array(
        'cache_dir' => plugin_dir_path( __FILE__ ) . 'cache/',
        'error_message' => __('<strong>Tiempo.com Plugin</strong>: Set write permissions to <strong>%s</strong> directory to enable caching.', 'tiempocom'),
    ));

    // API Libs
    require_once(dirname( __FILE__ ) . '/app/api.php');

    // Helpers
    require_once(dirname( __FILE__ ) . '/app/helpers/statics.php');
    require_once(dirname( __FILE__ ) . '/app/helpers/templates.php');
    require_once(dirname( __FILE__ ) . '/app/helpers/shortcodes.db.php');
    require_once(dirname( __FILE__ ) . '/app/helpers/shortcodes.form.php');
    require_once(dirname( __FILE__ ) . '/app/helpers/shortcodes.table.php');

    // Widgets
    require_once(dirname( __FILE__ ) . '/app/widget.php');

    // Shortcodes
    require_once(dirname( __FILE__ ) . '/app/shortcode.php');

    /**
    * TiempoCom Class
    */
    class TiempoCom
    {
        var $db_version = "1";
        var $api;

        function __construct() {}

        function register() {

            $this->api = new TiempoCom_API();
            
            $this -> register_shortcodes();

            add_action('init',                  array($this, 'init'));
            add_action('admin_init',            array($this, 'admin_init'));
            add_action('admin_menu',            array($this, 'admin_menu'));
            add_action('widgets_init',          array($this, 'widgets_init'));
            add_action('wp_enqueue_scripts',    array($this, 'enqueue_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('wp_ajax_api_tiempocom', array($this, 'ajax_api_tiempocom_callback'));

            register_activation_hook(__FILE__,  array($this, 'activate'));
            register_uninstall_hook(__FILE__,   array('TiempoCom', 'uninstall'));
            register_deactivation_hook(__FILE__,array($this, 'deactivate'));
        }

        public function init() {

            // Load plugin textdomain
            load_plugin_textdomain( 'tiempocom', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 

            // Add the gettext filter for template languages
            add_filter( 'gettext', 'tc_template_gettext_overwrite', 20, 3 );
        }

        public function admin_init() {}

        public function admin_menu() {

            // Add menu page for widget administration
            add_menu_page( 
                'Tiempo.com', 
                'Tiempo.com', 
                'manage_options', 
                'tiempocom/app/admin.php', '', 
                plugins_url( 'tiempocom/static/img/icon.png' ), 
                26
            );
        }

        public function widgets_init() {

            // Register widget
            register_widget( 'TiempoCom_Widget' );
        }

        public function register_shortcodes() {

            // Register [tiempocom id="ID"] shortcode
            add_shortcode( 'tiempocom', 'tc_shortcode_inv' );
        }

        public function enqueue_scripts() {

            // Register the front-end widgets styles
            wp_register_style( 'tiempocom_style', plugins_url('static/css/default.css', __FILE__), 1 );
            wp_enqueue_style('tiempocom_style');
        }

        public function admin_enqueue_scripts() {

            // Register the back-end styles
            wp_register_style( 'tc_backend_style', plugins_url('static/css/backend.css', __FILE__), 1 );
            wp_enqueue_style( 'tc_backend_style' );
            wp_enqueue_style( 'wp-color-picker' );

            // Register the back-end styles
            wp_register_script( 'tc_backend_scripts', plugins_url('static/js/backend.js', __FILE__), 
                array('jquery', 'wp-color-picker'), 1);

            wp_enqueue_script( 'tc_backend_scripts' );
        }

        public function ajax_api_tiempocom_callback() {

            $response = $this->api->get_list($_POST['type'], $_POST['id'], $_POST['language']);

            header('Content-Type: application/json');
            echo json_encode($response);
            if(!$response) echo json_encode('false');
            die();
        }

        public function activate() {

            // Create or upgrade table
            global $wpdb;

            // Get installed DB Version
            $db_installed_version = get_option( "tiempocom_db_version" );

            // Table name
            $table_name = $wpdb->prefix . "tc_shortcodes";

            // Check if udpate is needed
            if( $db_installed_version != $this->db_version ) {

                // Create table
                $sql = "CREATE TABLE $table_name (
                  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  title varchar(255) DEFAULT NULL,
                  lang varchar(10) DEFAULT NULL,
                  continent int(11) unsigned DEFAULT NULL,
                  country int(11) unsigned DEFAULT NULL,
                  province int(11) unsigned DEFAULT NULL,
                  location int(11) unsigned DEFAULT NULL,
                  location_link varchar(255) DEFAULT NULL,
                  location_label varchar(255) DEFAULT NULL,
                  time tinyint(2) unsigned DEFAULT '5',
                  format tinyint(2) unsigned DEFAULT '1',
                  style tinyint(2) unsigned DEFAULT '1',
                  meta longtext,
                  meta_formats longtext,
                  colors longtext,
                  font tinyint(2) unsigned DEFAULT '1',
                  cache_time bigint(20) DEFAULT NULL,
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

                // Run upgrade
                dbDelta( $sql );

                // Update with new DB Version
                update_option( "tiempocom_db_version", $this->db_version );
            }

            $domain = site_url();

            if($code = $this->api -> activate_plugin($domain)) {
                update_option('tc_install_code', $code->code);
            }

        }

        static function uninstall() {

            // Delete table
            global $wpdb;
            $table_name = $wpdb->prefix . "tc_shortcodes";
            $sql = "DROP TABLE IF_EXISTS $table_name;";
            $wpdb->query($sql);

            delete_option('tc_install_code');
        }

        public function deactivate() {

            // Clear all cache
            global $lw_cache;
            $lw_cache->clear();

            $code = get_option('tc_install_code');

            $this->api -> deactivate_plugin($code);
        }

    }

    // Everything begins here
    $tiempoCom = new TiempoCom();
    $tiempoCom -> register();

?>