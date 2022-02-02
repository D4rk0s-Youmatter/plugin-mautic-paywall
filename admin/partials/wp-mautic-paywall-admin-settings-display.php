<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.labubulle.com
 * @since      1.0.0
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       Wp_Mautic_Paywall.com/team
 * @since      1.0.0
 *
 * @package    PluginName
 * @subpackage PluginName/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>WP Mautic</h2>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php" class="wp_Mautic_Paywall_Options_Form">
        <!--
        Callback url for API : <code><?php //echo admin_url('admin.php?page=wp-mautic-paywall&mautic_callback'); ?></code>
        -->

        <?php
            settings_fields('Wp_Mautic_Paywall_general_settings');
            do_settings_sections('Wp_Mautic_Paywall_general_settings');
            submit_button("Save/update settings"); 
        ?>

    </form>
</div>