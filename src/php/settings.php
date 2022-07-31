<?php class dwpifyOptions {

	public function printCheckbox($key, $title, $text = false) {
		$isChecked = checked('yes', getOption('dwpify_' . $key), false); ?>

		<tr>
			<th scope="row">
				<?php echo $title;
				 if ($key === 'prioritise') {
					echo "<p class='desc'>" . __('Your site uses WordPress Multisite, which means DeWordPressify options are set up ', 'dewordpressify') . "<a href='" . network_admin_url('settings.php?page=dewordpressify') . "'>" . __('on the network level', 'dewordpressify') . "</a>" . __('. You can prioritise this specific site\'s settings by toggling this option.', 'dewordpressify') . "</p>";
				 }
				?>
				
			</th>
			
			<td>
				<label class='switch'>
					<!-- hidden checkbox to return no when unchecked -->
					<input name="dwpify_<?php echo $key; ?>" <?php echo $isChecked ?> type="hidden" value="no">
					<input id="<?php echo $key ?>" name="dwpify_<?php echo $key; ?>" <?php echo $isChecked ?>type="checkbox" value="yes">
					<span class='slider round span'></span>
				</label>

				<?php if ($text) { 
					$stringKey = $key . '_string';
					$value = getOption('dwpify_' . $stringKey);
					$placeholder = __($text, 'dewordpressify');

					echo "<input type='text' id='${stringKey}' class='greyedOut' name='dwpify_${stringKey}' value='${value}' placeholder='${placeholder}' />";
				} ?>
			</td>
		</tr>
	<?php }

	public function printTextField($key, $title, $placeholder) { 
		$value = getOption('dwpify_' . $key) ? getOption('dwpify_' . $key) : ''; ?>
		<tr>
			<th scope="row"><?php echo $title; ?></th>
			<td><?php echo "<input type='text' id='${key}' name='dwpify_${key}' value='${value}' placeholder='${placeholder}' />"; 
			if ($key == 'email_username') echo ' @' . $_SERVER['SERVER_NAME'] ?></td>
		</tr>
	<?php }

	public function settings_dom() {
		$settingsUrl = nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1>
				<?php if (nw()) { _e('DeWordPressify multisite settings', 'dewordpressify'); }
				else { _e('DeWordPressify Settings', 'dewordpressify'); } ?>
			</h1>

			<?php if (nw()) {
				echo "<span>" . __('These will impact your whole network of sites. If you wish to set things up specifically for a site, head to the DeWordPressify settings of its dashboard.', 'dewordpressify') . "</span>";
			} ?>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo tabsUrl(); ?>" class="nav-tab <?php if (getCurrentTab() == 'general') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('General', 'dewordpressify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), tabsUrl())); ?>" class="nav-tab <?php if (getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'dewordpressify') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), tabsUrl())); ?>" class="nav-tab <?php if (getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'dewordpressify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'bonus'), tabsUrl())); ?>" class="nav-tab <?php if (getCurrentTab() == 'bonus') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Bonus features!', 'dewordpressify') ?> 🎊
				</a>
			</h2>

			<form method="post" action="<?php echo nw() ? 'edit' : 'admin-post' ?>.php?action=dwifyAction&tab=<?php echo getCurrentTab()?>">
				<?php wp_nonce_field('dwpify-validate'); ?>

				<table class="form-table">
					<?php 
						if (is_multisite() && !nw()) {
							$this->printCheckbox('prioritise', __('Prioritise these settings', 'dewordpressify'));
						} ?>

						<?php switch(getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', __('WordPress admin bar logo', 'dewordpressify'));
								$this->printCheckbox('thank_you', __('Thank you sentence in admin footer', 'dewordpressify'), __('Your own text', 'dewordpressify'));
								$this->printCheckbox('footer_version', __('WordPress version in admin footer', 'dewordpressify'), __('Your own text', 'dewordpressify'));

								$options = getOption('dwpify_login_logo') ? getOption('dwpify_login_logo') : 'wp_logo' ?>
								<tr><th scope="row"><?php _e('Login logo image', 'dewordpressify'); ?></th>
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
								$this->printCheckbox('dashboard_news', __('"News and events" widget on dashboard', 'dewordpressify'));

								if (is_plugin_active('elementor/elementor.php')) {
									$this->printCheckbox('elementor_overview', __('"Elementor overview" widget on dashboard', 'dewordpressify'));
								}

								$this->printCheckbox('smileys', __('Integrated smileys', 'dewordpressify'));
								$this->printCheckbox('rss', __('Integrated RSS feed', 'dewordpressify'));
								$this->printCheckbox('comments', __('Comments', 'dewordpressify'));
								break;
							case 'email':
								$this->printTextField('email_from', __('"From" text of emails sent by your site', 'dewordpressify'), __('Your site\'s name', 'dewordpressify'));
								$this->printTextField('email_username', __('Username of the email adress that sends from your site', 'dewordpressify'), __('First part of email', 'dewordpressify'));
								break;
							case 'advanced':
								$this->printCheckbox('css', __('Global inline styles', 'dewordpressify'));
								$this->printCheckbox('head', __('Unnecessary code in head tag', 'dewordpressify'));
                                $this->printCheckbox('wp_embed', __('Embeds', 'dewordpressify'));
                                $this->printCheckbox('block_library', __('Block library', 'dewordpressify'));
                                $this->printCheckbox('wp_themes', __('Automatically download new WordPress themes', 'dewordpressify'));
								break;
							case 'bonus':
								$this->printCheckbox('svg', __('SVG upload', 'dewordpressify'));
								$this->printCheckbox('centerLogin', __('Center login form vertically', 'dewordpressify'));
								$this->printCheckbox('restAPI', __('REST API', 'dewordpressify'));
								$this->printCheckbox('jquery', __('jQuery (if possible)', 'dewordpressify'));
								break;
						}
					?>
				</table>
				<?php submit_button(); ?>
			</form>

			<br><hr><br><br>

			<a href="https://github.com/morceaudebois/DeWordPressify">
				<img src="<?php echo plugin_dir_url(__DIR__) . 'images/dewordpressify.png' ?>" alt="dewordpressify banner" id="dwpify_banner">
			</a>
		
			<p><?php _e('Made in France with ❤️ by ', 'dewordpressify') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>
		
			<p><?php _e('If you like DeWordPressify, please consider ', 'dewordpressify') ?> <a href="https://wordpress.org/plugins/dewordpressify/#reviews"><?php _e('giving it a review', 'dewordpressify') ?></a> <?php _e('or', 'dewordpressify') ?> <a href="https://ko-fi.com/tahoe"><?php _e('donating', 'dewordpressify') ?></a>. <br>
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
				updateOption($key, sanitize_text_field($value));
            }
        }

		wp_redirect(add_query_arg(array('page' => 'dewordpressify', 'updated' => true),
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
		add_action('admin_post_dwifyAction', function() { $this->save(); });

		add_action('network_admin_notices', function() {
			if (isset($_GET['page']) && $_GET['page'] == 'dewordpressify' && isset($_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		});
	}
}

add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_dewordpressify') return;

    $handle = 'dewordpressify';
    wp_register_script($handle, plugin_dir_url(__DIR__) . 'js/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__DIR__) . 'css/style.css');
    wp_enqueue_style($handle);
});

if (is_admin()) $settings_page = new dwpifyOptions();