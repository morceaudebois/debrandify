<?php 

function replaceableString($section, $input, $string) {
    $options = get_option('dwpify_' . $section);

    if (!empty($options[$input])) { // if checked
        if ($options[$string]) return $options[$string]; // return value if exists
    } else { return false; } // false if unchecked
}