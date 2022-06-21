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

function dewordpressify_admin_menu() {
    add_options_page('DeWordPressify', 'DeWordPressify', 'manage_options', 'dewordpressify', 'options_page');
}

function dewordpressify_settings_init() {
    register_setting('dewordpressify', 'dewordpressify_settings');

    add_settings_section(
        'dewordpressify_section',
        __('Our Section Title', 'wordpress'),
        'getSectionDescription',
        'dewordpressify'
    );

    add_settings_field(
        'text_input',
        __('Text input title', 'wordpress'),
        'text_input_render',
        'dewordpressify',
        'dewordpressify_section'
    );

	add_settings_field(
        'checkbox',
        'A Checkbox',
        'checkbox_render',
        'dewordpressify',
        'dewordpressify_section');

}

function getSectionDescription() {
    echo __('This Section Description', 'wordpress');
}

function text_input_render() {
    $options = get_option('dewordpressify_settings'); ?>

    <input type='text' name='dewordpressify_settings[text_input]' value='<?php echo $options['text_input']; ?>'>
<?php }


function checkbox_render() {
	$options = get_option('dewordpressify_settings');

	if($options['chkbox2']) { $checked = ' checked="checked" '; }

	echo "<input {$checked} id='checkbox' name='dewordpressify_settings[chkbox2]' type='checkbox' />";
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
