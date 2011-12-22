<?php
//
/* begin plugin settings */
//

add_action('admin_init', 'cove_api_options_init' );
add_action('admin_menu', 'cove_api_options_add_page');

/* Init plugin options to white list our options */
function cove_api_options_init(){
	register_setting( 'cove_api_options', 'cove_api_settings' );
}

// Add menu page
function cove_api_options_add_page() {

    if (current_user_can('manage_options')) {
	add_options_page('COVE API Options', 'COVE API Options', 'manage_options', 'cove_api-options', 'cove_api_options_page');
    }

}

// Draw the menu page itself

function cove_api_options_page() {

?>

	<div class="wrap">
		<h2>COVE API Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('cove_api_options'); ?>
			<?php $options = get_option('cove_api_settings'); ?>
                <fieldset>
                <table id="cove-api-options">
                <tr>
                <td class="content">
                <div class="options-liquid-left" style="clear:left; float:left; margin-right:-425px; width:100%;">
                <div class="options-left" style="margin-left:5px; margin-right:425px;">

			<table width="100%" class="form-table">
				<tr valign="top"><th scope="row">API Key</th>
					<td><input name="cove_api_settings[api_id]" type="text" size="60" value="<?php echo $options['api_id']; ?>" />
					<p>Your assigned COVE API key.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">API Secret</th>
					<td><input name="cove_api_settings[api_secret]" type="text" size="60" value="<?php echo $options['api_secret']; ?>" />
					<p>Your assigned COVE API secret.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Cache TTL</th>
					<td><input name="cove_api_settings[cache_ttl]" type="text" size="6" value="<?php echo $options['cache_ttl']; ?>" />
					<p>Time (in seconds) that an API request remains valid. 600 recomended.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Default Producer</th>
					<td><input name="cove_api_settings[default_producer]" type="text" size="10" value="<?php echo $options['default_producer']; ?>" />
					<p>Producer to use by default (if any) when requesting programs.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Default Program ID</th>
					<td><input name="cove_api_settings[default_program]" type="text" size="10" value="<?php echo $options['default_program']; ?>" />
					<p>The COVE API Program ID to use by default when requesting videos.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Show Dashboard Widget?</th>
				<td>
				<?php $showdashoptions = array('No', 'Yes'); ?>
				<?php foreach ($showdashoptions as $i => $showopt): ?>
					<input type="radio" name="cove_api_settings[show_dashboard_widget]" value="<?php echo $showopt; ?>" <?php echo (($options['show_dashboard_widget'] == $showopt) ? 'checked="checked"' : ''); ?>/> <?php echo $showopt; ?> &nbsp;&nbsp;
				<?php endforeach; ?>
                                        <p>Show a list of the latest cove videos for the default program on the WP Admin Dashboard.</p>
				</td>
				</tr>


			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
                </div>
                </div>
                <div class="options-liquid-right" style="clear:right; float:right; width:400px;">
                <div class="options-right" style="margin: 0 auto; width: 385px;">

<?php cove_default_producer_display(); ?>
                </div>
                </div>
		</form>
                </td>
        </tr>
        </table>
        </fieldset>

	</div>
<?php

}

?>
