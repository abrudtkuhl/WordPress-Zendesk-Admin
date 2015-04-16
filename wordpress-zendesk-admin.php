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
    $this->options = get_option( 'zendesk_admin_options' );
    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_action( 'admin_footer', array( $this, 'admin_widget' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
  }

  /**
   * Admin Settings
   */
  public function admin_menu() {
    add_options_page( 'Zendesk Admin Help Widget', 'Zendesk Help', 'manage_options', 'zendesk-admin', array( $this, 'admin_page' ));
  }

  public function admin_page() { ?>
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
      'zendesk_admin_section', // ID
      'Widget Options', // Title
      array( $this, 'admin_print_section_info' ), // Callback
      'zendesk-admin-settings' // Page
    );

    add_settings_field(
      'zendesk_enabled', // ID
      'Enable Zendesk Help Widget', // Title
      array( $this, 'zendesk_enabled_callback' ), // Callback
      'zendesk-admin-settings', // Page
      'zendesk_admin_section' // Section
    );

    add_settings_field(
      'zendesk_id', // ID
      'Zendesk Subdomain', // Title
      array( $this, 'zendesk_id_callback' ), // Callback
      'zendesk-admin-settings', // Page
      'zendesk_admin_section' // Section
    );

    add_settings_section(
      'zendesk_admin_advanced_section',
      'Advanced Options',
      array( $this, 'admin_print_advanced_instructions'),
      'zendesk-admin-settings'
    );

    add_settings_field(
      'zendesk_script', // ID
      'Zendesk Script', // Title
      array( $this, 'zendesk_script_callback' ), // Callback
      'zendesk-admin-settings', // Page
      'zendesk_admin_advanced_section' // Section
    );
  }

  public function zendesk_enabled_callback()
  {
    if ( isset( $this->options['zendesk_enabled'] ) ) {
      echo '<input type="checkbox" id="zendesk_enabled" name="zendesk_admin_options[zendesk_enabled]" value="1"' . checked( 1,  $this->options['zendesk_enabled'], false ) . '/>';
    } else {
      echo '<input type="checkbox" id="zendesk_enabled" name="zendesk_admin_options[zendesk_enabled]" value="1" />';
    }
  }

  public function zendesk_id_callback()
  {
    printf(
      '<input type="text" id="zendesk_id" name="zendesk_admin_options[zendesk_id]" placeholder="mysite" value="%s" /> .zendesk.com',
      isset( $this->options['zendesk_id'] ) ? esc_attr( $this->options['zendesk_id']) : ''
    );
  }

  public function zendesk_script_callback()
  {
    printf(
      '<textarea rows="10" cols="80" type="textarea" id="zendesk_script" name="zendesk_admin_options[zendesk_script]" placeholder="">%s</textarea>',
      isset( $this->options['zendesk_script'] ) ? esc_attr( $this->options['zendesk_script']) : ''
    );
  }

  public function admin_print_section_info() {
    print 'Enter Zendesk Details';
  }

  public function admin_print_advanced_instructions() {
    print 'ADVANCED USE ONLY';
  }

  public function sanitize( $input )
  {
    $new_input = array();

    if( isset( $input['zendesk_id'] ) )
      $new_input['zendesk_id'] = sanitize_text_field( $input['zendesk_id'] );

    if( isset( $input['zendesk_enabled'] ) )
      $new_input['zendesk_enabled'] = $input['zendesk_enabled'];
    else
      $new_input['zendesk_enabled'] = false;

    if( isset( $input['zendesk_script'] ) )
      $new_input['zendesk_script'] = $input['zendesk_script'];

    return $new_input;
  }

  public function admin_widget() {
    if ( !isset( $this->options['zendesk_enabled'] ) || 1 != $this->options['zendesk_enabled'])
      exit;

    $current_user = wp_get_current_user();
    ?>
      <!-- Start of Zendesk Widget script -->
      <script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","<?php echo $this->options['zendesk_id'] ?>.zendesk.com");/*]]>*/</script>
      <!-- End of Zendesk Widget script -->
      <script>
        zE(function() {
          zE.identify({name: '<?php echo $current_user->display_name; ?>', email: '<?php echo $current_user->user_email; ?>', externalId: '<?php echo $current_user->ID; ?>'});
        });
      </script>
  <?php }
}

if ( is_admin() ) {
  $zendesk_admin = new Zendesk_Admin;
}
