<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.labubulle.com
 * @since      1.0.0
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/admin
 * @author     Jérémie Gisserot <jeremie@labubulle.com>
 */
class Wp_Mautic_Paywall_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $Wp_Mautic_Paywall    The ID of this plugin.
     */
    private $Wp_Mautic_Paywall;

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
     * @param      string    $Wp_Mautic_Paywall       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($Wp_Mautic_Paywall, $version)
    {

        $this->Wp_Mautic_Paywall = $Wp_Mautic_Paywall;
        $this->version = $version;

        add_action('admin_menu', array($this, 'addPluginAdminMenu'), 9);
        add_action('admin_init', array($this, 'registerAndBuildFields'));
        if (isset($_GET["mautic_connect"])) {
            add_action('admin_init', array($this, 'mautic_api_connect'));
        }
        if (isset($_GET["mautic_callback"])) {
            //add_action('admin_init', array($this, 'mautic_api_connect'));
            $this->mautic_api_catch_callback();
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Mautic_Paywall_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Mautic_Paywall_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->Wp_Mautic_Paywall, plugin_dir_url(__FILE__) . 'css/wp-mautic-paywall-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->Wp_Mautic_Paywall, plugin_dir_url(__FILE__) . 'js/wp-mautic-paywall-admin.js', array('jquery'), $this->version, false);
    }
    public function addPluginAdminMenu()
    {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page($this->Wp_Mautic_Paywall, 'Mautic paywall', 'administrator', $this->Wp_Mautic_Paywall, array($this, 'displayPluginAdminDashboard'), 'dashicons-universal-access-alt', 26);

        //add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        //add_submenu_page($this->Wp_Mautic_Paywall, 'Plugin Name Settings', 'Settings', 'administrator', $this->Wp_Mautic_Paywall . '-settings', array($this, 'displayPluginAdminSettings'));
    }
    public function displayPluginAdminDashboard()
    {
        require_once plugin_dir_path(__FILE__) . 'partials/' . $this->Wp_Mautic_Paywall . '-admin-settings-display.php';
    }
    public function displayPluginAdminSettings()
    {
        // set this var to be used in the settings-display view
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'wpMauticSettingsMessages'));
            do_action('admin_notices', $_GET['error_message']);
        }

        require_once plugin_dir_path(__FILE__) . 'partials/' . $this->Wp_Mautic_Paywall . '-admin-settings-display.php';
    }
    public function wpMauticSettingsMessages($error_message)
    {
        switch ($error_message) {
            case '1':
                $message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain');
                $err_code = esc_attr('Wp_Mautic_Paywall_general_settings');
                $setting_field = 'Wp_Mautic_Paywall_general_settings';
                break;
        }
        $type = 'error';
        add_settings_error(
            $setting_field,
            $err_code,
            $message,
            $type
        );
    }

    public function registerAndBuildFields()
    {
        add_settings_section(
            'Wp_Mautic_Paywall_general_section',
            __("Configure your Mautic paywall.", "wp-mautic-paywall"),
            array($this, 'Wp_Mautic_Paywall_display_general_account'),
            'Wp_Mautic_Paywall_general_settings'
        );


        /////////
        // Enable
        /////////

        $args = array(
            'type'      => 'input',
            'subtype'   => 'checkbox',
            'id'    => 'Wp_Mautic_Paywall_Enable',
            'name'      => 'Wp_Mautic_Paywall_Enable',
            'required' => 'false',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Enable',
            'Enable paywall ?',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );
        unset($args);
        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Enable',
        );

        /////////
        // Paragraphs
        /////////

        $args = array(
            'type'      => 'input',
            'subtype'   => 'text',
            'id'    => 'Wp_Mautic_Paywall_Paragraphs',
            'name'      => 'Wp_Mautic_Paywall_Paragraphs',
            'required' => 'false',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Paragraphs',
            'Show paywall after how many paragraphs ?',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );
        unset($args);
        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Paragraphs',
        );

        /////////
        // Skip Button Txt
        /////////

        $args = array(
            'type'      => 'input',
            'subtype'   => 'text',
            'id'    => 'Wp_Mautic_Paywall_Skip_Button_text',
            'name'      => 'Wp_Mautic_Paywall_Skip_Button_text',
            'required' => 'false',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Skip_Button_text',
            'Skip button text',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );
        unset($args);
        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Skip_Button_text',
        );
        /////////
        // Title
        /////////

        $args = array(
            'type'      => 'input',
            'subtype'   => 'text',
            'id'    => 'Wp_Mautic_Paywall_Title',
            'name'      => 'Wp_Mautic_Paywall_Title',
            'required' => 'false',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_public_key',
            'Title',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );
        unset($args);
        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Title',
        );


        /////////
        // Message
        /////////

        $args = array(
            'type'      => 'editor',
            'id'    => 'Wp_Mautic_Paywall_Message',
            'name'      => 'Wp_Mautic_Paywall_Message',
            'required' => 'false',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Message',
            'Message',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );
        unset($args);

        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Message',
        );       
        
        // Post form
        $args = array(
            'type'      => 'textarea',
            'id'    => 'Wp_Mautic_Paywall_Mautic_Form',
            'name'      => 'Wp_Mautic_Paywall_Mautic_Form',
            'required' => 'true',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Mautic_Form',
            'Mautic post form code',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );

        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Mautic_Form',
        );  
        unset($args);

        // Comments form
        $args = array(
            'type'      => 'textarea',
            'id'    => 'Wp_Mautic_Paywall_Mautic_Comments_Form',
            'name'      => 'Wp_Mautic_Paywall_Mautic_Comments_Form',
            'required' => 'true',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );
        add_settings_field(
            'Wp_Mautic_Paywall_Mautic_Comments_Form',
            'Mautic comments form code',
            array($this, 'Wp_Mautic_Paywall_render_settings_field'),
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_general_section',
            $args
        );

        register_setting(
            'Wp_Mautic_Paywall_general_settings',
            'Wp_Mautic_Paywall_Mautic_Comments_Form',
        );  
        unset($args); 


    }
    public function Wp_Mautic_Paywall_display_general_account()
    {
        //echo _e("Configure the text of your paywall message and Mautic form code.", "wp-mautic-paywall");
    }
    public function Wp_Mautic_Paywall_render_settings_field($args)
    {

        if ($args['wp_data'] == 'option') {
            $wp_data_value = get_option($args['name']);
        } elseif ($args['wp_data'] == 'post_meta') {
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
        }

        $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
        switch ($args['type']) {

            case 'input':
                if ($args['subtype'] != 'checkbox') {
                    $prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
                    $step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
                    $min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
                    $max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
                    if (isset($args['disabled'])) {
                        // hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
                        echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    } else {
                        echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    }
                    /*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->Wp_Mautic_Paywall.'_cost2" name="'.$this->Wp_Mautic_Paywall.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->Wp_Mautic_Paywall.'_cost" step="any" name="'.$this->Wp_Mautic_Paywall.'_cost" value="' . esc_attr( $cost ) . '" />*/
                } else {
                    $checked = ($value) ? 'checked' : '';
                    echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
                }
                break;
            case 'textarea':
                echo "<textarea name=" . $args['name'] . ">" . esc_attr($value) . "</textarea>";
                break;
            case 'editor':
                $settings = array(
                    'media_buttons' => false,
                    'teeny' => true,
                    'textarea_rows' => 5
                );
                wp_editor( esc_attr($value), $args['name'], $settings );

            default:
                # code...
                break;
        }
    }


    public function mautic_api_catch_callback()
    {
        if ($_GET["state"] && $_GET["code"]) {
            update_option("Wp_Mautic_Paywall_api_state", $_GET["state"]);
            update_option("Wp_Mautic_Paywall_api_code", $_GET["code"]);
        }
    }
}
