<?php

namespace Optinly\App;

use Optinly\App\Controllers\Admin\Main;
use Optinly\App\Controllers\Site;

class Router
{
    /**
     * Init all hooks required
     */
    function initHooks()
    {
        if (is_admin() || wp_doing_ajax()) {
            $admin = new Main();
            add_action('admin_menu', array($admin, 'addMenu'));
            add_action('admin_enqueue_scripts', array($admin, 'adminScripts'), 100);
            add_action('wp_ajax_optinly_validate_app_id', array($admin, 'validateAppId'));
            add_action('wp_ajax_optinly_disconnect_app', array($admin, 'disconnectApp'));
        } else {
            $site = new Site();
            add_action('wp_footer', array($site, 'includeScript'));
        }
    }
}
