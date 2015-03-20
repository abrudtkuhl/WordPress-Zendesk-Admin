<?php
/*
 * Plugin Name: WordPresss Zendesk Admin
 * Version: 1.0
 * Plugin URI: https://github.com/abrudtkuhl/WordPress-Zendesk-Admin
 * Description: Adds a Zendesk help desk widget into the WordPress Dashboard
 * Author: Andy Brudtkuhl
 * Author URI: http://youmetandy.com
 * Requires at least: 4.0
 * Tested up to: 4.1
 *
 * Text Domain: wordpress-zendesk-admin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Andy Brudtkuhl
 * @since 1.0.0
 */



// TODO
// - admin capabilities
// - add submenu to Settings


if ( ! defined( 'ABSPATH' ) ) exit;

class Zendesk_Admin {

  /**
   * Private variables
   */
  private $options;

  /**
   * Constructor
   */
  public function __construct() {
    add_action( 'admin_init', array( $this, 'admin_init' ) );
  }

  /**
   * Admin Settings
   */
  public function admin_menu() {
    add_submenu_page( 'settings', __( 'Zendesk', 'zdw' ), __( 'Zendesk Widget', 'zdw' ), 'settings', 'settings-zendesk-admin', array( $this, 'admin_page' ) );
  }

  public function admin_page() {
    $this->options = get_option( 'zendesk_admin_options' );
    ?>
    <div class="wrap">
      <h2>Zendesk Admin Widget</h2>
      <form method="post" action="options.php">
        <?php
          // This prints out all hidden setting fields
          settings_fields( 'zendesk_admin_option_group' );
          do_settings_sections( 'zendesk-admin-settings' );
          submit_button();
        ?>
      </form>
    </div>

  <?php }

  public function admin_init() {
    register_setting(
      'zendesk_admin_option_group', // Option group
      'zendesk_admin_options', // Option name
      array( $this, 'sanitize' ) // Sanitize
    );

    add_settings_section(
      'rcp_terms_admin_section', // ID
      '', // Title
      array( $this, 'admin_print_section_info' ), // Callback
      'zendesk-admin-settings' // Page
    );

    add_settings_field(
      'zendesk_script', // ID
      'Zendesk Script', // Title
      array( $this, 'zendesk_script_callback' ), // Callback
      'zendesk-admin-settings', // Page
      'rcp_terms_admin_section' // Section
    );
  }

  public function zendesk_script_callback()
  {
    printf(
      '<input type="textarea" id="zendesk_script" name="zendesk_admin_options[zendesk_script]" value="%s" placeholder="<script>" />',
      isset( $this->options['zendesk_script'] ) ? esc_attr( $this->options['zendesk_script']) : ''
    );
  }


  public function admin_print_section_info() {
    print 'Enter Zendesk Details';
  }

  public function sanitize( $input )
  {
    $new_input = array();

    if( isset( $input['zendesk_script'] ) )
      $new_input['zendesk_script'] = sanitize_text_field( $input['zendesk_script'] );

    return $new_input;
  }
}

if ( is_admin() ) {
  $zendesk_admin = new Zendesk_Admin;
}
