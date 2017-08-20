<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>

<?php if (!empty($_POST)) { ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('Changes saved!', $this->slug); ?></p>
    </div>

<?php } ?>

    <form method="post" action="<?php echo admin_url('options-general.php?page=' . $this->slug); ?>">
        <?php wp_nonce_field('bsas_update_options', '_wpnonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">Activate Suspension</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span>Suspend Account</span></legend>
                        <label for="activated">
                            <input name="activated" type="checkbox" id="activated" value="<?php echo $this->settings['activated']; ?>"<?php checked(isset($this->settings['activated']) && $this->settings['activated']); ?> />
                            Enabled
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row">Bypass Search Bots</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span>Bypass Search Bots</span></legend>
                        <label for="bypass_search_bots">
                            <input name="bypass_search_bots" type="checkbox" id="bypass_search_bots" value="<?php echo $this->settings['bypass_search_bots']; ?>" aria-describedby="bypass-description"<?php checked(isset($this->settings['bypass_search_bots']) && $this->settings['bypass_search_bots']); ?> />
                            Enabled
                        </label>
                        <p class="description" id="bypass-description">This will have a severe impact on SEO ranking if disabled.</p>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="notice">Suspension Notice</label>
                </th>
                <td>
                    <input name="notice" type="text" id="notice" value="<?php echo $this->settings['notice']; ?>" class="regular-text" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="http_status">HTTP Status Code</label>
                </th>
                <td>
                    <select name="http_status" id="http_status">
                        <optgroup label="2xx Success">
                            <option value="200"<?php selected($this->settings['http_status'], 200); ?>>200 OK</option>
                        </optgroup>

                        <optgroup label="4xx Client Error">
                            <option value="401"<?php selected($this->settings['http_status'], 401); ?>>401 Unauthorized</option>
                            <option value="402"<?php selected($this->settings['http_status'], 402); ?>>402 Payment Required</option>
                            <option value="403"<?php selected($this->settings['http_status'], 403); ?>>403 Forbidden</option>
                            <option value="451"<?php selected($this->settings['http_status'], 451); ?>>451 Unavailable For Legal Reasons</option>
                        </optgroup>

                        <optgroup label="5xx Server Error">
                            <option value="500"<?php selected($this->settings['http_status'], 500); ?>>500 Internal Server Error</option>
                            <option value="503"<?php selected($this->settings['http_status'], 503); ?>>503 Service Unavailable</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
