<?php


/**
 * Add our sub-menu
 *
 * @author Zane Matthew
 * @since 0.1-alpha
 */
function prtp_admin_menu(){
    add_submenu_page( 'options-general.php', 'prtp', 'Plugin Readme to Post', 'activate_plugins', 'plugin-readme-to-post-settings', 'prtp_settings' );
}
add_action('admin_menu','prtp_admin_menu');


/**
 * Create our settings page and save on $_POST
 *
 * @author Zane Matthew
 * @since 0.1-alpha
 */
function prtp_settings(){
    $options = get_option('prtp_settings');

    if ( ! empty( $_POST['prtp_settings']['save'] ) ){
        if ( ! empty( $_POST['prtp_settings']['tabs'] ) )
            $tabs = $_POST['prtp_settings']['tabs'];
        else
            $tabs = 0;
        update_option( 'prtp_settings', $_POST['prtp_settings'] );
    }

    if ( empty( $options['tabs'] ) ){
        $tabs = 0;
    } else {
        $tabs = 1;
    }?>
    <div class="wrap">
        <h2>Plugin Readme to Post Settings</h2>
        <form action="options-general.php?page=events-venues-settings" method="POST">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Tabs</th>
                        <td>
                            <input type="checkbox" name="prtp_settings[tabs]" value="1" <?php checked( $tabs, 1 ); ?> id="tabs" /> <label for="tabs">Use jQuery UI Tabs</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="prtp_settings[save]" id="submit" class="button button-primary" value="Save Changes">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
<?php }