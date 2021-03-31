<?php

namespace Optinly\App\Controllers;
defined('ABSPATH') or die;

use mysql_xdevapi\Exception;
use Optinly\App\Api\optinlyApi;
use Optinly\App\Models\Connection;
use Optinly\App\Models\Settings as SettingsModel;

class Site extends Base
{
    function includeScript()
    {
        $model = new Connection();
        if ($model->isAppConnected() == 1) {
            $app_id = $model->getAppId();
            $popup_url = $this->getPopupJs();
            ?>
            <script>
                !function (e, c) {
                    !function (e) {
                        const o = c.createElement("script");
                        o.async = "true", o.dataset.app_id = "<?php echo $app_id?>",o.id="optinly_script",
                            o.type = "application/javascript", o.src = e, c.body.appendChild(o)
                    }("<?php echo $popup_url ?>")
                }(window, document);
            </script>
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
     * register required api endpoints
     */
    function registerAPIEndpoints()
    {
        register_rest_route('optinly/v1', '/subscribe/(?P<type>[a-zA-Z0-9-]+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'handleRestCallback')
        ));
    }

    function handleRestCallback(\WP_REST_Request $request)
    {
        $settings_model = new SettingsModel();
        $settings = $settings_model->getSettings();
        $app_secret_key = $settings_model->getSecretKey();
        if(!empty($app_secret_key)) {
            $requestParams = $request->get_params();
            $defaultRequestParams = array(
                'lead' => array(
                    'name' => '',
                    'first_name' => '',
                    'last_name' => '',
                    'email' => '',
                    'phone' => '',
                    'additional_data' => array()
                ),
                'digest' => '',
            );
            $params = wp_parse_args($requestParams, $defaultRequestParams);
            $cipher_text_raw = json_encode($params['lead'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $reverse_hmac = hash_hmac('sha256', $cipher_text_raw, $app_secret_key);
            if (hash_equals($reverse_hmac, $params['digest'])) {
                $type = $request->get_param('type');
                switch ($type) {
                    case "mailpoet":
                        try {
                            $this->handleMailpoet($params['lead'], $settings, $settings_model);
                            $status = 200;
                            $response = array('success' => true, 'RESPONSE_CODE' => 'PROCESSED', 'message' => 'User subscribed!');
                        } catch (\Exception $e) {
                            $status = 500;
                            $response = array('success' => true, 'RESPONSE_CODE' => 'ERROR', 'message' => $e->getMessage());
                        }
                        break;
                    default:
                        $status = 404;
                        $response = array('success' => false, 'RESPONSE_CODE' => 'INTEGRATION_NOT_FOUND', 'message' => 'Chosen integration not available!');
                        break;
                }
            } else {
                $status = 400;
                $response = array('success' => false, 'RESPONSE_CODE' => 'SECURITY_BREACH', 'message' => 'Security breached!');
            }
        }else{
            $status = 400;
            $response = array('success' => false, 'RESPONSE_CODE' => 'NO_SECRET_KEY', 'message' => 'No secret key found');
        }
        return new \WP_REST_Response($response, $status);
    }

    /**
     * @param $data
     * @param $settings
     * @param $settings_model
     * @throws \Exception
     */
    function handleMailpoet($data, $settings, $settings_model)
    {
        if ($this->isPluginActive('mailpoet/mailpoet.php')) {
            if (class_exists(\MailPoet\API\API::class)) {
                $mailpoet_api = \MailPoet\API\API::MP('v1');
                $subscriber = [];
                $subscriber_form_fields = $mailpoet_api->getSubscriberFields();
                foreach ($subscriber_form_fields as $field) {
                    if (!isset($data[$field['id']])) {
                        continue;
                    }
                    $subscriber[$field['id']] = $data[$field['id']];
                }
                $list_ids = $settings_model->getOption($settings, 'mailpoet_list_id');
                // Check if subscriber exists. If subscriber doesn't exist an exception is thrown
                try {
                    $get_subscriber = $mailpoet_api->getSubscriber($subscriber['email']);
                } catch (\Exception $exception) {
                }
                if (!$get_subscriber) {
                    // Subscriber doesn't exist let's create one
                    $mailpoet_api->addSubscriber($subscriber, $list_ids);
                } else {
                    // In case subscriber exists just add him to new lists
                    $mailpoet_api->subscribeToLists($subscriber['email'], $list_ids);
                }
            }
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