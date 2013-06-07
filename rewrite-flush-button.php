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
        $desc  = __( 'Flushing rewrite rules if your permalinks are not working correctly. This is usually caused by themes and plugins that add, remove or change custom post types & taxonomies.', 'rewrite-flush-button' );
        $nonce = '<span id="' . self::$id . '_nonce" class="hidden">' . wp_create_nonce( self::$id . '_nonce' ) . '</span>';
        printf(
            '<div id="%s_desc" class="description" style="display: inline-block">%s</div>%s',
            self::$id,
            $desc,
            $nonce
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

            // Pass array of parameters to JavaScript as object called 'RFB'.
            $params = $this->localize_script_parameters();
            wp_localize_script(
                $handle      = self::$id,
                $object_name = 'RFB',
                $params
            );
        }
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
            'Error! Unable to flush rewrite rules; try deactivating plugins and switching to default theme.',
            'rewrite-flush-button'
        );
        return array(
            'action_id'   => 'flush_rewrite_rules',
            'button_id'   => '#' . self::$id,
            'desc_id'     => '#' . self::$id . '_desc',
            'nonce_id'    => '#' . self::$id . '_nonce',
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
        if( wp_verify_nonce( $_REQUEST['nonce'], self::$id . '_nonce' ) ) {
            flush_rewrite_rules();
            die( '1' ); // Success!
        } else {
            die( '0' ); // Error.
        }
    }

}