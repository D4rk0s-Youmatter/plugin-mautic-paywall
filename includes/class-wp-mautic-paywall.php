<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.labubulle.com
 * @since      1.0.0
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/includes
 * @author     Jérémie Gisserot <jeremie@labubulle.com>
 */
class Wp_Mautic_Paywall
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wp_Mautic_Paywall_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('Wp_Mautic_Paywall_VERSION')) {
            $this->version = Wp_Mautic_Paywall_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wp-mautic-paywall';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_Mautic_Paywall_Loader. Orchestrates the hooks of the plugin.
     * - Wp_Mautic_Paywall_i18n. Defines internationalization functionality.
     * - Wp_Mautic_Paywall_Admin. Defines all hooks for the admin area.
     * - Wp_Mautic_Paywall_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-mautic-paywall-loader.php';


        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-mautic-paywall-i18n.php';


        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-mautic-paywall-admin.php';

        /**
         * Loads the Mautic API file
         */
        //require_once plugin_dir_path(dirname(__FILE__)) . '/vendor/autoload.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-mautic-paywall-public.php';

        $this->loader = new Wp_Mautic_Paywall_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_Mautic_Paywall_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Wp_Mautic_Paywall_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Wp_Mautic_Paywall_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Wp_Mautic_Paywall_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        //$this->loader->add_action('wp_footer', $plugin_public, 'rewind_to_previous_url');
        //$this->loader->add_action('init', $plugin_public, 'mautic_api_connect');
        //$this->loader->add_action('init', $plugin_public, 'mautic_get_user_data');
        
        // Blur posts for paywall
        $this->loader->add_filter('the_content', $plugin_public, 'blur_posts_for_paywall', 1);
        //$this->loader->add_filter('comment_form_fields', $plugin_public, 'filter_comment_form_for_paywall', 1);
        
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wp_Mautic_Paywall_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
