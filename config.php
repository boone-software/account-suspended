<?php

/**
 * Plugin version
 */

define('BSAS_VERSION', '1.0.0');

/**
 * Plugin paths
 */

define('BSAS_PATH', plugin_dir_path(__FILE__));
define('BSAS_CLASSES_PATH', BSAS_PATH . 'includes/classes/');
define('BSAS_FUNCTIONS_PATH', BSAS_PATH . 'includes/functions/');
define('BSAS_VIEWS_PATH', BSAS_PATH . 'views/');
define('BSAS_STYLES_PATH', BSAS_PATH . 'assets/styles/');

/**
 * Plugin URLs
 */

define('BSAS_URL', plugin_dir_url(__FILE__));
define('BSAS_SCRIPTS_URL', BSAS_URL . 'assets/scripts/');
define('BSAS_STYLES_URL', BSAS_URL . 'assets/styles/');
define('BSAS_IMAGES_URL', BSAS_URL . 'assets/images/');

/**
 * Debug mode definitions
 */

define('BSAS_ASSETS_SUFFIX', (defined('DEBUG_MODE') && DEBUG_MODE) ? '' : '.min');
