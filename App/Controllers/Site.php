<?php

namespace Optinly\App\Controllers;
defined('ABSPATH') or die;

use Optinly\App\Api\optinlyApi;
use Optinly\App\Models\Connection;

class Site
{
    function includeScript()
    {
        $model = new Connection();
        if ($model->isAppConnected() == 1) {
            $app_id = $model->getAppId();
            $popup_url = $this->getPopupJs();
            ?>
            <script id="optinly_script" async="true" data-app_id="<?php echo $app_id; ?>"
                    src="<?php echo $popup_url; ?>"></script>
            <?php
        }
    }

    /**
     * shortcode
     * @param array $attributes
     */
    function addShortcode($attributes = array())
    {
        $all_attributes = wp_parse_args($attributes, array('id' => ''));
        echo '<div class="optinly-embed-popup-' . $all_attributes['id'] . '"></div>';
    }

    /**
     * getting the PopUp js url
     * @return mixed|void
     */
    function getPopupJs()
    {
        $api = new optinlyApi();
        $js_url = $api->popup_js_url;
        return apply_filters('optinly_popup_js_url', $js_url);
    }
}