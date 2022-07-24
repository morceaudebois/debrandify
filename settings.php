<?php class dwpifyOptions {

	public function tabsUrl() {
		$tabsUrl = '.php?page=dewordpressify';
		if (nw()) { return network_admin_url('settings' . $tabsUrl);
		} else { return admin_url('admin' . $tabsUrl); }
	} 

	public function getCurrentTab() {
		return !isset($_GET['action']) ? 'general' : $_GET['action'];
	}

	public function printCheckbox($key, $title, $text = false) {
		$isChecked = checked('yes', get_site_option('dwpify_' . $key), false); ?>

		<tr>
			<th scope="row"><?php echo __($title, 'dewordpressify'); ?></th>
			<td>
				<label class='switch'>
					<!-- hidden checkbox to return no when unchecked -->
					<input name="dwpify_<?php echo $key; ?>" <?php echo $isChecked ?> type="hidden" value="no">
					<input id="<?php echo $key ?>" name="dwpify_<?php echo $key; ?>" <?php echo $isChecked ?>type="checkbox" value="yes">
					<span class='slider round span'></span>
				</label>

				<?php if ($text) { 
					$stringKey = $key . '_string';
					$value = get_site_option('dwpify_' . $stringKey);
					$placeholder = __($text, 'dewordpressify');

					echo "<input type='text' id='${stringKey}' class='greyedOut' name='dwpify_${stringKey}' value='${value}' placeholder='${placeholder}' />";
				} ?>
			</td>
		</tr>
	<?php }

	public function printTextField($key, $title, $placeholder) { 
		$value = get_site_option('dwpify_' . $key) ? get_site_option('dwpify_' . $key) : '';
		$placeholder = __($placeholder, 'dewordpressify') ?>
		<tr>
			<th scope="row"><?php echo __($title, 'dewordpressify'); ?></th>
			<td><?php echo "<input type='text' id='${key}' name='dwpify_${key}' value='${value}' placeholder='${placeholder}' />" ?></td>
		</tr>
	<?php }

	public function settings_dom() {
		$settingsUrl = nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1><?php _e('DeWordPressify Settings', 'dewordpressify') ?></h1>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo $this->tabsUrl(); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'general') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('General', 'dewordpressify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), $this->tabsUrl())); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'dewordpressify') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), $this->tabsUrl())); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'dewordpressify') ?>
				</a>
			</h2>

			<form method="post" action="<?php echo nw() ? 'edit' : 'admin-post' ?>.php?action=dwifyAction&tab=<?php echo $this->getCurrentTab()?>">
				<?php wp_nonce_field('dwpify-validate'); ?>

				<table class="form-table">
					<?php 
						switch($this->getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', 'WordPress admin bar logo');
								$this->printCheckbox('thank_you', 'Thank you sentence in admin footer', 'Your own string');
								$this->printCheckbox('footer_version', 'WordPress version in admin footer', 'Your own string');

								$options = get_site_option('dwpify_login_logo') ? get_site_option('dwpify_login_logo') : 'wp_logo' ?>
								<tr><th scope="row"><?php echo __('Login logo image', 'dewordpressify'); ?></th>
									<td>
										<select name="dwpify_login_logo">
											<option value="wp_logo" <?php selected($options, "wp_logo"); ?>>
												<?php _e('Default WordPress logo', 'dewordpressify') ?>
											</option>
								
											<option value="site_logo" <?php selected($options, "site_logo"); ?>>
												<?php _e('Site logo (if there is one)', 'dewordpressify') ?>
											</option>
								
											<option value="site_title" <?php selected($options, "site_title"); ?>>
												<?php _e('Site title', 'dewordpressify') ?>
											</option>
								
											<option value="none" <?php selected($options, "none"); ?>>
												<?php _e('Hide', 'dewordpressify') ?>
											</option>
										</select>
									</td>
								</tr>
                                <?php 
								$this->printCheckbox('dashboard_news', '"News and events" widget on dashboard');
								$this->printCheckbox('smileys', 'Integrated smileys');
								$this->printCheckbox('rss', 'Integrated RSS feed');
								$this->printCheckbox('comments', 'Comments');
								break;
							case 'email':
								$this->printTextField('email_from', '"From" text of emails sent by your site', 'Your site\'s name');
								$this->printTextField('email_username', 'Username of the email adress that sends from your site', 'First part of email');
								break;
							case 'advanced':
								$this->printCheckbox('css', 'Global inline styles');
								$this->printCheckbox('head', 'Unnecessary code in head tag');
								break;
						}
					?>
				</table>
				<?php submit_button(); ?>
			</form>

			<br><hr><br><br>

			<img src="<?php echo plugin_dir_url(__FILE__) . 'assets/dewordpressify.png' ?>" alt="dewordpressify banner" id="dwpify_banner">
		
			<p><?php _e('Made in France with ❤️ by ', 'dewordpressify') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>
		
			<p><?php _e('If you like DeWordPressify, please consider ', 'dewordpressify') ?> <a href="#"><?php _e('giving it a review', 'dewordpressify') ?></a> <?php _e('or', 'dewordpressify') ?> <a href="#"><?php _e('donating', 'dewordpressify') ?></a>. <br>
			<?php _e('This is what motivates me to keep it updated and create new projects as an indie developer 😊', 'dewordpressify') ?></p> 
		</div>
	<?php }

	public function save() {
		check_admin_referer('dwpify-validate'); // Nonce security check

		function rediUrl() {
			if (nw()) { return network_admin_url('settings.php?action=' . $_GET['tab']);
			} else { return admin_url('options-general.php?page=dewordpressify&action=' . $_GET['tab']); }
		}

        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 7) === "dwpify_") { // checks if starts with dwpify_
                update_site_option($key, sanitize_text_field($value));
            }
        }

		wp_redirect(add_query_arg(array( 'page' => 'dewordpressify', 'updated' => true),
			rediUrl()
		)); exit;
	}

	// init stuff
	public function __construct() {
		// regular site
		add_action("admin_menu", function() {
			add_options_page(
				'DeWordPressify',
				'DeWordPressify',
				'manage_options',
				'dewordpressify',
				array($this, 'settings_dom')
			);
		});

		// network
		add_action("network_admin_menu", function() {
			add_submenu_page(
				'settings.php', // Parent element
				'DeWordPressify', // Text in browser title bar
				'DeWordPressify', // Text to be displayed in the menu.
				'manage_options', // Capability
				'dewordpressify', // Page slug, will be displayed in URL
				array($this, 'settings_dom') // Callback function which displays the page
			);
		});

		add_action('network_admin_edit_dwifyAction', function() { $this->save(); });
		add_action( 'admin_post_dwifyAction', function() { $this->save(); });

		add_action( 'network_admin_notices', function() {
			if(isset($_GET['page']) && $_GET['page'] == 'dewordpressify' && isset( $_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		});
	}
}

add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_dewordpressify') return;

    $handle = 'dewordpressify';
    wp_register_script($handle, plugin_dir_url(__FILE__) . '/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__FILE__) . '/style.css');
    wp_enqueue_style($handle);
});

if (is_admin()) $settings_page = new dwpifyOptions();