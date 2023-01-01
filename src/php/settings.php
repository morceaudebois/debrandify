<?php if (!defined('ABSPATH')) { exit; }

class dwpifyOptions {

	public function printCheckbox($key, $title, $text = false) {
		$isChecked = checked('yes', wpdbrd_getOption('wpdbrd_' . $key), false); ?>

		<tr>
			<th scope="row">
				<?php echo $title;
				 if ($key === 'prioritise') {
					echo "<p class='desc'>" . __('Your site uses WordPress Multisite, which means WP Debrand options are set up ', 'wp-debrand') . "<a href='" . network_admin_url('settings.php?page=wp-debrand') . "'>" . __('on the network level', 'wp-debrand') . "</a>" . __('. You can prioritise this specific site\'s settings by toggling this option.', 'wp-debrand') . "</p>";
				 }
				?>
				
			</th>
			
			<td>
				<label class='switch'>
					<!-- hidden checkbox to return no when unchecked -->
					<input name="wpdbrd_<?php echo $key; ?>" <?php echo $isChecked ?> type="hidden" value="no">
					<input id="<?php echo $key ?>" name="wpdbrd_<?php echo $key; ?>" <?php echo $isChecked ?>type="checkbox" value="yes">
					<span class='slider round span'></span>
				</label>

				<?php if ($text) { 
					$stringKey = $key . '_string';
					$value = wpdbrd_getOption('wpdbrd_' . $stringKey);
					$placeholder = __($text, 'wp-debrand');

					echo "<input type='text' id='${stringKey}' class='greyedOut' name='wpdbrd_${stringKey}' value='${value}' placeholder='${placeholder}' />";
				} ?>
			</td>
		</tr>
	<?php }

	public function printTextField($key, $title, $placeholder) { 
		$value = wpdbrd_getOption('wpdbrd_' . $key) ? wpdbrd_getOption('wpdbrd_' . $key) : ''; ?>
		<tr>
			<th scope="row"><?php echo $title; ?></th>
			<td><?php echo "<input type='text' id='${key}' name='wpdbrd_${key}' value='${value}' placeholder='${placeholder}' />"; 
			if ($key == 'email_username') echo ' @' . $_SERVER['SERVER_NAME'] ?></td>
		</tr>
	<?php }

	public function settings_dom() {
		$settingsUrl = nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1>
				<?php if (nw()) { _e('WP Debrand multisite settings', 'wp-debrand'); }
				else { _e('WP Debrand Settings', 'wp-debrand'); } ?>
			</h1>

			<?php if (nw()) {
				echo "<span>" . __('These will impact your whole network of sites. If you wish to set things up specifically for a site, head to the WP Debrand settings of its dashboard.', 'wp-debrand') . "</span>";
			} ?>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo wpdbrd_tabsUrl(); ?>" class="nav-tab <?php if (wpdbrd_getCurrentTab() == 'general') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('General', 'wp-debrand') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), wpdbrd_tabsUrl())); ?>" class="nav-tab <?php if (wpdbrd_getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'wp-debrand') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), wpdbrd_tabsUrl())); ?>" class="nav-tab <?php if (wpdbrd_getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'wp-debrand') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'bonus'), wpdbrd_tabsUrl())); ?>" class="nav-tab <?php if (wpdbrd_getCurrentTab() == 'bonus') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Bonus features!', 'wp-debrand') ?> 🎊
				</a>
			</h2>

			<form method="post" action="<?php echo nw() ? 'edit' : 'admin-post' ?>.php?action=dwifyAction&tab=<?php echo wpdbrd_getCurrentTab()?>">
				<?php wp_nonce_field('dwpify-validate'); ?>

				<table class="form-table">
					<?php 
						if (is_multisite() && !nw()) {
							$this->printCheckbox('prioritise', __('Prioritise these settings', 'wp-debrand'));
						} ?>

						<?php switch(wpdbrd_getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', __('WordPress admin bar logo', 'wp-debrand'));
								$this->printCheckbox('thank_you', __('Thank you sentence in admin footer', 'wp-debrand'), __('Your own text', 'wp-debrand'));
								$this->printCheckbox('footer_version', __('WordPress version in admin footer', 'wp-debrand'), __('Your own text', 'wp-debrand'));

								$options = wpdbrd_getOption('wpdbrd_login_logo') ? wpdbrd_getOption('wpdbrd_login_logo') : 'wp_logo' ?>
								<tr><th scope="row"><?php _e('Login logo image', 'wp-debrand'); ?></th>
									<td>
										<select name="wpdbrd_login_logo">
											<option value="wp_logo" <?php selected($options, "wp_logo"); ?>>
												<?php _e('Default WordPress logo', 'wp-debrand') ?>
											</option>
								
											<option value="site_logo" <?php selected($options, "site_logo"); ?>>
												<?php _e('Site logo (if there is one)', 'wp-debrand') ?>
											</option>
								
											<option value="site_title" <?php selected($options, "site_title"); ?>>
												<?php _e('Site title', 'wp-debrand') ?>
											</option>
								
											<option value="none" <?php selected($options, "none"); ?>>
												<?php _e('Hide', 'wp-debrand') ?>
											</option>
										</select>
									</td>
								</tr>

                                <?php 
								$this->printCheckbox('wordpress-tab-suffix', __('"— WordPress" suffix in tab titles of dashboard pages', 'wp-debrand'));

								$this->printCheckbox('dashboard_news', __('"News and events" widget on dashboard', 'wp-debrand'));

								if (is_plugin_active('elementor/elementor.php')) {
									$this->printCheckbox('elementor_overview', __('"Elementor overview" widget on dashboard', 'wp-debrand'));
								}

								$this->printCheckbox('smileys', __('Integrated smileys', 'wp-debrand'));
								$this->printCheckbox('rss', __('Integrated RSS feed', 'wp-debrand'));
								$this->printCheckbox('comments', __('Comments', 'wp-debrand'));
								break;
							case 'email':
								$this->printTextField('email_from', __('"From" text of emails sent by your site', 'wp-debrand'), __('Your site\'s name', 'wp-debrand'));
								$this->printTextField('email_username', __('Username of the email adress that sends from your site', 'wp-debrand'), __('First part of email', 'wp-debrand'));
								break;
							case 'advanced':
								$this->printCheckbox('css', __('Global inline styles', 'wp-debrand'));
								$this->printCheckbox('head', __('Unnecessary code in head tag', 'wp-debrand'));
                                $this->printCheckbox('wp_embed', __('Embeds', 'wp-debrand'));
                                $this->printCheckbox('block_library', __('Block library', 'wp-debrand'));
                                $this->printCheckbox('wp_themes', __('Automatically download new WordPress themes', 'wp-debrand'));
								break;
							case 'bonus':
								$this->printCheckbox('svg', __('SVG upload', 'wp-debrand'));
								$this->printCheckbox('centerLogin', __('Center login form vertically', 'wp-debrand'));
								$this->printCheckbox('restAPI', __('REST API', 'wp-debrand'));
								$this->printCheckbox('jquery', __('jQuery (if possible)', 'wp-debrand'));
								break;
						}
					?>
				</table>
				<?php submit_button(); ?>
			</form>

			<br><hr><br><br>

			<a href="https://github.com/morceaudebois/wp-debrand">
				<img src="<?php echo plugin_dir_url(__DIR__) . 'images/wp-debrand.png' ?>" alt="wp-debrand banner" id="wpdbrd_banner">
			</a>
		
			<p><?php _e('Made in France with ❤️ by ', 'wp-debrand') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>
		
			<p><?php _e('If you like WP Debrand, please consider ', 'wp-debrand') ?> <a href="https://wordpress.org/plugins/wp-debrand/#reviews"><?php _e('giving it a review', 'wp-debrand') ?></a> <?php _e('or', 'wp-debrand') ?> <a href="https://ko-fi.com/tahoe"><?php _e('donating', 'wp-debrand') ?></a>. <br>
			<?php _e('This is what motivates me to keep it updated and create new projects as an indie developer 😊', 'wp-debrand') ?></p> 
		</div>
	<?php }

	public function save() {
		check_admin_referer('dwpify-validate'); // Nonce security check

		function rediUrl() {
			if (nw()) { return network_admin_url('settings.php?action=' . $_GET['tab']);
			} else { return admin_url('options-general.php?page=wp-debrand&action=' . $_GET['tab']); }
		}

        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 7) === "wpdbrd_") { // checks if starts with wpdbrd_
				wpdbrd_updateOption($key, sanitize_text_field($value));
            }
        }

		wp_redirect(add_query_arg(array('page' => 'wp-debrand', 'updated' => true),
			rediUrl()
		)); exit;
	}

	// init stuff
	public function __construct() {
		// regular site
		add_action("admin_menu", function() {
			add_options_page(
				'WP Debrand',
				'WP Debrand',
				'manage_options',
				'wp-debrand',
				array($this, 'settings_dom')
			);
		});

		// network
		add_action("network_admin_menu", function() {
			add_submenu_page(
				'settings.php', // Parent element
				'WP Debrand', // Text in browser title bar
				'WP Debrand', // Text to be displayed in the menu.
				'manage_options', // Capability
				'wp-debrand', // Page slug, will be displayed in URL
				array($this, 'settings_dom') // Callback function which displays the page
			);
		});

		add_action('network_admin_edit_dwifyAction', function() { $this->save(); });
		add_action('admin_post_dwifyAction', function() { $this->save(); });

		add_action('network_admin_notices', function() {
			if (isset($_GET['page']) && $_GET['page'] == 'wp-debrand' && isset($_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		});
	}
}

add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_wp-debrand') return;

    $handle = 'wp-debrand';
    wp_register_script($handle, plugin_dir_url(__DIR__) . 'js/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__DIR__) . 'css/style.css');
    wp_enqueue_style($handle);
});

if (is_admin()) $settings_page = new dwpifyOptions();