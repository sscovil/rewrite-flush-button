<?php
/**
 * Plugin Name: Rewrite Flush Button
 * Plugin URI: https://github.com/sscovil/rewrite-flush-button
 * Description: Adds a 'Flush Rewrite Rules' button to WP-Admin > Settings > Permalinks.
 * Version: 1.0
 * Author: sscovil
 * Author URI: http://shaunscovil.com
 * Text Domain: rewrite-flush-button
 * License: GPL2
 */

/**
 * Only instantiate plugin class when viewing WP-Admin > Settings > Permalinks or doing AJAX in wp-admin.
 */
global $pagenow;
if ( 'options-permalink.php' == $pagenow || defined( 'DOING_AJAX' ) && is_admin() )
    Rewrite_Flush_Button::instance();

/**
 * Class Rewrite_Flush_Button
 */
class Rewrite_Flush_Button {

    protected static $instance, $id;

    /**
     * Singleton Factory
     *
     * @return mixed
     */
    public static function instance() {
        if ( !isset( self::$instance ) ) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    /**
     * Construct
     *
     * Runs as soon as class is instantiated.
     */
    protected function __construct() {
        self::$id = 'rfb_060613';

        // Load text domain for language translation files.
        load_plugin_textdomain( 'rewrite-flush-button', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

        // Assign methods to WP action hooks.
        add_action( 'admin_init', array( $this, 'add_settings_section' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
        add_action( 'wp_ajax_flush_rewrite_rules', array( $this, 'flush_rewrite_rules' ) );
    }

    /**
     * Add Settings Section
     */
    function add_settings_section() {
        add_settings_section(
            $id       = 'troubleshooting',
            $title    = __( 'Troubleshooting', 'rewrite-flush-button' ),
            $callback = array( $this, 'add_settings_field' ),
            $page     = 'permalink'
        );
    }

    /**
     * Add Settings Field
     */
    function add_settings_field() {
        $label = __( 'Flush Rewrite Rules', 'rewrite-flush-button' );
        add_settings_field(
            $id       = self::$id,
            $title    = '<input type="button" id="' . self::$id . '" value="' . $label . '" class="button" />',
            $callback = array( $this, 'button_description' ),
            $page     = 'permalink',
            $section  = 'troubleshooting'
        );
    }

    /**
     * Button Description
     *
     * Callback used in add_settings_field() to display a brief description.
     */
    function button_description() {
        printf(
            '<div id="%s_desc" class="description" style="display: inline-block">%s</div>',
            self::$id,
            __( 'Flushing rewrite rules if your permalinks are not working correctly. This is usually caused by themes and plugins that add, remove or change custom post types & taxonomies.', 'rewrite-flush-button' )
        );
    }

    /**
     * Load JavaScript
     */
    function load_js() {
        wp_register_script(
            $handle = self::$id,
            $src    = plugins_url( 'js/rewrite-flush-button.js', __FILE__ ),
            $deps   = array( 'jquery' )
        );
        wp_enqueue_script( self::$id );

        // Pass array of parameters to JavaScript as object called 'RFB'.
        wp_localize_script(
            $handle      = self::$id,
            $object_name = 'RFB',
            $params      = $this->localize_script_parameters()
        );
    }

    /**
     * Localize Script Parameters
     *
     * @return array Parameters for wp_localize_script().
     */
    function localize_script_parameters() {
        $success_msg = __(
            'Success! Rewrite rules have been flushed.',
            'rewrite-flush-button'
        );
        $error_msg = __(
            'Error! Unable to flush rewrite rules; try deactivating all other plugins and switching to default theme.',
            'rewrite-flush-button'
        );
        return array(
            'action_id'   => 'flush_rewrite_rules',
            'button_id'   => '#' . self::$id,
            'desc_id'     => '#' . self::$id . '_desc',
            'nonce'       => wp_create_nonce( self::$id . '_nonce' ),
            'success_msg' => $success_msg,
            'error_msg'   => $error_msg,
        );
    }

    /**
     * Flush Rewrite Rules
     *
     * AJAX callback used to run flush_rewrite_rules() with nonce verification.
     */
    function flush_rewrite_rules() {
        if( check_ajax_referer( self::$id . '_nonce', 'nonce' ) ) {
            flush_rewrite_rules();
            die( '1' ); // Success!
        } else {
            die( '0' ); // Error.
        }
    }

}