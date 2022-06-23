<?php 


function dewordpressify_admin_menu() {
    add_options_page('DeWordPressify', 'DeWordPressify', 'manage_options', 'dewordpressify', 'options_page');
}

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

    add_settings_field(
        'login_logo',
        __('Remove logo on login page', 'wordpress'),
        'login_logo',
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
    $checked = isset($options['thank_you']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[thank_you]'>
<?php }

function footer_version() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['footer_version']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[footer_version]'>
<?php }


function adminbar_logo() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['adminbar_logo']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[adminbar_logo]'>
<?php }

function dashboard_news() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['dashboard_news']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[dashboard_news]'>
<?php }

function login_logo() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['login_logo']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[login_logo]'>
<?php }


add_action('admin_menu', 'dewordpressify_admin_menu');
add_action('admin_init', 'dewordpressify_settings_init');