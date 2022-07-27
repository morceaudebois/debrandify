<?php 

function nw() { return is_network_admin(); }

function updateOption($key, $value) {
    if (nw()) { return update_site_option($key, $value);
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
        'adminbar_logo' => 'yes',
        'thank_you' => 'yes',
        'thank_you_string' => '',
        'footer_version' => 'yes',
        'footer_version_string' => '',
        'login_logo' => 'wp_logo',
        'dashboard_news' => 'yes',
        'smileys' => 'yes',
        'rss' => 'yes',
        'comments' => 'yes',

        'email_from' => '',
        'email_username' => '',

        'css' => 'yes',
        'head' => 'yes',
        'wp_embed' => 'yes',
        'block_library' => 'yes',
        'wp_themes' => 'yes',

        'svg' => 'no',
        'centerLogin' => 'no',
    );

    // add dwpify_ prefix to all keys
    $dwpifyDefaults = array_combine(
        array_map(function($k) { return 'dwpify_' . $k; },
        array_keys($dwpifyDefaults)), $dwpifyDefaults
    );

    return $dwpifyDefaults;
}

function checkOption($key, $string = false) {
    $key = 'dwpify_' . $key;

    // checks if per network option has priority
    if (is_multisite() && get_option('dwpify_prioritise') == 'no') {
        if (!$string) return get_site_option($key) == 'yes' ? true : false;
        return get_site_option($key); // when string output (select)
    } 
    
    if (!$string) return get_option($key) == 'yes' ? true : false; // else just returns normal option
    return get_option($key); // when string output
}