<?php
/**
 * Plugin Name: Tribus Job Listings
 * Description: Job vacancies and submissions management plugin.
 * Version 0.1.0
 * Requires at least: 6.9
 * Requires PHP: 8.3
 * Author: Shane Muirhead (Tribus)
 * Text Domain: tribus-job-listings
 * Domain Path: /languages
 */

declare (strict_types = 1);

if (! defined( 'ABSPATH' )) {
    exit;
}

define('TRIBUS_JL_PLUGIN_FILE', __FILE__);
define('TRIBUS_JL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TRIBUS_JL_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TRIBUS_JL_PLUGIN_PATH . 'vendor/autoload.php';

add_action('plugins_loaded', static function () {
   \TRIBUS_JL\Plugin::init();
});
