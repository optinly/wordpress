<?php

namespace Optinly\App\Controllers\Admin;
defined('ABSPATH') or die;

use Optinly\App\Api\optinlyApi;
use Optinly\App\Helpers\Template;
use Optinly\App\Models\Connection as ConnectionModel;

class Main
{
    /**
     * Add menu for the plugin
     */
    function addMenu()
    {
        add_menu_page(__('Optinly Connection wizard', OPTINLY_TEXT_DOMAIN), __('Optinly', OPTINLY_TEXT_DOMAIN), 'manage_options', OPTINLY_SLUG, array($this, 'manageMenus'), 'dashicons-buddicons-pm');
    }

    /**
     * Adding stylesheets and scripts required by admin
     * @param $hook
     */
    function adminScripts($hook)
    {
        if ($hook != 'toplevel_page_' . OPTINLY_SLUG) {
            return;
        }
        //Enqueue css
        wp_enqueue_style(OPTINLY_SLUG . '-admin', OPTINLY_BASE_URL . 'App/Assets/Css/admin.css', array(), OPTINLY_VERSION);
        if (!wp_script_is(OPTINLY_SLUG . 'track-user-cart', 'enqueued')) {
            //Enqueue js
            wp_enqueue_script(OPTINLY_SLUG . '-admin', OPTINLY_BASE_URL . 'App/Assets/Js/admin.js', array('jquery'), OPTINLY_VERSION);
            $optinly_data = array(
                'slug' => OPTINLY_SLUG,
                'ajax_url' => admin_url('admin-ajax.php'),
                'reconnect_btn_txt' => __('Re-Connect'),
                'connect_btn_txt' => __('Connect'),
            );
            $optinly_data = apply_filters('optinly_localize_admin_data', $optinly_data);
            wp_localize_script(OPTINLY_SLUG . '-admin', 'optinly_admin_data', $optinly_data);
        }
    }

    /**
     * Manage all pages
     */
    function manageMenus()
    {
        if (isset($_GET["page"]) && sanitize_text_field($_GET["page"]) == OPTINLY_SLUG) {
            $template_helper = new Template();
            $app = new optinlyApi();
            $path = rtrim(OPTINLY_BASE_PATH, '/') . '/App/Views/Admin/connection.php';
            $connection_model = new ConnectionModel();
            $data = array(
                'app_id' => $connection_model->getAppId(),
                'is_app_connected' => $connection_model->isAppConnected(),
                'app_dashboard_url' => $app->app_url
            );
            $template_helper->setPath($path)->setData($data)->display();
        }
    }

    /**
     * Validate the APP ID entered by the user
     */
    function validateAppId()
    {
        $connection_model = new ConnectionModel();
        $connection_model->saveAppStatus(0);
        if (isset($_POST['app_id'])) {
            $app_id = sanitize_text_field($_POST['app_id']);
            $connection_model->saveAppId($app_id);
            if (empty($app_id)) {
                wp_send_json_error(__("Please enter App-Id!", OPTINLY_TEXT_DOMAIN));
            } else {
                $api = new optinlyApi();
                try {
                    $site_url = $this->removeHttp(site_url());
                    $site_url = apply_filters('optinly_get_site_url_for_verification', $site_url);
                    $api_response = $api->validateAppId($app_id, $site_url);
                    $api_response = apply_filters('optinly_app_id_validation_success', $api_response);
                    $connection_model->saveAppStatus(1);
                    wp_send_json_success($api_response);
                } catch (\Exception $e) {
                    do_action('optinly_app_id_validation_failed');
                    wp_send_json_error($e->getMessage());
                }
            }
        } else {
            wp_send_json_error(__("Invalid request found", OPTINLY_TEXT_DOMAIN));
        }
    }

    /**
     * remove http from url
     * @param $url
     * @return mixed
     */
    function removeHttp($url)
    {
        $disallowed = array('http://', 'https://');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }

    /**
     * Validate the APP ID entered by the user
     */
    function disconnectApp()
    {
        $connection_model = new ConnectionModel();
        $connection_model->saveAppStatus(0);
        wp_send_json_success(__('App disconnected successfully', OPTINLY_TEXT_DOMAIN));
    }
}