<?php

/**
 * Retrieve and display plugin info
 *
 * @since 1.0.0
 * @method bsas_plugin_info
 * @param  string $slug Plugin slug
 * @return mixed
 */
function bsas_plugin_info($slug)
{
    add_filter('extra_plugin_headers', function () {
        return ['GitHub Plugin URI', 'Twitter'];
    });

    $plugin_data = get_plugin_data(BSAS_PATH . $slug . '.php');

    return $plugin_data;
}

/**
 * Activate account suspension
 *
 * @since 1.0.0
 * @method bsas_activate_suspension
 */
function bsas_activate_suspension()
{
    // Soft fail for unauthorized users
    if (!current_user_can('manage_options')) {
        return;
    }

    // Make sure the option exists, first...
    if (get_option('bsas_settings')) {
        // Get current options
        $options = Account_Suspended::get_instance()->get_settings();

        // Change activated
        $options['activated'] = 1;

        // Update settings
        update_option('bsas_settings', $options);
    }
}

add_action('bsas_activate', 'bsas_activate_suspension');

/**
 * Deactivate account suspension
 *
 * @since 1.0.0
 * @method bsas_deactivate_suspension
 */
function bsas_deactivate_suspension()
{
    // Soft fail for unauthorized users
    if (!current_user_can('manage_options')) {
        return;
    }

    // Make sure the option exists, first...
    if (get_option('bsas_settings')) {
        // Get current options
        $options = Account_Suspended::get_instance()->get_settings();

        // Change activated
        $options['activated'] = 0;

        // Update settings
        update_option('bsas_settings', $options);
    }
}

add_action('bsas_deactivate', 'bsas_deactivate_suspension');

if (!function_exists('wp_scripts')) {
    /**
     * Exposes enqueued WordPress scripts and styles
     *
     * @since 1.0.0
     * @method wp_scripts
     * @return WP_Scripts
     */
    function wp_scripts()
    {
        global $wp_scripts;

        if (!($wp_scripts instanceof WP_Scripts)) {
            $wp_scripts = new WP_Scripts();
        }

        return $wp_scripts;
    }
}
