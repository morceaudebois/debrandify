<?php

// exit if uninstall constant is not defined
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

include(plugin_dir_path(__FILE__) . 'functions.php');

$allOptions = getDefaultOptions();

// gets rid of all data
foreach ($allOptions as $key => $value) {
	if (get_option($key)) delete_option($key);
	if (get_site_option($key)) delete_site_option($key);
}