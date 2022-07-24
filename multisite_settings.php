<?php
class dwpifyOptionsBis {

	public function tabsUrl() {
		$tabsUrl = '.php?page=dewordpressify';
		if (nw()) { return network_admin_url('settings' . $tabsUrl);
		} else { return admin_url('admin' . $tabsUrl); }
	} 

	public function getCurrentTab() {
		return !isset($_GET['action']) ? 'general' : $_GET['action'];
	}

	public function printCheckbox($key, $title) {
		$isChecked = checked('yes', get_site_option('dwpify_' . $key), false); ?>

		<tr>
			<th scope="row"><?php echo __($title, 'dewordpressify'); ?></th>
			<td><label><input name="dwpify_<?php echo $key; ?>" <?php echo $isChecked ?>type="checkbox" value="yes"></label></td>
		</tr>
	<?php }

	public function misha_settings_page_1() {
		$settingsUrl = nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1><?php _e('DeWordPressify Settings', 'dewordpressify') ?></h1>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo $this->tabsUrl(); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'general') echo 'nav-tab-active'; ?>"
				>
					<?php esc_html_e('General', 'dewordpressify') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), $this->tabsUrl())); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'dewordpressify') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), $this->tabsUrl())); ?>" class="nav-tab <?php if ($this->getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'dewordpressify') ?>
				</a>
			</h2>

			<form method="post" action="<?php echo nw() ? 'edit' : 'admin-post' ?>.php?action=mishaaction&tab=<?php echo $this->getCurrentTab()?>">
				<?php wp_nonce_field( 'misha-validate' ); ?>

				<table class="form-table">
					<?php 
						switch($this->getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', 'WordPress admin bar logo');
								break;
							case 'email':
								$this->printCheckbox('email_username', 'Username of the email adress that sends from your site');
								break;
							case 'advanced':
								break;
						}
					?>
				</table>

				<?php submit_button(); ?>

			</form>

			<br>
			<hr><br><br>

			<img src="<?php echo plugin_dir_url(__FILE__) . 'assets/dewordpressify.png' ?>" alt="dewordpressify banner" id="dwpify_banner">
		
			<p><?php _e('Made in France with ❤️ by ', 'dewordpressify') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>
		
			<p><?php _e('If you like DeWordPressify, please consider ', 'dewordpressify') ?> <a href="#"><?php _e('giving it a review', 'dewordpressify') ?></a> <?php _e('or', 'dewordpressify') ?> <a href="#"><?php _e('donating', 'dewordpressify') ?></a>. <br>
			<?php _e('This is what motivates me to keep it updated and create new projects as an indie developer 😊', 'dewordpressify') ?></p> 
		</div>

	<?php }



	public function save() {
		check_admin_referer('misha-validate'); // Nonce security check

		function rediUrl() {
			if (nw()) { return network_admin_url('settings.php');
			} else { return admin_url('options-general.php?page=dewordpressify&action=' . $_GET['tab']); }
		}

		error_log(print_r($_POST, true));
		$allInputs = array('adminbar_logo', 'email_username');

		foreach ($allInputs as $input) {
			$key = 'dwpify_' . $input;
			$toUpdate = isset($_POST[$key]) ? $_POST[$key] : false;

			update_site_option(
				$key, sanitize_text_field($toUpdate ? $toUpdate : '')
			);
		}

		wp_redirect(add_query_arg(array( 'page' => 'dewordpressify', 'updated' => true),
			rediUrl()
		));

		exit;
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
				array($this, 'misha_settings_page_1')
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
				array($this, 'misha_settings_page_1') // Callback function which displays the page
			);
		});

		add_action('network_admin_edit_mishaaction', function() { $this->save(); });
		add_action( 'admin_post_mishaaction', function() { $this->save(); });

		add_action( 'network_admin_notices', function() {
			if(isset($_GET['page']) && $_GET['page'] == 'dewordpressify' && isset( $_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated. You\'re the best!</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		});
	}


}

if (is_admin()) $settings_page = new dwpifyOptionsBis();