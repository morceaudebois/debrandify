<?php if (!defined('ABSPATH')) { exit; }

function wpdbrd_getDefaultOptions() {
    $dwpifyDefaults = array(

        // general tab
        'prioritise' => 'no',
        'adminbar_logo' => 'yes',
        'thank_you' => 'yes',
        'thank_you_string' => '',
        'footer_version' => 'yes',
        'footer_version_string' => '',
        'login_logo' => 'wp_logo',
        'wordpress-tab-suffix' => 'yes',
        'dashboard_news' => 'yes',
        'elementor_overview' => 'yes',
        'smileys' => 'yes',
        'rss' => 'yes',
        'comments' => 'yes',

        // email tab
        'email_from' => '',
        'email_username' => '',

        // advanced tab
        'css' => 'yes',
        'head' => 'yes',
        'wp_embed' => 'yes',
        'block_library' => 'yes',
        'wp_themes' => 'yes',

        // bonus tab
        'svg' => 'no',
        'centerLogin' => 'no',
        'restAPI' => 'yes',
        'jquery' => 'yes',

        // state of banners
        'installDate' => false,
        'installBanner' => false,
        'usedNotice' => false,
    );

    // add wpdbrd_ prefix to all keys
    $dwpifyDefaults = array_combine(
        array_map(function($k) { return 'wpdbrd_' . $k; },
        array_keys($dwpifyDefaults)), $dwpifyDefaults
    );

    return $dwpifyDefaults;
}

function nw() { return is_network_admin(); }

function wpdbrd_updateOption($key, $value) {
    if (nw()) { return update_site_option($key, $value);
    } else { return update_option($key, $value); }
}

function wpdbrd_getOption($key) {
    if (nw()) { return get_site_option($key);
    } else { return get_option($key); }
}

function wpdbrd_is_login_form() {
    $ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
    return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files())) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF']== '/wp-login.php');
}

function wpdbrd_tabsUrl() {
    $wpdbrd_tabsUrl = '.php?page=wp-debrand';
    if (nw()) { return network_admin_url('settings' . $wpdbrd_tabsUrl);
    } else { return admin_url('admin' . $wpdbrd_tabsUrl); }
}
function wpdbrd_getCurrentTab() {
    return !isset($_GET['action']) ? 'general' : $_GET['action'];
}

function wpdbrd_checkOption($key, $string = false) {
    $key = 'wpdbrd_' . $key;

    // checks if per network option has priority
    if (is_multisite() && get_option('wpdbrd_prioritise') == 'no') {
        if (!$string) return get_site_option($key) == 'yes' ? true : false;
        return get_site_option($key); // when string output (select)
    } 
    
    if (!$string) return get_option($key) == 'yes' ? true : false; // else just returns normal option
    return get_option($key); // when string output
}