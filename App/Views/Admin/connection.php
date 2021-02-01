<?php
defined('ABSPATH') or die;
/**
 * @var $app_dashboard_url string
 * @var $is_app_connected string
 * @var $app_id string
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th scope="row" class="app_id">
            <label for="<?php echo OPTINLY_SLUG ?>_app_id"><?php _e('Enter your App ID', OPTINLY_TEXT_DOMAIN); ?></label>
        </th>
        <td class="forminp forminp-text">
            <input type="text" name="app_id" id="<?php echo OPTINLY_SLUG ?>_app_id" class="regular-text" value="<?php echo $app_id; ?>">
            <p class="optinly-field-description">
                <?php _e('Get your App-id ', OPTINLY_TEXT_DOMAIN); ?>
                <a target="_blank"
                   href="<?php echo $app_dashboard_url; ?>"><?php _e('here', OPTINLY_TEXT_DOMAIN); ?></a>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
        </th>
        <td>
            <div>
                <button type="button" class="button button-primary"
                        id="<?php echo OPTINLY_SLUG ?>_validate_app_id_btn"><?php ($is_app_connected == 1) ? _e('Re-Connect', OPTINLY_TEXT_DOMAIN) : _e('Connect', OPTINLY_TEXT_DOMAIN); ?></button>
                <button type="button" class="button"
                        id="<?php echo OPTINLY_SLUG ?>_disconnect_app_btn"
                        style="<?php echo ($is_app_connected == 0) ? 'display:none' : ''; ?> "><?php _e('Dis-Connect', OPTINLY_TEXT_DOMAIN); ?></button>
                <div id="optinly-loader-container">
                    <div class="optinly-load-spinner"></div>
                </div>
                <div id="optinly-api-response-container" class="optinly-api-response-container">
                    <?php
                    if (($is_app_connected == 1)) {
                        ?>
                        <span style="color: green"><?php _e('App successfully connected to Optinly!'); ?></span>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>