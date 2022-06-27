<?php 

function replaceableString($section, $input, $string) {
    $options = get_option('dwpify_' . $section);
    
    if (!empty($options[$input])) { return ''; } // if checked, hidden
    else if (!empty($options[$string])) {return $options[$string]; }
}