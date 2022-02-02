<?php

use D4rk0s\WpMauticApi\WpMauticApi;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.labubulle.com
 * @since      1.0.0
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/public
 * @author     Jérémie Gisserot <jeremie@labubulle.com>
 */
class Wp_Mautic_Paywall_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-mautic-paywall-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-mautic-paywall-public.js', array('jquery'), $this->version, true);
    }

    /**
     * Check if user has an email on mautic
     *
     * @since    1.0.0
     */
    public function isMauticUser(): bool
    {
        if (!isset($_COOKIE['mtc_id'])) {
            return false;
        }
        $api = WpMauticApi::getAPI('contacts');
        $result = $api->get((int)$_COOKIE['mtc_id']);

        return isset($result["contact"]);
    }
    /**
     * Blurs the content if user is not connected to mautic
     *
     * @since    1.0.0
     */
    public function blur_posts_for_paywall($content)
    {
        
        
        if (!get_option("Wp_Mautic_Paywall_Enable")) {
            return $content;
        }
        if (!in_array(get_post()->post_type, ['post'])) {
            return $content;
        }
        if ($this->isMauticUser()) {
            return $content;
        }
        if ($_COOKIE["ym_nopaywall"]) {
            return $content;
        }
        
        $how_many_paragraphs = get_option("Wp_Mautic_Paywall_Paragraphs") ? get_option("Wp_Mautic_Paywall_Paragraphs") : 3;
        $skip_button_text = get_option("Wp_Mautic_Paywall_Skip_Button_text");

        $paywall_markup = "<section id='paywall' class='paywall_blurred_content'>";
        $paywall_markup .= "<div id='paywall_message' class='paywall_message'>";

        if ($skip_button_text) {
            $paywall_markup .= "<a href='#' id='skip_button'>". mb_convert_encoding($skip_button_text, 'HTML-ENTITIES', 'UTF-8') ."</a>";
        }
        $paywall_markup .= "<h2>". mb_convert_encoding(get_option("Wp_Mautic_Paywall_Title"), 'HTML-ENTITIES', 'UTF-8')  ."</h2><p>". mb_convert_encoding(get_option("Wp_Mautic_Paywall_Message"), 'HTML-ENTITIES', 'UTF-8') ."</p>". get_option("Wp_Mautic_Paywall_Mautic_Form");
        $paywall_markup .= "</div></section>";


        $doc_out = new DOMDocument('1.0', 'UTF-8');
        $doc_out->loadHTML("<div id='pre_paywall'></div>". $paywall_markup);

        $pre = $doc_out->getElementById('pre_paywall');
        $paywall = $doc_out->getElementById('paywall');
        
        $doc_in = new DOMDocument('1.0', 'UTF-8');
        $doc_in->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        
        $tagsloop = 0;
        $tagsBody = $doc_in->getElementsByTagName('body')->item(0);

        foreach ($tagsBody->childNodes as $tag) {

            if ($tagsloop > $how_many_paragraphs + 1) {
                $importtag = $doc_out->importNode($tag, true);
                $paywall->appendChild($importtag);
            } else {
                $importtag = $doc_out->importNode($tag, true);
                $pre->appendChild($importtag);  
            }
            if ($tag->tagName == "p") {
                $tagsloop++;
            }
        }

        $html = $doc_out->saveHTML($doc_out);
        return $html;

    }

    /**
     * Filters the comment form
     *
     * @since    1.0.0
     */
    public function filter_comment_form_for_paywall($fields) {
        return;
    }
    /**
     * Filters the comment form
     *
     * @since    1.0.0
     */
    public static function maybe_output_comments_form() {
        echo "<div id='paywall_message' class='paywall_message'><h2>". get_option("Wp_Mautic_Paywall_Title") ."</h2><p>". get_option("Wp_Mautic_Paywall_Message") ."</p>". get_option("Wp_Mautic_Paywall_Mautic_Comments_Form") ."</div>";
    }
}
