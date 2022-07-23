<?php



// regular site
add_action("admin_menu", function() {
	add_options_page(
		'DeWordPressify',
		'DeWordPressify',
		'manage_options',
		'dewordpressify',
		'misha_settings_page_1'
   );
});

// network
add_action("network_admin_menu", function() {
	add_submenu_page(
		'settings.php', // Parent element
		'Settings Page 1', // Text in browser title bar
		'Settings Page 1', // Text to be displayed in the menu.
		'manage_options', // Capability
		'dewordpressify', // Page slug, will be displayed in URL
		'misha_settings_page_1' // Callback function which displays the page
	);
});

function nw() { return is_network_admin(); }
function misha_settings_page_1() {

	

	$options_general = get_option('dwpify_general');
	$options_email = get_option('dwpify_email');
	$options_advanced = get_option('dwpify_advanced');

	$email_Screen = (isset($_GET['action']) && 'email' == $_GET['action']) ? true : false;
	$advanced_Screen = (isset($_GET['action']) && 'advanced' == $_GET['action']) ? true : false; 
	
	$settingsUrl = nw() ? 'edit' : 'admin-post';

	function tabsUrl() {
		$tabsUrl = '.php?page=dewordpressify';
		if (nw()) { return network_admin_url('settings' . $tabsUrl);
		} else { return admin_url('admin' . $tabsUrl); }
	}


	?>


	<div class="wrap">
		<h1><?php _e('DeWordPressify Settings', 'dewordpressify') ?></h1>
            
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo tabsUrl(); ?>" class="nav-tab<?php if (! isset($_GET['action']) || isset($_GET['action']) && 'email' != $_GET['action']  && 'advanced' != $_GET['action']) echo ' nav-tab-active'; ?>">
				<?php esc_html_e('General', 'dewordpressify') ?>
			</a>

			<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), tabsUrl())); ?>" class="nav-tab<?php if ($email_Screen) echo ' nav-tab-active'; ?>">
				<?php esc_html_e('Email', 'dewordpressify') ?>
			</a> 

			<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), tabsUrl())); ?>" class="nav-tab<?php if ($advanced_Screen) echo ' nav-tab-active'; ?>">
				<?php esc_html_e('Advanced', 'dewordpressify') ?>
			</a>        
		</h2>

	

		<form method="post" action="<?php echo nw() ? 'edit' : 'admin-post' ?>.php?action=mishaaction">
			<?php wp_nonce_field( 'misha-validate' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><label for="some_field">Some option</label></th>
					<td>
						<input name="some_field" class="regular-text" type="text" id="some_field" value="<?php echo esc_attr( get_site_option( 'some_field') ) ?>" />
						<p class="description">Field description can be added here.</p>
					</td>
				</tr>
			</table>

			<h2>Section 2</h2>
			<table class="form-table">
				<tr>
					<th scope="row">Some checkbox</th>
					<td><label><input name="some_checkbox" type="checkbox" value="yes" <?php echo checked('yes', get_site_option( 'some_checkbox'), false ) ?>> Yes, check this checkbox</label></td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form></div>

<?php }



function save() {
	check_admin_referer('misha-validate'); // Nonce security check

	update_site_option('some_field', $_POST['some_field']);
	update_site_option('some_checkbox', $_POST['some_checkbox']);

	function rediUrl() {
		if (nw()) { return network_admin_url('settings.php');
		} else { return admin_url('options-general.php?page=dewordpressify'); }
	}

	wp_redirect(add_query_arg(array(
		'page' => 'dewordpressify', 'updated' => true),
		rediUrl()
	));

	exit;
}

add_action('network_admin_edit_mishaaction', function() { save(); });
add_action( 'admin_post_mishaaction', function() { save(); });