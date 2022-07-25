<?php 

function replaceableString($section, $input, $string) {
    $options = get_option('dwpify_' . $section);

    if (!empty($options[$input])) { // if checked
        if ($options[$string]) return $options[$string]; // return value if exists
    } else { return false; } // false if unchecked
}

function nw() { return is_network_admin(); }

function updateOption($key, $value) {
    if (nw()) { update_site_option($key, $value);
    } else { return update_option($key, $value); }
}

function getOption($key) {
    if (nw()) { return get_site_option($key);
    } else { return get_option($key); }
}

function tabsUrl() {
    $tabsUrl = '.php?page=dewordpressify';
    if (nw()) { return network_admin_url('settings' . $tabsUrl);
    } else { return admin_url('admin' . $tabsUrl); }
}
function getCurrentTab() {
    return !isset($_GET['action']) ? 'general' : $_GET['action'];
}

function getDefaultOptions() {
    $dwpifyDefaults = array(
        'installDate' => false,
        'installBanner' => false,
        'usedNotice' => false,

        'prioritise' => 'no',
        'dwpify_adminbar_logo' => 'yes',
        'dwpify_thank_you' => 'yes',
        'dwpify_thank_you_string' => '',
        'dwpify_footer_version' => 'yes',
        'dwpify_footer_version_string' => '',
        'dwpify_login_logo' => 'wp_logo',
        'dwpify_dashboard_news' => 'yes',
        'dwpify_smileys' => 'yes',
        'dwpify_rss' => 'yes',
        'dwpify_comments' => 'yes',
        'dwpify_svg' => 'no',

        'dwpify_css' => 'yes',
        'dwpify_head' => 'yes',
        'dwpify_wp_embed' => 'yes',
        'dwpify_block_library' => 'yes',
        'dwpify_wp_themes' => 'yes',
    );

    return $dwpifyDefaults;
}