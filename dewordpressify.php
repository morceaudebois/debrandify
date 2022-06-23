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


add_action('admin_menu', 'dewordpressify_admin_menu');
add_action('admin_init', 'dewordpressify_settings_init');
add_action('admin_init', 'menu_edits');

function dewordpressify_admin_menu() {
    add_options_page('DeWordPressify', 'DeWordPressify', 'manage_options', 'dewordpressify', 'options_page');
}

function getSectionDescription() {
    echo __('This Section Description', 'wordpress');
}

function dewordpressify_settings_init() {
    register_setting('dewordpressify', 'dewordpressify_settings');

    add_settings_section(
        'dewordpressify_section',
        __('Our Section Title', 'wordpress'),
        'getSectionDescription',
        'dewordpressify'
    );

    // add_settings_field(
    //     'text_input',
    //     __('Text input title', 'wordpress'),
    //     'text_input_render',
    //     'dewordpressify',
    //     'dewordpressify_section'
    // );

    add_settings_field(
        'thank_you',
        __('Hide thank you sentence in admin footer', 'wordpress'),
        'thank_you',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'footer_version',
        __('Hide WordPress version number in footer', 'wordpress'),
        'footer_version',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'adminbar_logo',
        __('Hide WordPress admin bar logo', 'wordpress'),
        'adminbar_logo',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'dashboard_news',
        __('Disable news and events widget in dashboard', 'wordpress'),
        'dashboard_news',
        'dewordpressify',
        'dewordpressify_section'
    );

}



function text_input_render() {
    $options = get_option('dewordpressify_settings'); ?>

    <input type='text' name='dewordpressify_settings[text_input]' value='<?php echo $options['text_input'] ?>'>
<?php }

function thank_you() {
    $options = get_option('dewordpressify_settings'); 
    $checked = $options['thank_you'] ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[thank_you]'>
<?php }


function footer_version() {
    $options = get_option('dewordpressify_settings'); 
    $checked = $options['footer_version'] ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[footer_version]'>
<?php }


function adminbar_logo() {
    $options = get_option('dewordpressify_settings'); 
    $checked = $options['adminbar_logo'] ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[adminbar_logo]'>
<?php }

function dashboard_news() {
    $options = get_option('dewordpressify_settings'); 
    $checked = $options['dashboard_news'] ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[dashboard_news]'>
<?php }




function options_page() { ?>
    <form action='options.php' method='post'>

        <h2>DeWordPressify</h2>

        <?php
            settings_fields('dewordpressify');
            do_settings_sections('dewordpressify');
            
            submit_button();
        ?>

    </form>
<?php }

function menu_edits() {
    $options = get_option('dewordpressify_settings');

    if ($options['thank_you']) {
        // Admin footer modification
        function remove_footer_admin () {
            echo '';
        }
        
        add_filter('admin_footer_text', 'remove_footer_admin');
    }


    if ($options['footer_version']) {
        function remove_version() {
            remove_filter( 'update_footer', 'core_update_footer' );
        }
        
        add_action( 'admin_footer_text', 'remove_version' );
    }

    if ($options['adminbar_logo']) {
        function example_admin_bar_remove_logo() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu( 'wp-logo' );
        }
        add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );
    }

    if ($options['dashboard_news']) {
        function rm_dahsboardnews() {
            remove_meta_box( 'dashboard_primary', get_current_screen(), 'side' );
        }
        add_action( 'wp_network_dashboard_setup', 'rm_dahsboardnews', 20 );
        add_action( 'wp_user_dashboard_setup',    'rm_dahsboardnews', 20 );
        add_action( 'wp_dashboard_setup',         'rm_dahsboardnews', 20 );
    }
}