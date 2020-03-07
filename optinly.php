<?php
/**
 * Plugin name: Optinly
 * Plugin URI: https://www.optinly.com
 * Description: Shop popups on your site
 * Author: Optinly
 * Author URI: https://www.optinly.com
 * Version: 2.0.2
 * Slug: optinly-for-wordpress
 * Text Domain: optinly-for-wordpress
 * Domain Path: /i18n/languages/
 * Plugin URI: https://www.optinly.com
 * Requires at least: 4.6.1
 * Contributers: Sathyaseelan
 */
//Define plugin version
defined('OPTINLY_VERSION') or define('OPTINLY_VERSION', '1.0.0');
//Define plugin text domain
defined('OPTINLY_TEXT_DOMAIN') or define('OPTINLY_TEXT_DOMAIN', 'optinly-for-wordpress');
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
