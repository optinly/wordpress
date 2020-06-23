<?php
/**
 * Plugin name: Optinly
 * Plugin URI: https://www.optinly.com
 * Description: Popups, flyers, banners, sidebars for WordPress
 * Author: Optinly
 * Version: 1.0.2
 * Slug: optinly
 * Text Domain: optinly
 * Domain Path: /languages/
 * Plugin URI: https://www.optinly.com
 * Requires at least: 4.6.1
 * Contributers: Optinly
 */
defined('ABSPATH') or die;
//Define plugin version
defined('OPTINLY_VERSION') or define('OPTINLY_VERSION', '1.0.2');
//Define plugin text domain
defined('OPTINLY_TEXT_DOMAIN') or define('OPTINLY_TEXT_DOMAIN', 'optinly');
//Define plugin text domain
defined('OPTINLY_SLUG') or define('OPTINLY_SLUG', 'optinly');
//Define plugin base path
defined('OPTINLY_BASE_PATH') or define('OPTINLY_BASE_PATH', plugin_dir_path(__FILE__));
//Define plugin base URL
defined('OPTINLY_BASE_URL') or define('OPTINLY_BASE_URL', plugin_dir_url(__FILE__));
if (version_compare(phpversion(), '5.6', '<')) {
    return false;
}
if (!file_exists(__DIR__ . "/vendor/autoload.php")) {
    return false;
} else {
    require __DIR__ . "/vendor/autoload.php";
}
$optinly = new \Optinly\App\Router();
$optinly->initHooks();
