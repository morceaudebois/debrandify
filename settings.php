<?php 


function dewordpressify_admin_menu() {
    add_options_page('DeWordPressify', 'DeWordPressify', 'manage_options', 'dewordpressify', 'options_page');
}

// adds script.js to settings page
add_action('admin_enqueue_scripts', 'enqueue_script');
function enqueue_script($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_dewordpressify') return;

    $handle = 'dewordpressify';
    wp_register_script($handle, plugin_dir_url( __FILE__ ) . '/script.js');
    wp_enqueue_script($handle);
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

    add_settings_field(
        'emojis',
        __('Remove integrated emojis', 'wordpress'),
        'emojis',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'rss',
        __('Remove integrated RSS feed', 'wordpress'),
        'rss',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'comments',
        __('Disable comments', 'wordpress'),
        'comments',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'css',
        __('Disable global inline styles', 'wordpress'),
        'css',
        'dewordpressify',
        'dewordpressify_section'
    );

    add_settings_field(
        'head',
        __('Remove unnecessary code in <head>', 'wordpress'),
        'head',
        'dewordpressify',
        'dewordpressify_section'
    );
}



function thank_you() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['thank_you']) ? 'checked' : ''; ?>

 
    <input <?php echo $checked ?> type="checkbox" id="dewordpressify_settings[thank_you]" name="dewordpressify_settings[thank_you]">
    
    <input type='text' placeholder='Set your own phrase' name='dewordpressify_settings[text_input]' value='<?php echo $options['text_input'] ?>'>
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

function emojis() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['emojis']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[emojis]'>
<?php }

function rss() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['rss']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[rss]'>
<?php }

function comments() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['comments']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[comments]'>
<?php }

function css() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['css']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[css]'>
<?php }

function head() {
    $options = get_option('dewordpressify_settings'); 
    $checked = isset($options['head']) ? 'checked' : ''; ?>

    <input <?php echo $checked ?> type='checkbox' name='dewordpressify_settings[head]'>
<?php }


add_action('admin_menu', 'dewordpressify_admin_menu');
add_action('admin_init', 'dewordpressify_settings_init');