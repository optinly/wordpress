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
        //addsubscriber form mailpoet_api
        register_rest_route('optinly/v1', '/subscribe/(?P<type>[a-zA-Z0-9-]+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'handleRestSubscribeCallback')
        ));

        //get_list form mailpoet_api
        register_rest_route('optinly/v1', '/list/(?P<type>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRestGetListCallback')
        ));
    }

    function handleRestSubscribeCallback(\WP_REST_Request $request)
    {
        $settings_model = new SettingsModel();
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
                    "list_id"=>'',
                    "send_confirmation_email"=> true
                ),
                'digest' => '',
            );
            $params = wp_parse_args($requestParams, $defaultRequestParams);
            $cipher_text_raw = json_encode($params['lead'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $cipher_text_raw;
            $reverse_hmac = hash_hmac('sha256', $cipher_text_raw, $app_secret_key);

            if (hash_equals($reverse_hmac, $params['digest'])) {
                $type = $request->get_param('type');
                switch ($type) {
                    case "mailpoet":
                        if (class_exists(\MailPoet\API\API::class)) {

                            // Get MailPoet API instance
                            $mailpoet_api = \MailPoet\API\API::MP('v1');
                
                
                            $data=$params["lead"];

                            $subscriber=array(
                                "email"=>$data["email"],
                                "first_name"=>$data["first_name"],
                                "last_name"=>$data["last_name"]
                            );
                
                            $list_ids = array($data['list_id']);

                            $options=array(
                                "send_confirmation_email"=>$data['send_confirmation_email']
                            );

                
                            try{
                                $add_sub=$mailpoet_api->addSubscriber($subscriber, $list_ids, $options);
                                //return new WP_REST_Response(array('data' => $add_sub), 200);
                                $status = 200;
                                $response = array('success' => true, 'RESPONSE_CODE' => 'PROCESSED', 'data' => $add_sub);
                            } catch (\Exception $e) {
                                $error_message = $e->getMessage();
                                //return new WP_REST_Response(array('message' => $error_message), 400);
                                $status = 500;
                                $response = array('success' => false, 'message' => $error_message);
                            }
                
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

    function handleRestGetListCallback(\WP_REST_Request $request)
    {
        $headers = $request->get_headers();
        $settings_model = new SettingsModel();
        $app_secret_key = $settings_model->getSecretKey();
        // echo $app_secret_key;
        $type = $request->get_param('type');


        if(!empty( $headers["api_key"][0])){
            $api_res_key=$headers["api_key"][0];
            if($api_res_key==$app_secret_key){

                switch ($type){
                    case "mailpoet":
                        try{
                            if (class_exists(\MailPoet\API\API::class)) {
                                // Get MailPoet API instance
                                $mailpoet_api = \MailPoet\API\API::MP('v1');
                                //Get mailpoet list
                                $list = [];
                                $list = $mailpoet_api->getLists();
                        
                                $status = 200;
                                $response = array('success' => true, 'RESPONSE_CODE' => 'PROCESSED', 'list' => $list);
                                
                            }
                        }
                        catch (\Exception $e){
                            $status = 400;
                            $response = array('success' => true, 'RESPONSE_CODE' => 'ERROR', 'message' => $e->getMessage());
                        }
                        break;
                    default:
                        $status = 404;
                        $response = array('success' => false , 'message' => 'Chosen Integration Not Available!');
                        break;
                }

            } else{
                $status = 400;
                $response = array('success' => false, 'RESPONSE_CODE' => 'BAD_REQUEST', 'message' => 'Invalid_API_KEY');
            }

        } else{
            $status = 400;
            $response = array('success' => false, 'RESPONSE_CODE' => 'NO_API_KEY', 'message' => 'No API_KEY Found');
        }
        

        return new \WP_REST_Response($response, $status);
    }


    /**
     * @param $data
     * @param $settings
     * @param $settings_model
     * @throws \Exception
     */


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