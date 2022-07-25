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
        'dwpify_installDate' => false,
        'dwpify_installBanner' => false,
        'dwpify_usedNotice' => false,

        'dwpify_prioritise' => 'no',
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

        'dwpify_email_from' => '',
        'dwpify_email_username' => '',

        'dwpify_css' => 'yes',
        'dwpify_head' => 'yes',
        'dwpify_wp_embed' => 'yes',
        'dwpify_block_library' => 'yes',
        'dwpify_wp_themes' => 'yes',
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