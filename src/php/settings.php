<?php if (!defined('ABSPATH')) { exit; }

class dbrdifyOptions {

	// for each checkbox
	public function printCheckbox($key, $title, $text = false) {
		$isChecked = checked('yes', dbrdify_getOption('dbrdify_' . $key), false); ?>

		<tr>
			<th scope="row">
				<?php echo esc_html($title);
					if ($key === 'prioritise') {
						echo "<p class='desc'>" . esc_html(__('Your site uses WordPress Multisite, which means Debrandify options are set up ', 'debrandify')) . "<a href='" . esc_url(network_admin_url('settings.php?page=debrandify')) . "'>" . esc_html(__('on the network level', 'debrandify')) . "</a>" . esc_html(__('. You can prioritise this specific site\'s settings by toggling this option.', 'debrandify')) . "</p>";
					}
				?>
			</th>
			
			<td>
				<label class='switch'>
					<!-- hidden checkbox to return no when unchecked -->
					<input 
						name="dbrdify_<?php echo esc_html($key) ?>"
						<?php echo esc_attr($isChecked) ?>
						type="hidden" value="no"
					>
					<input id="<?php echo esc_attr($key) ?>" name="dbrdify_<?php echo esc_attr($key) ?>" <?php echo esc_attr($isChecked) ?> type="checkbox" value="yes">
					<span class='slider round span'></span>
				</label>

				<?php if ($text) { 
					$stringKey = $key . '_string';
					$value = esc_attr(dbrdify_getOption('dbrdify_' . $stringKey));
					$placeholder = esc_attr($text);

					echo "<input type='text' id='" . esc_attr($stringKey) . "' class='greyedOut' name='dbrdify_" . esc_attr($stringKey) . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($placeholder) . "' />";

				} ?>
			</td>
		</tr>
	<?php }

	public function printTextField($key, $title, $placeholder) { 
		$value = dbrdify_getOption('dbrdify_' . $key) ? dbrdify_getOption('dbrdify_' . $key) : ''; ?>

		<tr>
			<th scope="row"><?php echo esc_html($title) ?></th>
			<td>
				<input type="text" id="<?php echo esc_attr($key) ?>" name="dbrdify_<?php echo esc_attr($key) ?>" value="<?php echo esc_attr($value) ?>" placeholder="<?php echo esc_attr($placeholder) ?>" />
				<?php
					if (isset($key) && is_string($key) && $key === 'email_username') {
						echo ' @' . esc_html($_SERVER['SERVER_NAME']);
					}
				?>
			</td>
		</tr>
	<?php }

	public function settings_dom() {
		$settingsUrl = dbrdify_nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1>
				<?php if (dbrdify_nw()) { _e('Debrandify multisite settings', 'debrandify'); }
				else { _e('Debrandify Settings', 'debrandify'); } ?>
			</h1>

			<?php if (dbrdify_nw()) {
				echo "<span>" . esc_html(__('These will impact your whole network of sites. If you wish to set things up specifically for a site, head to the Debrandify settings of its dashboard.', 'debrandify')) . "</span>";
			} ?>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url(dbrdify_tabsUrl()); ?>" class="nav-tab <?php if (dbrdify_getCurrentTab() == 'general') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('General', 'debrandify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), dbrdify_tabsUrl())); ?>" class="nav-tab <?php if (dbrdify_getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'debrandify') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), dbrdify_tabsUrl())); ?>" class="nav-tab <?php if (dbrdify_getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'debrandify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'bonus'), dbrdify_tabsUrl())); ?>" class="nav-tab <?php if (dbrdify_getCurrentTab() == 'bonus') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Bonus features!', 'debrandify') ?> 🎊
				</a>
			</h2>

			<form method="post" action="<?php echo dbrdify_nw() ? 'edit' : 'admin-post' ?>.php?action=dwifyAction&tab=<?php echo esc_attr(dbrdify_getCurrentTab())?>">
				<?php wp_nonce_field('dbrdify-validate'); ?>

				<table class="form-table">
					<?php 
						if (is_multisite() && !dbrdify_nw()) {
							$this->printCheckbox('prioritise', __('Prioritise these settings', 'debrandify'));
						} ?>

						<?php switch(dbrdify_getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', __('WordPress admin bar logo', 'debrandify'));
								$this->printCheckbox('thank_you', __('Thank you sentence in admin footer', 'debrandify'), __('Your own text', 'debrandify'));
								$this->printCheckbox('footer_version', __('WordPress version in admin footer', 'debrandify'), __('Your own text', 'debrandify'));

								$options = dbrdify_getOption('dbrdify_login_logo') ? dbrdify_getOption('dbrdify_login_logo') : 'wp_logo' ?>
								<tr><th scope="row"><?php _e('Login logo image', 'debrandify'); ?></th>
									<td>
										<select name="dbrdify_login_logo">
											<option value="wp_logo" <?php selected($options, "wp_logo"); ?>>
												<?php _e('Default WordPress logo', 'debrandify') ?>
											</option>
								
											<option value="site_logo" <?php selected($options, "site_logo"); ?>>
												<?php _e('Site logo (if there is one)', 'debrandify') ?>
											</option>
								
											<option value="site_title" <?php selected($options, "site_title"); ?>>
												<?php _e('Site title', 'debrandify') ?>
											</option>
								
											<option value="none" <?php selected($options, "none"); ?>>
												<?php _e('Hide', 'debrandify') ?>
											</option>
										</select>
									</td>
								</tr>

                                <?php 
								$this->printCheckbox('wordpress-tab-suffix', __('"— WordPress" suffix in tab titles of dashboard pages', 'debrandify'));

								$this->printCheckbox('dashboard_news', __('"News and events" widget on dashboard', 'debrandify'));

								if (is_plugin_active('elementor/elementor.php')) {
									$this->printCheckbox('elementor_overview', __('"Elementor overview" widget on dashboard', 'debrandify'));
								}

								$this->printCheckbox('smileys', __('Integrated smileys', 'debrandify'));
								$this->printCheckbox('rss', __('Integrated RSS feed', 'debrandify'));
								$this->printCheckbox('comments', __('Comments', 'debrandify'));
								break;
							case 'email':
								$this->printTextField('email_from', __('"From" text of emails sent by your site', 'debrandify'), __('Your site\'s name', 'debrandify'));
								$this->printTextField('email_username', __('Username of the email adress that sends from your site', 'debrandify'), __('First part of email', 'debrandify'));
								break;
							case 'advanced':
								$this->printCheckbox('css', __('Global inline styles', 'debrandify'));
								$this->printCheckbox('head', __('Unnecessary code in head tag', 'debrandify'));
                                $this->printCheckbox('wp_embed', __('Embeds', 'debrandify'));
                                $this->printCheckbox('block_library', __('Block library', 'debrandify'));
								break;
							case 'bonus':
								$this->printCheckbox('svg', __('SVG upload', 'debrandify'));
								$this->printCheckbox('centerLogin', __('Center login form vertically', 'debrandify'));
								$this->printCheckbox('restAPI', __('REST API', 'debrandify'));
								$this->printCheckbox('jquery', __('jQuery (if possible)', 'debrandify'));
								break;
						}
					?>
				</table>
				<?php submit_button(); ?>
			</form>

			<br><hr><br><br>

			<a href="https://github.com/morceaudebois/debrandify">
				<img src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/debrandify.png') ?>" alt="debrandify banner" id="dbrdify_banner">
			</a>
		
			<p><?php _e('Made in France with ❤️ by ', 'debrandify') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>
		
			<p><?php _e('If you like Debrandify, please consider ', 'debrandify') ?> <a href="https://wordpress.org/plugins/debrandify/#reviews"><?php _e('giving it a review', 'debrandify') ?></a> <?php _e('or', 'debrandify') ?> <a href="https://ko-fi.com/tahoe"><?php _e('donating', 'debrandify') ?></a>. <br>
			<?php _e('This is what motivates me to keep it updated and create new projects as an indie developer 😊', 'debrandify') ?></p> 

			<p>
				<?php _e('As this is my first WordPress plugin, it might have some bugs! 🐞', 'debrandify'); ?>
				<br>
				<?php _e('If you find some, please ', 'debrandify') ?>
				<a href="https://github.com/morceaudebois/debrandify/issues"><?php _e('report them here', 'debrandify')?></a> 
				<?php _e('so that I can fix them quickly.', 'debrandify') ?>
			</p>
		</div>
	<?php }


	public function save() {
		
		check_admin_referer('dbrdify-validate'); // Nonce security check

		function dbrdify_rediUrl() {
			$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';

			// validation
			if (!in_array($tab, array('general', 'email', 'advanced', 'bonus'))) {
				$tab = 'general';
			}

			if (dbrdify_nw()) { return network_admin_url('settings.php?action=' . $tab);
			} else { return admin_url('options-general.php?page=debrandify&action=' . $tab); }
		}

        foreach ($_POST as $key => $value) {
			$key = sanitize_text_field($key);
            if (substr($key, 0, 8) === "dbrdify_") { // checks if starts with dbrdify_
				dbrdify_updateOption($key, sanitize_text_field($value));
            }
        }

		wp_redirect(add_query_arg(array('page' => 'debrandify', 'updated' => true),
			dbrdify_rediUrl()
		)); exit;
	}

	// init stuff
	public function __construct() {
		// regular site
		add_action("admin_menu", function() {
			add_options_page(
				'Debrandify',
				'Debrandify',
				'manage_options',
				'debrandify',
				array($this, 'settings_dom')
			);
		});

		// network
		add_action("network_admin_menu", function() {
			add_submenu_page(
				'settings.php', // Parent element
				'Debrandify', // Text in browser title bar
				'Debrandify', // Text to be displayed in the menu.
				'manage_options', // Capability
				'debrandify', // Page slug, will be displayed in URL
				array($this, 'settings_dom') // Callback function which displays the page
			);
		});

		add_action('network_admin_edit_dwifyAction', function() { $this->save(); });
		add_action('admin_post_dwifyAction', function() { $this->save(); });

		add_action('network_admin_notices', function() {
			if (isset($_GET['page']) && $_GET['page'] == 'debrandify' && isset($_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Settings updated.', 'debrandify') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'debrandify') . '</span></button></div>';
			}
		});
	}
}

add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_debrandify') return;

    $handle = 'debrandify';
    wp_register_script($handle, plugin_dir_url(__DIR__) . 'js/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__DIR__) . 'css/style.css');
    wp_enqueue_style($handle);
});

if (is_admin()) $settings_page = new dbrdifyOptions();
