<?php

namespace Optinly\App\Controllers;

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
            <script>
                var upopsObject = {up_app_id: '<?php echo $app_id; ?>', email: ''};
                window.uberpopups = upopsObject;
                !function (e, t, n, p, o, a, i, s, c) {
                    e[o] || (i = e[o] = function () {
                        i.process ? i.process.apply(i, arguments) : i.queue.push(arguments)
                    }, i.queue = [], i.t = 1 * new Date, s = t.createElement(n), s.async = 1, s.src = p + "?t=" + Math.ceil(new Date / a) * a, c = t.getElementsByTagName(n)[0], c.parentNode.insertBefore(s, c))
                }(window, document, "script", '<?php echo $popup_url; ?>', "rabbit", 1), rabbit("init", upopsObject.up_app_id), rabbit("event", "pageload")
            </script>
            <?php
        }
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