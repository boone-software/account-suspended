<?php

/**
 * Boone Software Account Suspended
 *
 * Plugin Name: Account Suspended
 * Plugin URI: https://boone.io
 * Description: Adds a splash page that indicates account suspension.
 * Version: 1.0.0
 * Author: Boone Software
 * Author URI: https://boone.io
 * Twitter: boonesoftware
 * GitHub Plugin URI: https://github.com/boone-software/account-suspended
 * GitHub Branch: master
 * Text Domain: boone-account-suspended
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Exit if PHP version is too low
if (version_compare(phpversion(), '5.6', '<')) exit;

/**
 * Plugin functionality
 */

// Require constants
require_once('config.php');

// Require helpers
require_once(BSAS_FUNCTIONS_PATH . 'helpers.php');

// Require filters
require_once(BSAS_FUNCTIONS_PATH . 'filters.php');

/**
 * Recommended and required plugins
 */

$config = [
    'id' => 'boone-account-suspended',
    'default_path' => '',
    'menu' => 'tgmpa-install-plugins',
    'parent_slug' => 'plugins.php',
    'capability' => 'manage_options',
    'has_notices' => true,
    'dismissable' => true,
    'dismiss_msg' => '',
    'is_automatic' => false,
    'message' => ''
];

$plugins = [
    // Advanced Access Manager
    [
        'name'      => 'Advanced Access Manager',
        'slug'      => 'advanced-access-manager',
        'required'  => false,
    ]
];

/**
 * Multisite functionality
 */

if (is_multisite() && !function_exists('is_plugin_active_for_network')) {
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
 * Frontend views
 */

// Include relevant classes
require_once(BSAS_CLASSES_PATH . 'account-suspended.php');

// Register hooks
register_activation_hook(__FILE__, ['Account_Suspended', 'activate']);
register_deactivation_hook(__FILE__, ['Account_Suspended', 'deactivate']);

// Add functionality for plugin load
add_action('plugins_loaded', ['Account_Suspended', 'get_instance']);

/**
 * Admin views
 */

if (is_admin()) {

    // Include relevant classes
    require_once(BSAS_CLASSES_PATH . 'tgm-plugin-activation.php');
    require_once(BSAS_CLASSES_PATH . 'account-suspended-admin.php');

    // Configure TGMPA
    add_action('tgmpa_register', function () use ($plugins, $config) {
        tgmpa($plugins, $config);
    });

    // Add functionality for plugin load
    add_action('plugins_loaded', ['Account_Suspended_Admin', 'get_instance']);
}
