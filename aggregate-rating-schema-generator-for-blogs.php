<?php
/*
Plugin Name: Aggregate Rating Schema Generator for Blogs
Description: Enhances blog posts with user review and rating inputs while optimizing aggregate rating using Schema markup for better SEO performance.
Version: 1.9.3
Author: Najmus Sayadat
Donate link: https://www.buymeacoffee.com/7yborg
Author URI: https://intentfarm.com/
Website: https://www.linkedin.com/in/najmus-sayadat/
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: aggregate-rating-schema-generator-for-blogs

*/

// Ensure code is only executed in WordPress environment
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the dashboard file
require_once plugin_dir_path(__FILE__) . 'dashboard.php';

// Create table for storing ratings
function strpgn_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'star_ratings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        rating tinyint(1) NOT NULL,
        rated tinyint(1) NOT NULL DEFAULT 0,
        ip_address varchar(45) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Set default settings
function strpgn_set_default_settings() {
    if (get_option('strpgn_apply_to') === false) {
        update_option('strpgn_apply_to', 'posts'); // Set default to 'posts'
    }
}
register_activation_hook(__FILE__, 'strpgn_create_table');
register_activation_hook(__FILE__, 'strpgn_set_default_settings');

// Enqueue scripts and styles
function strpgn_enqueue_scripts() {
    // Get the apply-to setting
    $apply_to = get_option('strpgn_apply_to');

    // Check if current post type matches the setting
    if (
        ($apply_to === 'posts' && is_single()) ||
        ($apply_to === 'pages' && is_page()) ||
        ($apply_to === 'both' && (is_single() || is_page()))
    ) {
        wp_enqueue_script('jquery');

        // Define the version for scripts and styles
        $version = '1.9.3'; // Updated to match plugin version

        // Enqueue confetti library with its version to avoid caching issues
        wp_enqueue_script(
            'strpgn-confetti',
            plugins_url('/js/confetti.browser.min.js', __FILE__),
            array('jquery'),
            '1.4.0',
            true
        );

        // Enqueue the rating script and style with versioning
        wp_enqueue_style('strpgn-rating-style', plugins_url('/css/style.css', __FILE__), array(), $version);
        wp_enqueue_script('strpgn-rating-script', plugins_url('/js/rating.js', __FILE__), array('jquery'), $version, true);
        
        // Localize the script for AJAX
        $ajax_nonce = wp_create_nonce('strpgn_rating_nonce');
        wp_localize_script('strpgn-rating-script', 'strpgn_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => $ajax_nonce
        ));
    }
}
add_action('wp_enqueue_scripts', 'strpgn_enqueue_scripts');

// Add star rating to the content (for user interaction)
function strpgn_add_star_rating($content) {
    if (is_single() || is_page()) {
        // Get the apply-to setting
        $apply_to = get_option('strpgn_apply_to');

        // Check if current post type matches the setting
        if (($apply_to === 'posts' && is_single()) ||
            ($apply_to === 'pages' && is_page()) ||
            $apply_to === 'both') {

            global $wpdb;
            $post_id = get_the_ID();

            // Cache the rating count
            $rating_count = wp_cache_get('strpgn_rating_count_' . $post_id);

            if ($rating_count === false) {
                $rating_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND rated = 1", $post_id
                ));
                wp_cache_set('strpgn_rating_count_' . $post_id, $rating_count, '', 12 * HOUR_IN_SECONDS);
            }

            if ($rating_count == 0) {
                $content .= '<div class="strpgn-star-rating" data-post-id="' . esc_attr($post_id) . '">'; // Changed class to 'strpgn-star-rating'
                for ($i = 1; $i <= 5; $i++) {
                    $content .= '<span class="strpgn-star" data-value="' . esc_attr($i) . '" data-post-id="' . esc_attr($post_id) . '">★</span>'; // Changed class to 'strpgn-star'
                }
                $content .= '</div>';
            }
        }
    }
    return $content;
}
add_filter('the_content', 'strpgn_add_star_rating');

// Display average rating and schema markup (only if there are ratings)
function strpgn_display_average_rating($content) {
    if (is_single() || is_page()) {
        // Get the apply-to setting
        $apply_to = get_option('strpgn_apply_to');

        // Check if current post type matches the setting
        if (($apply_to === 'posts' && is_single()) ||
            ($apply_to === 'pages' && is_page()) ||
            $apply_to === 'both') {

            global $wpdb;
            $post_id = get_the_ID();

            $cache_key = 'strpgn_average_rating_' . $post_id;
            $cached_data = wp_cache_get($cache_key);

            if ($cached_data === false) {
                $average_rating = round($wpdb->get_var($wpdb->prepare(
                    "SELECT AVG(rating) FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND rated = 1", $post_id
                )), 1);

                $rating_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND rated = 1", $post_id
                ));

                wp_cache_set($cache_key, array('average' => $average_rating, 'count' => $rating_count), '', 12 * HOUR_IN_SECONDS);
            } else {
                $average_rating = $cached_data['average'];
                $rating_count = $cached_data['count'];
            }

            if ($average_rating !== null && $rating_count !== null) {
                $output = '<div class="strpgn-rating-container" data-post-id="' . esc_attr($post_id) . '">'; // Changed class to 'strpgn-rating-container'
                // $output .= '<div id="thank-you-message" style="display:none;">THANK YOU FOR RATING!! 😊</div>'; // Ensured it's initially hidden
                $output .= '<div class="strpgn-rating-content">';
                $output .= '<div class="strpgn-average-rating"><span class="strpgn-average-number">' . esc_html($average_rating) . '</span></div>';
                $output .= '<div class="strpgn-rating-details"><div class="strpgn-stars">'; // Changed class to 'strpgn-stars'

                for ($i = 1; $i <= 5; $i++) {
                    $filled = ($i <= $average_rating) ? ' strpgn-star-filled' : ' strpgn-star-empty'; // Changed class to 'strpgn-star-filled' and 'strpgn-star-empty'
                    $output .= '<span class="strpgn-star' . esc_attr($filled) . '" data-value="' . esc_attr($i) . '">★</span>'; // Changed class to 'strpgn-star'
                }

                $output .= '</div><div class="strpgn-total-ratings">Based on ' . esc_html($rating_count) . ' ratings</div></div></div></div>';

                $schema = '<script type="application/ld+json">{
                    "@context": "https://schema.org/",
                    "@type": "CreativeWorkSeries",
                    "name": "' . esc_js(get_the_title()) . '",
                    "aggregateRating": {
                        "@type": "AggregateRating",
                        "ratingValue": "' . esc_js($average_rating) . '",
                        "bestRating": "5",
                        "ratingCount": "' . esc_js($rating_count) . '"
                    }
                }</script>';

                $content .= $schema . $output;
            }
        }
    }

    return $content;
}
add_filter('the_content', 'strpgn_display_average_rating');

// Save star rating
function strpgn_save_star_rating() {
    global $wpdb;

    check_ajax_referer('strpgn_rating_nonce', 'nonce'); // Updated nonce name

    $post_id   = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $rating    = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    // Validate and sanitize IP address
    $ip_address = '';
    if ( isset($_SERVER['REMOTE_ADDR']) ) {
        $ip_address = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }

    if ($post_id <= 0 || $rating <= 0 || $rating > 5) {
        wp_send_json_error('Invalid rating or post ID');
        return;
    }

    // Implement caching for existing rating check
    $cache_key = 'strpgn_existing_rating_' . $post_id . '_' . md5($ip_address);
    $existing_rating = wp_cache_get($cache_key);

    if ($existing_rating === false) {
        $existing_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND ip_address = %s",
            $post_id,
            $ip_address
        ));
        wp_cache_set($cache_key, $existing_rating, '', 12 * HOUR_IN_SECONDS);
    }

    if ($existing_rating) {
        wp_send_json_error('You have already rated this post 🙂');
        return;
    }

    $result = $wpdb->insert(
        $wpdb->prefix . 'star_ratings',
        array(
            'post_id'    => $post_id,
            'rating'     => $rating,
            'rated'      => 1,
            'ip_address' => $ip_address
        ),
        array('%d', '%d', '%d', '%s')
    );

    if ($result) {
        // Clear the cache for this post’s rating after submission to ensure fresh data is loaded
        wp_cache_delete('strpgn_average_rating_' . $post_id);
        wp_send_json_success();
    } else {
        wp_send_json_error('Database insertion failed');
    }
}
add_action('wp_ajax_save_star_rating', 'strpgn_save_star_rating');
add_action('wp_ajax_nopriv_save_star_rating', 'strpgn_save_star_rating');

// Get average rating
function strpgn_get_average_rating() {
    global $wpdb;

    // Verify nonce
    check_ajax_referer('strpgn_rating_nonce', 'nonce'); // Updated nonce name

    if (!isset($_POST['post_id'])) {
        wp_send_json_error('Invalid data');
        return;
    }

    $post_id = intval($_POST['post_id']);

    $cache_key = 'strpgn_average_rating_' . $post_id;
    $cached_data = wp_cache_get($cache_key);

    if ($cached_data === false) {
        $average_rating = round($wpdb->get_var($wpdb->prepare(
            "SELECT AVG(rating) FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND rated = 1", $post_id
        )), 1);

        $rating_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}star_ratings WHERE post_id = %d AND rated = 1", $post_id
        ));

        // Cache the rating count and average
        wp_cache_set($cache_key, array('average' => $average_rating, 'count' => $rating_count), '', 12 * HOUR_IN_SECONDS);
    } else {
        $average_rating = $cached_data['average'];
        $rating_count = $cached_data['count'];
    }

    wp_send_json_success(array('average_rating' => $average_rating, 'rating_count' => $rating_count));
}
add_action('wp_ajax_get_average_rating', 'strpgn_get_average_rating');
add_action('wp_ajax_nopriv_get_average_rating', 'strpgn_get_average_rating');
// Add custom links (Support and Donate) to the plugin on the plugins page
function strpgn_plugin_links($links, $file) {
    if (strpos($file, 'aggregate-rating-schema-generator-for-blogs.php') !== false) {
        $new_links = array(
            '<a href="https://infoverse.org.in/drop-a-message/" target="_blank">' . __('Support') . '</a>',
            '<a href="https://www.buymeacoffee.com/7yborg" target="_blank">' . __('Buy a Coffee') . '</a>'
        );
        $links = array_merge($links, $new_links);
    }
    return $links;
}
add_filter('plugin_row_meta', 'strpgn_plugin_links', 10, 2);
