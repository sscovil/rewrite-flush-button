<?php
/**
 * Plugin Name: Rewrite Flush Button
 * Plugin URI: http://shaunscovil.com
 * Description: Adds a 'Flush Rewrite Rules' button to WP-Admin > Settings > Permalinks.
 * Version: 0.1
 * Author: sscovil
 * Author URI: http://shaunscovil.com
 * Text Domain: rewrite-flush-button
 * License: GPL2
 */

if ( is_admin() )
    Rewrite_Flush_Button::instance();

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
        self::$id    = 'rfb_060613';

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
        $label = __( 'Troubleshooting', 'rewrite-flush-button' );
        add_settings_section(
            'troubleshooting',
            $label,
            array( $this, 'add_settings_field' ),
            'permalink'
        );
    }

    /**
     * Add Settings Field
     *
     * Adds the 'Flush Rewrite Rules' button to the Optional section of WP-Admin > Settings > Permalinks.
     */
    function add_settings_field() {
        $label = __( 'Flush Rewrite Rules', 'rewrite-flush-button' );
        $button = '<input type="button" id="' . self::$id . '" value="' . $label . '" class="button" />';
        add_settings_field(
            self::$id,
            $button,
            array( $this, 'button_description' ),
            'permalink',
            'troubleshooting'
        );
    }

    /**
     * Button Description
     *
     * Callback used in add_settings_field() to display a brief description and nonce field.
     */
    function button_description() {
        $desc  = __( 'Try flushing rewrite rules if your permalinks are not working correctly. This is usually caused by themes and plugins that add, remove or change custom post types & taxonomies.', 'rewrite-flush-button' );
        printf(
            '<p id="%s_desc" class="description">%s</p>',
            self::$id,
            $desc
        );
    }

    /**
     * Load JavaScript
     *
     * Only loads plugin script when viewing WP-Admin > Settings > Permalinks.
     */
    function load_js() {
        global $pagenow;
        if ( $pagenow == 'options-permalink.php' ) {
            wp_register_script(
                self::$id,
                plugins_url( 'js/rewrite-flush-button.js', __FILE__ ),
                array( 'jquery' )
            );
            wp_enqueue_script( self::$id );

            // Pass array of parameters to JavaScript as an object called 'RFB'.
            $params = array(
                'actionid' => 'flush_rewrite_rules',
                'buttonid' => '#' . self::$id,
                'descid'   => '#' . self::$id . '_desc',
                'nonce'    => wp_create_nonce( self::$id . '_nonce' )
            );
            wp_localize_script(
                $handle      = self::$id,
                $object_name = 'RFB',
                $params
            );
        }
    }

    /**
     * Flush Rewrite Rules
     *
     * AJAX callback used to run flush_rewrite_rules() with nonce verification.
     */
    function flush_rewrite_rules() {
        if( check_ajax_referer( self::$id . '_nonce', 'nonce' ) ) {
            flush_rewrite_rules();
            die('1'); // Success!
        } else {
            die('0'); // Error.
        }
    }

}