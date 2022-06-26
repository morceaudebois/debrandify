<?php 

function replaceableString($field, $string) {
    $options = get_option('dewordpressify_settings'); // needs to be redeclared for some reason
    
    if (isset($options[$field])) { return ''; }
    else {
        if (!empty($options[$string])) {
            // request is needed again for some reason?
            return get_option('dewordpressify_settings')[$string];
        } else {
            return $defaultString;
        }
    }
}

function getCheckedValue($value) {
    $options = get_option('dewordpressify_settings');
    return isset($options[$value]) ? 'checked' : '';
}

function getInputString($value) {
    $options = get_option('dewordpressify_settings');
    return $options[$value];
}