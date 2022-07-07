<?php

// exit if uninstall constant is not defined
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$options = array(
	'banner',
	'dwpify_general',
	'dwpify_email',
	'dwpify_advanced',
);

// gets rid of all data
foreach ($options as $option) {
	if (get_option($option)) delete_option($option);
}