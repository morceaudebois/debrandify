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