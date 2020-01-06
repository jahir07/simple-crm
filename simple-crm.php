<?php
/**
* Plugin Name: Simple CRM
* Plugin URI: http://xstheme.com/
* Description: Simple CRM Testing Plugin
* Author: Jahirul Islam Mamun
* Author URI: https://www.xstheme.com/
* Version: 1.0.0
* Text Domain: simple-crm
* Domain Path: /languages
*/


/**
 * Copyright (c) 2019 CodexTune (email: admin@xstheme.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

namespace Simple_CRM;

use Simple_CRM\Classes\Assets;
use Simple_CRM\Classes\PostType\Customer;
use Simple_CRM\Classes\Frontend;


// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Simple_CRM class
 * @since  1.0
 * @class Simple_CRM The class that holds the entire Simple_CRM plugin
 */
final class Simple_CRM {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.6.0';

    /**
     * Holds various class instances
     *
     * @since 2.6.10
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Simple_CRM class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        if ( ! $this->is_supported_php() ) {
            return;
        }

        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php() {
        if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
            return false;
        }

        return true;
    }


    /**
     * Initializes the Simple_CRM() class
     *
     * Checks for an existing Simple_CRM() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Simple_CRM();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 1.0
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }


    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {

        // Plugin version.
        if ( ! defined( 'SCRM_VERSION' ) ) {
            define( 'SCRM_VERSION', $this->version );
        }

        // Plugin Root File.
        if ( ! defined( 'SCRM_FILE' ) ) {
            define( 'SCRM_FILE', __FILE__ );
        }

        // Plugin Folder Path.
        if ( ! defined( 'SCRM_DIR' ) ) {
            define( 'SCRM_DIR', plugin_dir_path( __FILE__ ) );
        }

        // Plugin Folder URL.
        if ( ! defined( 'SCRM_URL' ) ) {
            define( 'SCRM_URL', plugin_dir_url( __FILE__ ) );
        }

        // Plugins Classes
        if ( ! defined( 'SCRM_CLASSES' ) ) {
            define( 'SCRM_CLASSES', SCRM_DIR . 'includes/classes' );
        }

        // Plugins Assets url
        if ( ! defined( 'SCRM_ASSETS' ) ) {
            define( 'SCRM_ASSETS', SCRM_URL . 'assets' );
        }
        
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'wp_simple_crm_installed' );

        if ( ! $installed ) {
            update_option( 'wp_simple_crm_installed', time() );
        }

        update_option( 'wp_simple_crm_version', SCRM_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once SCRM_CLASSES . '/assets.php';

        if ( $this->is_request( 'current_user' ) ) {
            require_once SCRM_CLASSES . '/customer.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once SCRM_CLASSES . '/frontend.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        // initialize classes
        add_action( 'init', [ $this, 'init_classes' ] );

        // Localize our plugin
        add_action( 'init', [ $this, 'localization_setup' ] );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {
        $this->container['assets'] = new Assets();

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['frontend'] = new Frontend();
        }
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'simple-crm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
            return is_admin();

            case 'current_user' :
            return current_user_can('administrator');

            case 'ajax' :
            return defined( 'DOING_AJAX' );

            case 'rest' :
            return defined( 'REST_REQUEST' );

            case 'cron' :
            return defined( 'DOING_CRON' );

            case 'frontend' :
            return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }




} // Simple_CRM

/**
 * Load wp_simple_crm Plugin when all plugins loaded
 *
 * @return void
 */
function wp_simple_crm() {
    return Simple_CRM::init();
}

// Lets Play :)
wp_simple_crm();
