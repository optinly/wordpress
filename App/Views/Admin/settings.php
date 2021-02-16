<?php
defined('ABSPATH') or die;
/**
 * @var $is_mailpoet_enabled string
 * @var $app_secret_key string
 * @var $mailpoet_webhook string
 * @var $mailpoet_list_id array
 * @var $mailpoet_lists array
 */
?>
<form id="optinly-settings-form">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row" class="app_id">
                <label for="<?php echo OPTINLY_SLUG ?>app_secret_key"><?php _e('App secret key', OPTINLY_TEXT_DOMAIN); ?></label>
            </th>
            <td class="forminp forminp-text">
                <input type="text" name="app_secret_key" class="regular-text"
                       id="<?php echo OPTINLY_SLUG ?>app_secret_key"
                       placeholder="Enter secret key" readonly
                       value="<?php echo $app_secret_key ?>">
                <p>
                    You need to enter the above secret key in your optinly dashboard
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" class="app_id">
                <label for="<?php echo OPTINLY_SLUG ?>is_mailpoet_enabled"><?php _e('Enable mailpoet integration', OPTINLY_TEXT_DOMAIN); ?></label>
            </th>
            <td class="forminp forminp-text">
                <label><input type="radio" name="is_mailpoet_enabled" id="<?php echo OPTINLY_SLUG ?>is_mailpoet_enabled"
                              class="regular-text" <?php echo ($is_mailpoet_enabled == "yes") ? "checked" : "" ?>
                              value="yes">Yes</label>
                <label><input type="radio" name="is_mailpoet_enabled" id="<?php echo OPTINLY_SLUG ?>is_mailpoet_enabled"
                              class="regular-text" <?php echo ($is_mailpoet_enabled == "no") ? "checked" : "" ?>
                              value="no">No</label>
                <div id="mailpoet_settings_container">
                    <p>
                        To get the secret key you need to copy paste the below URL
                    </p>
                    <p><?php echo $mailpoet_webhook; ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row" class="app_id">
                <label for="<?php echo OPTINLY_SLUG ?>mailpoet_list_id"><?php _e('Mailpoet subscription list', OPTINLY_TEXT_DOMAIN); ?></label>
            </th>
            <td class="forminp forminp-text">
                <select name="mailpoet_list_id[]" id="<?php echo OPTINLY_SLUG ?>mailpoet_list_id" multiple>
                    <?php
                    if (!empty($mailpoet_lists)) {
                        foreach ($mailpoet_lists as $list) {
                            ?>
                            <option value="<?php echo $list['id'] ?>" <?php echo (in_array($list['id'], $mailpoet_list_id)) ? "selected" : "" ?>><?php echo $list['name'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="hidden" name="action" value="save_optinly_settings">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(OPTINLY_SLUG . '_save_settings') ?>">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
</form>