<?php
/**
 * @package DeWordPressify
 * @version 1.0
 */
/*
Plugin Name: DeWordPressify
Plugin URI: https://tahoe.be
Description: DeWordPressify lets you remove WordPress' branding and replace it with your own!
Version: 1.0
Author: Tahoe Beetschen
Author URI: https://tahoe.be
License: GPL2
*/

include(plugin_dir_path(__FILE__) . 'functions.php');
include(plugin_dir_path(__FILE__) . 'settings.php');

add_action('admin_init', 'wp_admin'); // triggers in wp-admin
add_action('login_init', 'loginPage'); // triggers on login page

function loggedInAction() {
    if (is_user_logged_in()) {
        user_logged_in();
    }
}
add_action('init', 'loggedInAction'); // triggers when user logged in


function wp_admin() {
    $options = get_option('dewordpressify_settings');

    if (isset($options['thank_you'])) {

        function thankYouText() {
            return '';
        }

        add_filter('admin_footer_text', 'thankYouText', 11);
    }

    if (isset($options['footer_version'])) {

        function versionText() {
            return '';
        }

        add_filter('update_footer', 'versionText', 11);
    }

    if (isset($options['dashboard_news'])) {
        function rm_dahsboardnews() {
            remove_meta_box('dashboard_primary', get_current_screen(), 'side');
        }
        add_action('wp_network_dashboard_setup', 'rm_dahsboardnews', 20);
        add_action('wp_user_dashboard_setup',    'rm_dahsboardnews', 20);
        add_action('wp_dashboard_setup',         'rm_dahsboardnews', 20);
    }
}

function user_logged_in() {
    $options = get_option('dewordpressify_settings');
    
    if (isset($options['adminbar_logo'])) {
        function admin_bar_remove_logo() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }
    
        add_action('wp_before_admin_bar_render', 'admin_bar_remove_logo', 0);
    }
}

function loginPage() {
    $options = get_option('dewordpressify_settings');
    
    if (isset($options['login_logo'])) {
        function remove_logo() { ?>
            <style type="text/css">

                .login h1 a { display: none }

                /* better centered login form */
                @media screen and (min-height: 550px) {
                    body {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        flex-direction: column;
                    }

                    #login {
                        padding: 20px 0;
                        margin: unset;
                    }

                    .login form {
                        margin-top: unset;
                    }
                }

            </style>
        <?php }
            
        add_action('login_head', 'remove_logo');
    }
}