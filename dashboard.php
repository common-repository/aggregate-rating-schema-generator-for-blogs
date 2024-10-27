<?php
// Register settings
function strpgn_register_settings() {
    register_setting(
        'strpgn_settings_group', // Option group
        'strpgn_apply_to',       // Option name
        'strpgn_sanitize_settings' // Sanitization callback
    );
}
add_action('admin_init', 'strpgn_register_settings');

// Sanitization callback function
function strpgn_sanitize_settings($input) {
    // Define allowed values
    $allowed_values = array('posts', 'pages', 'both');

    // Check if the input is one of the allowed values
    if (in_array($input, $allowed_values, true)) {
        return $input;
    } else {
        // Return a default value or handle the error as needed
        return 'posts';
    }
}

// Add settings page
function strpgn_add_settings_page() {
    add_options_page(
        'Star Rating Settings',
        'Star Rating',
        'manage_options',
        'strpgn-settings',
        'strpgn_render_settings_page'
    );
}
add_action('admin_menu', 'strpgn_add_settings_page');

// Render settings page
function strpgn_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Star Rating Settings', 'aggregate-rating-schema-generator-for-blogs' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('strpgn_settings_group');
            do_settings_sections('strpgn_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Apply to:', 'aggregate-rating-schema-generator-for-blogs' ); ?></th>
                    <td>
                        <select name="strpgn_apply_to">
                            <option value="posts" <?php selected(get_option('strpgn_apply_to'), 'posts'); ?>>
                                <?php esc_html_e( 'Posts Only', 'aggregate-rating-schema-generator-for-blogs' ); ?>
                            </option>
                            <option value="pages" <?php selected(get_option('strpgn_apply_to'), 'pages'); ?>>
                                <?php esc_html_e( 'Pages Only', 'aggregate-rating-schema-generator-for-blogs' ); ?>
                            </option>
                            <option value="both" <?php selected(get_option('strpgn_apply_to'), 'both'); ?>>
                                <?php esc_html_e( 'Both Posts and Pages', 'aggregate-rating-schema-generator-for-blogs' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>
